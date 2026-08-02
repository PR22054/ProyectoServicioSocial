<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;
use App\Models\Distrito;
use App\Models\Nula;
use App\Models\TipoEspecie;
use App\Models\TrasladoDetalle;
use Illuminate\Http\Request;

class DistritoController extends Controller
{
    // ── Historial de anulaciones ──────────────────────────────────────────────

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
            ->get();

        return view('frontend.admin.especies.distritos.anulaciones.historial',
            compact('nulas', 'distritos', 'distFiltro'));
    }

    // ── Registrar anulación ───────────────────────────────────────────────────

    public function anulacionCrear()
    {
        $distritos = Distrito::where('activo', true)->orderBy('codigo')->get();
        $tipos     = TipoEspecie::where('activo', true)->orderBy('nombre')->get();

        return view('frontend.admin.especies.distritos.anulaciones.crear',
            compact('distritos', 'tipos'));
    }

    public function anulacionStore(Request $request)
    {
        $request->validate([
            'distrito_id'         => 'required|exists:distritos,id',
            'traslado_detalle_id' => 'required|exists:traslado_detalles,id',
            'numero_inicio'       => 'required|integer|min:1',
            'numero_fin'          => 'required|integer|min:1',
            'fecha'               => 'required|date',
            'motivo'              => 'nullable|string|max:255',
        ], [
            'distrito_id.required'         => 'Seleccione un distrito.',
            'traslado_detalle_id.required' => 'Seleccione un detalle de traslado.',
            'numero_inicio.required'       => 'El número de inicio es obligatorio.',
            'numero_fin.required'          => 'El número de fin es obligatorio.',
            'fecha.required'               => 'La fecha es obligatoria.',
        ]);

        $inicio   = (int) $request->numero_inicio;
        $fin      = (int) $request->numero_fin;
        $cantidad = $fin - $inicio + 1;

        if ($inicio > $fin) {
            return back()
                ->withErrors(['numero_fin' => 'El número fin debe ser mayor al inicio.'])
                ->withInput();
        }

        $detalle = TrasladoDetalle::with('traslado')->findOrFail($request->traslado_detalle_id);

        // El traslado_detalle debe pertenecer al distrito indicado
        if ($detalle->traslado->distrito_id != $request->distrito_id) {
            return back()
                ->withErrors(['traslado_detalle_id' => 'El detalle seleccionado no pertenece a ese distrito.'])
                ->withInput();
        }

        // El rango debe estar contenido en el detalle
        if ($inicio < $detalle->numero_inicio || $fin > $detalle->numero_fin) {
            return back()
                ->withErrors(['numero_inicio' => "El rango debe estar dentro del rango del detalle ({$detalle->numero_inicio}–{$detalle->numero_fin})."])
                ->withInput();
        }

        // Sin solapamiento con nulas ya registradas para el mismo detalle
        $overlap = Nula::where('traslado_detalle_id', $detalle->id)
            ->where('numero_inicio', '<=', $fin)
            ->where('numero_fin', '>=', $inicio)
            ->exists();

        if ($overlap) {
            return back()
                ->withErrors(['numero_inicio' => 'Ese rango (o parte de él) ya fue anulado anteriormente.'])
                ->withInput();
        }

        // Stock disponible en el detalle (sin contar nulas ya registradas)
        $yaAnulado  = Nula::where('traslado_detalle_id', $detalle->id)
                        ->selectRaw('SUM(numero_fin - numero_inicio + 1) as total')
                        ->value('total') ?? 0;
        $disponible = $detalle->cantidad - $yaAnulado;

        if ($cantidad > $disponible) {
            return back()
                ->withErrors(['numero_fin' => "La cantidad ({$cantidad}) supera el disponible ({$disponible}) en ese detalle."])
                ->withInput();
        }

        Nula::create([
            'traslado_detalle_id' => $detalle->id,
            'distrito_id'         => $request->distrito_id,
            'numero_inicio'       => $inicio,
            'numero_fin'          => $fin,
            'fecha'               => $request->fecha,
            'motivo'              => $request->motivo,
            'usuario_id'          => auth()->id(),
        ]);

        return redirect()->route('admin.especies.distritos.anulaciones.historial')
            ->with('success', 'Anulación registrada correctamente.');
    }

    public function anulacionDestroy(Nula $nula)
    {
        $nula->delete();

        return redirect()->route('admin.especies.distritos.anulaciones.historial')
            ->with('success', 'Anulación eliminada correctamente.');
    }

    // ── AJAX ─────────────────────────────────────────────────────────────────

    /**
     * Devuelve los traslado_detalles de un distrito y tipo_especie
     * con el stock restante (cantidad - ya anulado) > 0.
     */
    public function ajaxDetallesDisponibles(Request $request)
    {
        $detalles = TrasladoDetalle::with('lote.tipoEspecie', 'lote.denominacion', 'traslado')
            ->whereHas('traslado', fn($q) => $q->where('distrito_id', $request->distrito_id))
            ->whereHas('lote',     fn($q) => $q->where('tipo_especie_id', $request->tipo_especie_id))
            ->get()
            ->map(function ($d) {
                $yaAnulado  = Nula::where('traslado_detalle_id', $d->id)
                                ->selectRaw('SUM(numero_fin - numero_inicio + 1) as total')
                                ->value('total') ?? 0;
                $disponible = $d->cantidad - $yaAnulado;

                // Rangos ya anulados (para informar al usuario)
                $nulas = Nula::where('traslado_detalle_id', $d->id)
                            ->orderBy('numero_inicio')
                            ->get(['numero_inicio', 'numero_fin']);

                return [
                    'id'          => $d->id,
                    'label'       => $d->lote->tipoEspecie->nombre
                                   . ' — $' . number_format($d->lote->denominacion->valor, 2)
                                   . ' — Rango: ' . number_format($d->numero_inicio) . '–' . number_format($d->numero_fin)
                                   . ' — Disp: ' . number_format($disponible),
                    'inicio'      => $d->numero_inicio,
                    'fin'         => $d->numero_fin,
                    'disponible'  => $disponible,
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

    // ── Stock por distrito ────────────────────────────────────────────────────

    public function stock(Request $request)
    {
        $distritos  = Distrito::where('activo', true)->orderBy('codigo')->get();
        $distFiltro = $request->distrito_id;

        // Todos los traslado_detalles del distrito (o todos si no hay filtro)
        $detalles = TrasladoDetalle::with(
                'traslado.distrito',
                'lote.tipoEspecie',
                'lote.denominacion',
                'lote.compra'
            )
            ->when($distFiltro,
                fn($q) => $q->whereHas('traslado', fn($q2) => $q2->where('distrito_id', $distFiltro)),
                fn($q) => $q->whereHas('traslado') // trae todos
            )
            ->get()
            ->map(function ($d) {
                $yaAnulado  = Nula::where('traslado_detalle_id', $d->id)
                                ->selectRaw('SUM(numero_fin - numero_inicio + 1) as total')
                                ->value('total') ?? 0;
                $d->anulado    = $yaAnulado;
                $d->disponible = $d->cantidad - $yaAnulado;
                return $d;
            })
            ->sortBy([
                fn($a, $b) => $a->traslado->distrito->codigo <=> $b->traslado->distrito->codigo,
                fn($a, $b) => $a->lote->tipoEspecie->nombre  <=> $b->lote->tipoEspecie->nombre,
            ]);

        return view('frontend.admin.especies.distritos.stock',
            compact('detalles', 'distritos', 'distFiltro'));
    }
}
