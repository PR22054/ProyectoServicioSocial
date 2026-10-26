<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;
use App\Models\Distrito;
use App\Models\Nula;
use App\Models\Realizacion;
use App\Models\TipoEspecie;
use App\Models\TrasladoDetalle;
use App\Services\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

//controlador de distritos: anulaciones (registrar, editar, eliminar e historial) y stock de cada distrito
class DistritoController extends Controller
{
    //historial de anulaciones

    public function anulacionHistorial(Request $request)
    {
        $distritos   = Distrito::where('activo', true)->orderBy('codigo')->get();
        $distFiltro  = $request->distrito_id;

        $nulas = Nula::with(
                'distrito',
                'usuario',
                'trasladoDetalle.lote.tipoEspecie',
                'trasladoDetalle.lote.denominacion',
                'trasladoDetalle.lote.compra'
            )
            ->when($distFiltro, fn($q) => $q->where('distrito_id', $distFiltro))
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('frontend.admin.especies.distritos.anulaciones.historial',
            compact('nulas', 'distritos', 'distFiltro'));
    }

    //registrar anulacion

    public function anulacionCrear()
    {
        $distritos = Distrito::where('activo', true)->orderBy('codigo')->get();
        $tipos     = TipoEspecie::where('activo', true)->orderBy('nombre')->get();

        return view('frontend.admin.especies.distritos.anulaciones.crear',
            compact('distritos', 'tipos'));
    }

    //registra una anulacion nueva
    public function anulacionStore(Request $request)
    {
        $this->guardarAnulacion($request);

        return redirect()->route('admin.especies.distritos.anulaciones.historial')
            ->with('success', 'Anulación registrada correctamente.');
    }

    //editar anulacion

    public function anulacionEditar(Nula $nula)
    {
        $nula->load('trasladoDetalle.lote');
        $distritos = Distrito::where('activo', true)->orWhere('id', $nula->distrito_id)->orderBy('codigo')->get();
        //el tipo de la anulacion aparece aunque ya este inactivo
        $tipos     = TipoEspecie::where('activo', true)->orWhere('id', $nula->trasladoDetalle->lote->tipo_especie_id)->orderBy('nombre')->get();

        //al guardar se vuelve a la pagina y filtro del historial de donde se vino
        $volver = str_starts_with(url()->previous(), route('admin.especies.distritos.anulaciones.historial'))
            ? url()->previous() : route('admin.especies.distritos.anulaciones.historial');

        return view('frontend.admin.especies.distritos.anulaciones.crear', compact('distritos', 'tipos', 'nula', 'volver'));
    }

    //guarda los cambios de una anulacion y regresa al historial de donde se vino
    public function anulacionUpdate(Request $request, Nula $nula)
    {
        $this->guardarAnulacion($request, $nula);

        $volver = (string) $request->volver;
        return redirect(str_starts_with($volver, route('admin.especies.distritos.anulaciones.historial'))
                ? $volver : route('admin.especies.distritos.anulaciones.historial'))
            ->with('success', 'Anulación actualizada correctamente.');
    }

    //registra una anulacion nueva o cambia una existente con las mismas reglas: el detalle es del distrito,
    //el rango cabe en el detalle, no esta anulado (salvo por la misma anulacion) ni realizado, y el distrito
    //lo tenia en existencia a esa fecha.
    private function guardarAnulacion(Request $request, ?Nula $nula = null): void
    {
        $request->validate([
            'distrito_id'         => 'required|exists:distritos,id',
            'traslado_detalle_id' => 'required|exists:traslado_detalles,id',
            'numero_inicio'       => 'required|integer|min:1|max:999999999',
            'numero_fin'          => 'required|integer|min:1|max:999999999|gte:numero_inicio',
            'fecha'               => 'required|date|before_or_equal:today',
            'motivo'              => 'nullable|string|max:255',
        ], [
            'fecha.before_or_equal'        => 'La fecha no puede ser posterior a hoy.',
            'distrito_id.required'         => 'Seleccione un distrito.',
            'traslado_detalle_id.required' => 'Seleccione un detalle de traslado.',
            'numero_inicio.required'       => 'El número de inicio es obligatorio.',
            'numero_fin.required'          => 'El número de fin es obligatorio.',
            'numero_fin.gte'               => 'El número fin debe ser mayor o igual al inicio.',
            'fecha.required'               => 'La fecha es obligatoria.',
        ]);

        $inicio = (int) $request->numero_inicio;
        $fin    = (int) $request->numero_fin;

        $detalle = TrasladoDetalle::with('traslado', 'lote')->findOrFail($request->traslado_detalle_id);

        //el traslado_detalle debe pertenecer al distrito indicado
        if ($detalle->traslado->distrito_id != $request->distrito_id) {
            throw ValidationException::withMessages(['traslado_detalle_id' => 'El detalle seleccionado no pertenece a ese distrito.']);
        }

        //el rango debe estar contenido en el detalle
        if ($inicio < $detalle->numero_inicio || $fin > $detalle->numero_fin) {
            throw ValidationException::withMessages(['numero_inicio' => "El rango debe estar dentro del rango del detalle ({$detalle->numero_inicio}–{$detalle->numero_fin})."]);
        }

        DB::transaction(function () use ($request, $detalle, $inicio, $fin, $nula) {
            $lote  = $detalle->lote;
            $antes = $nula ? [[$nula->distrito_id, $nula->trasladoDetalle->lote_id]] : [];
            Inventario::bloquear($lote->tipo_especie_id, ...($nula ? [$nula->trasladoDetalle->lote->tipo_especie_id] : []));

            //un numero se anula una sola vez, sin importar por cual traslado llego (la propia anulacion no cuenta)
            $yaAnulado = Nula::whereHas('trasladoDetalle', fn($q) => $q->where('lote_id', $lote->id))
                ->when($nula, fn($q) => $q->whereKeyNot($nula->id))
                ->where('numero_inicio', '<=', $fin)
                ->where('numero_fin', '>=', $inicio)
                ->exists();

            if ($yaAnulado) {
                throw ValidationException::withMessages(['numero_inicio' => 'Ese rango (o parte de él) ya fue anulado anteriormente.']);
            }

            //no se puede anular lo ya realizado (misma serie, en cualquier distrito)
            $yaRealizado = Realizacion::where('tipo_especie_id', $lote->tipo_especie_id)
                ->where(fn($q) => $lote->serie === null ? $q->whereNull('serie') : $q->where('serie', $lote->serie))
                ->where('numero_inicio', '<=', $fin)
                ->where('numero_fin', '>=', $inicio)
                ->exists();

            if ($yaRealizado) {
                throw ValidationException::withMessages(['numero_inicio' => 'Ese rango (o parte de él) ya fue realizado y no puede anularse.']);
            }

            $datos = [
                'traslado_detalle_id' => $detalle->id,
                'distrito_id'         => $request->distrito_id,
                'numero_inicio'       => $inicio,
                'numero_fin'          => $fin,
                'fecha'               => $request->fecha,
                'motivo'              => $request->motivo,
            ];
            $nula ? $nula->update($datos) : Nula::create($datos + ['usuario_id' => auth()->id()]);

            //que siga en el distrito a esa fecha (no enviado a otro lado) lo decide la historia del lote;
            //al editar tambien se revisa el lote y distrito anteriores
            Inventario::asegurar(array_merge($antes, [[(int) $request->distrito_id, $lote->id]]));
        });
    }

