<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;
use App\Models\Denominacion;
use App\Models\Distrito;
use App\Models\Nula;
use App\Models\Realizacion;
use App\Models\TipoEspecie;
use App\Services\Inventario;
use App\Services\Tarifas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RealizacionController extends Controller
{
    //CONTROLADOR DE REALIZACIONES - gestiona historial, registro, eliminacion y endpoint AJAX de stock por distrito
    public function historial(Request $request)
    {
        $distritos  = Distrito::where('activo', true)->orderBy('codigo')->get();
        $tipos      = TipoEspecie::where('activo', true)->orderBy('nombre')->get();
        $distFiltro = $request->distrito_id;
        $tipoFiltro = $request->tipo_especie_id;

        $consulta = Realizacion::with('tipoEspecie', 'denominacion', 'distrito', 'usuario')
            ->when($distFiltro, fn($q) => $q->where('distrito_id', $distFiltro))
            ->when($tipoFiltro, fn($q) => $q->where('tipo_especie_id', $tipoFiltro));

        // El total cobrado es de todo el filtro, no solo de la pagina visible
        $totalCobrado  = (clone $consulta)->sum('monto_cobrado');
        $realizaciones = $consulta->orderByDesc('fecha')->orderByDesc('id')->paginate(25)->withQueryString();

        return view('frontend.admin.especies.realizaciones.historial',
            compact('realizaciones', 'totalCobrado', 'distritos', 'tipos', 'distFiltro', 'tipoFiltro'));
    }

    public function crear()
    {
        $distritos = Distrito::where('activo', true)->orderBy('codigo')->get();
        $tipos     = TipoEspecie::where('activo', true)->orderBy('nombre')->get();

        return view('frontend.admin.especies.realizaciones.crear',
            compact('distritos', 'tipos'));
    }

    public function store(Request $request)
    {
        $datos = $this->datos($request);

        DB::transaction(function () use ($datos) {
            Inventario::bloquear($datos['tipo_especie_id']);
            $this->validarRango($datos);

            $realizacion = Realizacion::create($datos + ['usuario_id' => auth()->id()]);
            Inventario::asegurar($this->pares($realizacion));
        });

        return redirect()->route('admin.especies.realizaciones.historial')
            ->with('success', "Realización registrada: {$datos['cantidad']} documentos por $" . number_format($datos['monto_cobrado'], 2) . '.');
    }

    public function editar(Realizacion $realizacion)
    {
        $distritos = Distrito::where('activo', true)->orderBy('codigo')->get();
        $tipos     = TipoEspecie::where('activo', true)->orderBy('nombre')->get();

        return view('frontend.admin.especies.realizaciones.editar',
            compact('realizacion', 'distritos', 'tipos'));
    }

    public function update(Request $request, Realizacion $realizacion)
    {
        $datos = $this->datos($request);

        DB::transaction(function () use ($datos, $realizacion) {
            Inventario::bloquear($realizacion->tipo_especie_id, $datos['tipo_especie_id']);
            $realizacion->refresh();

            // Se ignora la propia realizacion al validar solapamientos
            $this->validarRango($datos, $realizacion->id);

            $antes = $this->pares($realizacion);
            $realizacion->update($datos);
            Inventario::asegurar(array_merge($antes, $this->pares($realizacion)));
        });

        return redirect()->route('admin.especies.realizaciones.historial')
            ->with('success', 'Realización actualizada correctamente.');
    }

    public function destroy(Realizacion $realizacion)
    {
        $realizacion->delete();

        // back() conserva la pagina y los filtros del historial
        return back()->with('success', 'Realización eliminada correctamente.');
    }

    // Valida el formulario y devuelve los campos listos para guardar
    private function datos(Request $request): array
    {
        $request->validate([
            'distrito_id'          => 'required|exists:distritos,id',
            'tipo_especie_id'      => 'required|exists:tipo_especies,id',
            'denominacion_id'      => 'required|exists:denominaciones,id',
            'serie'                => 'nullable|string|max:10',
            'numero_inicio'        => 'required|integer|min:1|max:999999999',
            'numero_fin'           => 'required|integer|min:1|max:999999999|gte:numero_inicio',
            'fecha'                => 'required|date|before_or_equal:today',
            'nombre_contribuyente' => 'nullable|string|max:200',
        ], [
            'fecha.before_or_equal'    => 'La fecha no puede ser posterior a hoy.',
            'distrito_id.required'     => 'Seleccione un distrito.',
            'tipo_especie_id.required' => 'Seleccione un tipo de especie.',
            'denominacion_id.required' => 'Seleccione una denominación.',
            'numero_inicio.required'   => 'El número de inicio es obligatorio.',
            'numero_fin.required'      => 'El número de fin es obligatorio.',
            'numero_fin.gte'           => 'El número fin debe ser mayor o igual al inicio.',
            'numero_inicio.max'        => 'El número de inicio no es válido.',
            'numero_fin.max'           => 'El número fin no es válido.',
            'fecha.required'           => 'La fecha es obligatoria.',
        ]);

        $denom = Denominacion::findOrFail($request->denominacion_id);
        if ($denom->tipo_especie_id != $request->tipo_especie_id) {
            throw ValidationException::withMessages(['denominacion_id' => 'La denominación no pertenece al tipo seleccionado.']);
        }

        $inicio   = (int) $request->numero_inicio;
        $fin      = (int) $request->numero_fin;
        $cantidad = $fin - $inicio + 1;

        // Se cobra el precio de venta del distrito; el valor de la denominacion es el del libro y
        // puede ser el costo (carnet: $0.09 en libro, $2.00 cobrado). Sin precio de venta no se cobra.
        $precioVenta = Tarifas::precioVenta((int) $request->distrito_id, $denom) ?? 0;

        return [
            'distrito_id'          => (int) $request->distrito_id,
            'tipo_especie_id'      => (int) $request->tipo_especie_id,
            'denominacion_id'      => $denom->id,
            'serie'                => Inventario::serie($request->serie),
            'numero_inicio'        => $inicio,
            'numero_fin'           => $fin,
            'cantidad'             => $cantidad,
            'fecha'                => $request->fecha,
            'nombre_contribuyente' => $request->nombre_contribuyente,
            'monto_cobrado'        => round($cantidad * $precioVenta, 2),
        ];
    }

    /**
     * Reglas propias del rango. Que el distrito tuviera esos documentos en esa fecha
     * lo verifica Inventario::asegurar despues de escribir.
     */
    private function validarRango(array $d, ?int $ignorarId = null): void
    {
        $serie = fn($q) => $d['serie'] === null ? $q->whereNull('serie') : $q->where('serie', $d['serie']);

        $lotes   = Inventario::lotesDelRango($d['tipo_especie_id'], $d['serie'], $d['numero_inicio'], $d['numero_fin']);
        $propios = $lotes->where('denominacion_id', $d['denominacion_id'])
            ->flatMap(fn($l) => $l->rangos->map(fn($r) => [$r->numero_inicio, $r->numero_fin]))
            ->all();

        if (!Inventario::contiene($propios, $d['numero_inicio'], $d['numero_fin'])) {
            $otra = $lotes->firstWhere('denominacion_id', '!=', $d['denominacion_id']);
            throw ValidationException::withMessages(['numero_inicio' => $otra
                ? 'Esos números pertenecen a la denominación de $' . number_format($otra->denominacion->valor, 2) . '.'
                : 'Ese rango no existe completo en ninguna compra de ese tipo ' . ($d['serie'] ? "y serie {$d['serie']}." : 'sin serie.')]);
        }

        $realizada = Realizacion::with('distrito')
            ->where('tipo_especie_id', $d['tipo_especie_id'])
            ->where($serie)
            ->when($ignorarId, fn($q) => $q->where('id', '!=', $ignorarId))
            ->where('numero_inicio', '<=', $d['numero_fin'])
            ->where('numero_fin', '>=', $d['numero_inicio'])
            ->first();

        if ($realizada) {
            throw ValidationException::withMessages(['numero_inicio' => 'Parte del rango ya fue realizada ('
                . number_format($realizada->numero_inicio) . '–' . number_format($realizada->numero_fin)
                . ", {$realizada->fecha->format('d/m/Y')}, {$realizada->distrito->nombre})."]);
        }

        $anulada = Nula::whereHas('trasladoDetalle.lote', fn($q) => $q->where('tipo_especie_id', $d['tipo_especie_id'])->where($serie))
            ->where('numero_inicio', '<=', $d['numero_fin'])
            ->where('numero_fin', '>=', $d['numero_inicio'])
            ->first();

        if ($anulada) {
            throw ValidationException::withMessages(['numero_inicio' => 'Parte del rango está anulada ('
                . number_format($anulada->numero_inicio) . '–' . number_format($anulada->numero_fin)
                . ", {$anulada->fecha->format('d/m/Y')}) y no puede realizarse."]);
        }
    }

    // Pares distrito-lote cuya historia toca esta realizacion
    private function pares(Realizacion $r): array
    {
        return Inventario::lotesDelRango($r->tipo_especie_id, $r->serie, $r->numero_inicio, $r->numero_fin)
            ->map(fn($l) => [$r->distrito_id, $l->id])
            ->all();
    }

    // AJAX: lo que el distrito tiene de un tipo, agrupado por denominacion y serie
    public function ajaxInfoDistritoTipo(Request $request)
    {
        $distritoId = (int) $request->distrito_id;
        $inv = Inventario::distrito($distritoId, (int) $request->tipo_especie_id,
            null, $request->integer('ignorar') ?: null);

        $grupos = $inv->filter(fn($r) => $r['cantidad'] > 0)
            ->groupBy(fn($r) => $r['lote']->denominacion_id . '|' . $r['lote']->serie)
            ->map(function ($filas) use ($distritoId) {
                $lote       = $filas->first()['lote'];
                $intervalos = Inventario::neto($filas->pluck('intervalos')->collapse()->all());

                return [
                    'denominacion_id' => $lote->denominacion_id,
                    'valor'           => (float) $lote->denominacion->valor,
                    'precio_venta'    => Tarifas::precioVenta($distritoId, $lote->denominacion),
                    'etiqueta'        => $lote->denominacion->etiqueta,
                    'serie'           => $lote->serie,
                    'disponible'      => Inventario::total($intervalos),
                    'rangos'          => array_map(fn($i) => ['inicio' => $i[0], 'fin' => $i[1]], $intervalos),
                ];
            })
            ->sort(fn($a, $b) => [$a['valor'], (string) $a['serie']] <=> [$b['valor'], (string) $b['serie']])
            ->values();

        return response()->json([
            'recibido'   => $inv->sum('recibido'),
            'salido'     => $inv->sum('salido'),
            'anulado'    => $inv->sum('anulado'),
            'realizado'  => $inv->sum('realizado'),
            'disponible' => $inv->sum('cantidad'),
            'grupos'     => $grupos,
        ]);
    }
}
