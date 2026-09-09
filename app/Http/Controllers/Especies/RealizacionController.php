<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;
use App\Models\Denominacion;
use App\Models\Distrito;
use App\Models\Nula;
use App\Models\Realizacion;
use App\Models\TipoEspecie;
use App\Models\TrasladoDetalle;
use Illuminate\Http\Request;

class RealizacionController extends Controller
{
    //CONTROLADOR DE REALIZACIONES - gestiona historial, registro, eliminacion y endpoint AJAX de stock por distrito
    public function historial(Request $request)
    {
        $distritos  = Distrito::where('activo', true)->orderBy('codigo')->get();
        $tipos      = TipoEspecie::where('activo', true)->orderBy('nombre')->get();
        $distFiltro = $request->distrito_id;
        $tipoFiltro = $request->tipo_especie_id;

        $realizaciones = Realizacion::with('tipoEspecie', 'denominacion', 'distrito', 'usuario')
            ->when($distFiltro, fn($q) => $q->where('distrito_id', $distFiltro))
            ->when($tipoFiltro, fn($q) => $q->where('tipo_especie_id', $tipoFiltro))
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get();

        return view('frontend.admin.especies.realizaciones.historial',
            compact('realizaciones', 'distritos', 'tipos', 'distFiltro', 'tipoFiltro'));
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
        $request->validate([
            'distrito_id'          => 'required|exists:distritos,id',
            'tipo_especie_id'      => 'required|exists:tipo_especies,id',
            'denominacion_id'      => 'required|exists:denominaciones,id',
            'numero_inicio'        => 'required|integer|min:1',
            'numero_fin'           => 'required|integer|min:1',
            'fecha'                => 'required|date',
            'nombre_contribuyente' => 'nullable|string|max:200',
        ], [
            'distrito_id.required'     => 'Seleccione un distrito.',
            'tipo_especie_id.required' => 'Seleccione un tipo de especie.',
            'denominacion_id.required' => 'Seleccione una denominación.',
            'numero_inicio.required'   => 'El número de inicio es obligatorio.',
            'numero_fin.required'      => 'El número de fin es obligatorio.',
            'fecha.required'           => 'La fecha es obligatoria.',
        ]);

        $inicio = (int) $request->numero_inicio;
        $fin    = (int) $request->numero_fin;

        if ($inicio > $fin) {
            return back()
                ->withErrors(['numero_fin' => 'El número fin debe ser mayor al inicio.'])
                ->withInput();
        }

        $cantidad = $fin - $inicio + 1;
        $tipoId   = $request->tipo_especie_id;
        $distId   = $request->distrito_id;
        $denomId  = $request->denominacion_id;

        $denom = Denominacion::findOrFail($denomId);
        if ($denom->tipo_especie_id != $tipoId) {
            return back()
                ->withErrors(['denominacion_id' => 'La denominación no pertenece al tipo seleccionado.'])
                ->withInput();
        }

        if ($error = $this->validarRango($distId, $tipoId, $inicio, $fin)) {
            return back()->withErrors($error)->withInput();
        }

        Realizacion::create([
            'tipo_especie_id'      => $tipoId,
            'denominacion_id'      => $denomId,
            'distrito_id'          => $distId,
            'numero_inicio'        => $inicio,
            'numero_fin'           => $fin,
            'cantidad'             => $cantidad,
            'fecha'                => $request->fecha,
            'nombre_contribuyente' => $request->nombre_contribuyente,
            'monto_cobrado'        => $cantidad * $denom->valor,
            'usuario_id'           => auth()->id(),
        ]);

        return redirect()->route('admin.especies.realizaciones.historial')
            ->with('success', "Realización registrada: {$cantidad} documentos por $" . number_format($cantidad * $denom->valor, 2) . '.');
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
        $request->validate([
            'distrito_id'          => 'required|exists:distritos,id',
            'tipo_especie_id'      => 'required|exists:tipo_especies,id',
            'denominacion_id'      => 'required|exists:denominaciones,id',
            'numero_inicio'        => 'required|integer|min:1',
            'numero_fin'           => 'required|integer|min:1',
            'fecha'                => 'required|date',
            'nombre_contribuyente' => 'nullable|string|max:200',
        ], [
            'distrito_id.required'     => 'Seleccione un distrito.',
            'tipo_especie_id.required' => 'Seleccione un tipo de especie.',
            'denominacion_id.required' => 'Seleccione una denominación.',
            'numero_inicio.required'   => 'El número de inicio es obligatorio.',
            'numero_fin.required'      => 'El número de fin es obligatorio.',
            'fecha.required'           => 'La fecha es obligatoria.',
        ]);

        $inicio = (int) $request->numero_inicio;
        $fin    = (int) $request->numero_fin;

        if ($inicio > $fin) {
            return back()
                ->withErrors(['numero_fin' => 'El número fin debe ser mayor al inicio.'])
                ->withInput();
        }

        $cantidad = $fin - $inicio + 1;
        $tipoId   = $request->tipo_especie_id;
        $distId   = $request->distrito_id;

        $denom = Denominacion::findOrFail($request->denominacion_id);
        if ($denom->tipo_especie_id != $tipoId) {
            return back()
                ->withErrors(['denominacion_id' => 'La denominación no pertenece al tipo seleccionado.'])
                ->withInput();
        }

        // Se ignora la propia realizacion al validar solapamientos
        if ($error = $this->validarRango($distId, $tipoId, $inicio, $fin, $realizacion->id)) {
            return back()->withErrors($error)->withInput();
        }

        $realizacion->update([
            'tipo_especie_id'      => $tipoId,
            'denominacion_id'      => $denom->id,
            'distrito_id'          => $distId,
            'numero_inicio'        => $inicio,
            'numero_fin'           => $fin,
            'cantidad'             => $cantidad,
            'fecha'                => $request->fecha,
            'nombre_contribuyente' => $request->nombre_contribuyente,
            'monto_cobrado'        => $cantidad * $denom->valor,
        ]);

        return redirect()->route('admin.especies.realizaciones.historial')
            ->with('success', 'Realización actualizada correctamente.');
    }