    //elimina una anulacion; sus numeros vuelven a la existencia del distrito
    public function anulacionDestroy(Nula $nula)
    {
        $nula->delete();

        //back() conserva la pagina y el filtro del historial
        return back()->with('success', 'Anulación eliminada correctamente.');
    }

    //AJAX

    //devuelve los traslado_detalles de un distrito y tipo_especie con lo que de cada uno
    //sigue en el distrito (no realizado, no anulado, no enviado a otro lado).
    public function ajaxDetallesDisponibles(Request $request)
    {
        $distId = (int) $request->distrito_id;
        $tipoId = (int) $request->tipo_especie_id;
        $inv    = Inventario::distrito($distId, $tipoId);
        //al editar una anulacion, sus numeros vuelven a estar disponibles en su detalle
        $editada = $request->nula_id ? Nula::find($request->nula_id) : null;

        $detalles = TrasladoDetalle::with('lote.tipoEspecie', 'lote.denominacion', 'traslado')
            ->whereHas('traslado', fn($q) => $q->where('distrito_id', $distId))
            ->whereHas('lote',     fn($q) => $q->where('tipo_especie_id', $tipoId))
            ->orderBy('numero_inicio')
            ->get()
            ->map(function ($d) use ($inv, $editada) {
                $rangos     = Inventario::interseccion($inv->get($d->lote_id)['intervalos'] ?? [],
                                                        [[$d->numero_inicio, $d->numero_fin]]);
                if ($editada && $editada->traslado_detalle_id == $d->id) {
                    $rangos = Inventario::neto(array_merge($rangos, [[$editada->numero_inicio, $editada->numero_fin]]));
                }
                $disponible = Inventario::total($rangos);

                //rangos ya anulados (para informar al usuario)
                $nulas = Nula::where('traslado_detalle_id', $d->id)
                            ->when($editada, fn($q) => $q->whereKeyNot($editada->id))
                            ->orderBy('numero_inicio')
                            ->get(['numero_inicio', 'numero_fin']);

                return [
                    'id'          => $d->id,
                    'label'       => $d->lote->tipoEspecie->nombre
                                   . ' — $' . number_format($d->lote->denominacion->valor, 2)
                                   . ($d->lote->serie ? ' — Serie ' . $d->lote->serie : '')
                                   . ' — Rango: ' . number_format($d->numero_inicio) . '–' . number_format($d->numero_fin)
                                   . ' — Disp: ' . number_format($disponible),
                    'inicio'      => $d->numero_inicio,
                    'fin'         => $d->numero_fin,
                    'disponible'  => $disponible,
                    'rangos'      => array_map(fn($i) => ['inicio' => $i[0], 'fin' => $i[1]], $rangos),
                    'ya_anulados' => $nulas->map(fn($n) => [
                        'inicio' => $n->numero_inicio,
                        'fin'    => $n->numero_fin,
                    ]),
                ];
            })
            ->filter(fn($d) => $d['disponible'] > 0)
            ->values();

        return response()->json($detalles);
    }

    //stock por distrito

    public function stock(Request $request)
    {
        $distritos  = Distrito::where('activo', true)->orderBy('codigo')->get();
        $distFiltro = $request->distrito_id;

        //una fila por distrito y lote, con los rangos que siguen en existencia
        $filas = $distritos
            ->when($distFiltro, fn($c) => $c->where('id', $distFiltro))
            ->flatMap(fn($d) => Inventario::distrito($d->id)->map(fn($r) => $r + ['distrito' => $d]))
            ->sortBy([
                fn($a, $b) => $a['distrito']->codigo <=> $b['distrito']->codigo,
                fn($a, $b) => $a['lote']->tipoEspecie->nombre <=> $b['lote']->tipoEspecie->nombre,
            ])
            ->values();

        return view('frontend.admin.especies.distritos.stock',
            compact('filas', 'distritos', 'distFiltro'));
    }
}