    public function destroy(Realizacion $realizacion)
    {
        $realizacion->delete();

        return redirect()->route('admin.especies.realizaciones.historial')
            ->with('success', 'Realización eliminada correctamente.');
    }

    /**
     * Valida que el rango sea realizable en el distrito. Devuelve el error o null.
     * $ignorarId excluye una realizacion del chequeo de solapamiento (para edicion).
     */
    private function validarRango(int $distId, int $tipoId, int $inicio, int $fin, ?int $ignorarId = null): ?array
    {
        if (!$this->rangoCubierto($distId, $tipoId, $inicio, $fin)) {
            return ['numero_inicio' => 'El rango no está dentro de los documentos trasladados a este distrito.'];
        }

        $overlapReal = Realizacion::where('tipo_especie_id', $tipoId)
            ->when($ignorarId, fn($q) => $q->where('id', '!=', $ignorarId))
            ->where('numero_inicio', '<=', $fin)
            ->where('numero_fin',    '>=', $inicio)
            ->exists();

        if ($overlapReal) {
            return ['numero_inicio' => 'Parte o la totalidad del rango ya fue realizada anteriormente.'];
        }

        $overlapNula = Nula::whereHas('trasladoDetalle', function ($q) use ($distId, $tipoId) {
                $q->whereHas('traslado', fn($q2) => $q2->where('distrito_id', $distId))
                  ->whereHas('lote',     fn($q2) => $q2->where('tipo_especie_id', $tipoId));
            })
            ->where('numero_inicio', '<=', $fin)
            ->where('numero_fin',    '>=', $inicio)
            ->exists();

        if ($overlapNula) {
            return ['numero_inicio' => 'Parte o la totalidad del rango está anulada y no puede realizarse.'];
        }

        return null;
    }

    // AJAX: stock disponible + denominaciones para distrito + tipo
    public function ajaxInfoDistritoTipo(Request $request)
    {
        $distId = (int) $request->distrito_id;
        $tipoId = (int) $request->tipo_especie_id;

        $detalles = TrasladoDetalle::whereHas('traslado', fn($q) => $q->where('distrito_id', $distId))
            ->whereHas('lote',     fn($q) => $q->where('tipo_especie_id', $tipoId))
            ->get(['numero_inicio', 'numero_fin', 'cantidad']);

        $recibido = $detalles->sum('cantidad');

        // Documentos que salieron del distrito (devueltos a bodega o enviados a otro distrito)
        $salido = TrasladoDetalle::whereHas('traslado', fn($q) =>
                $q->whereIn('tipo', ['distrito_bodega', 'distrito_distrito'])
                  ->where('origen_distrito_id', $distId))
            ->whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipoId))
            ->sum('cantidad');

        $anulado = Nula::whereHas('trasladoDetalle', function ($q) use ($distId, $tipoId) {
                $q->whereHas('traslado', fn($q2) => $q2->where('distrito_id', $distId))
                  ->whereHas('lote',     fn($q2) => $q2->where('tipo_especie_id', $tipoId));
            })
            ->selectRaw('COALESCE(SUM(numero_fin - numero_inicio + 1), 0) as total')
            ->value('total') ?? 0;

        $realizado = Realizacion::where('tipo_especie_id', $tipoId)
            ->where('distrito_id', $distId)
            ->sum('cantidad');

        $disponible = $recibido - $salido - $anulado - $realizado;

        $denominaciones = Denominacion::where('tipo_especie_id', $tipoId)
            ->where('activo', true)
            ->orderBy('valor')
            ->get(['id', 'valor']);

        $rangos = $detalles->map(fn($d) => [
            'inicio' => $d->numero_inicio,
            'fin'    => $d->numero_fin,
        ]);

        return response()->json(compact('disponible', 'recibido', 'salido', 'anulado', 'realizado', 'rangos', 'denominaciones'));
    }

    private function rangoCubierto(int $distId, int $tipoId, int $inicio, int $fin): bool
    {
        $detalles = TrasladoDetalle::whereHas('traslado', fn($q) => $q->where('distrito_id', $distId))
            ->whereHas('lote',     fn($q) => $q->where('tipo_especie_id', $tipoId))
            ->where('numero_inicio', '<=', $fin)
            ->where('numero_fin',   '>=', $inicio)
            ->orderBy('numero_inicio')
            ->get(['numero_inicio', 'numero_fin']);

        if ($detalles->isEmpty()) return false;

        $cubierto = $inicio;
        foreach ($detalles as $d) {
            if ($d->numero_inicio > $cubierto) break;
            $cubierto = max($cubierto, $d->numero_fin + 1);
            if ($cubierto > $fin) return true;
        }

        if ($cubierto <= $fin) return false;

        // Verificar que ninguna parte del rango salió del distrito (devolucion o traslado a otro distrito)
        return !TrasladoDetalle::whereHas('traslado', fn($q) =>
                $q->whereIn('tipo', ['distrito_bodega', 'distrito_distrito'])
                  ->where('origen_distrito_id', $distId))
            ->whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipoId))
            ->where('numero_inicio', '<=', $fin)
            ->where('numero_fin', '>=', $inicio)
            ->exists();
    }
}
