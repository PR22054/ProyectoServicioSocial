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
            ->paginate(25)
            ->withQueryString();

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

        DB::transaction(function () use ($request, $detalle, $inicio, $fin) {
            $lote = $detalle->lote;
            Inventario::bloquear($lote->tipo_especie_id);

            // Un numero se anula una sola vez, sin importar por cual traslado llego
            $yaAnulado = Nula::whereHas('trasladoDetalle', fn($q) => $q->where('lote_id', $lote->id))
                ->where('numero_inicio', '<=', $fin)
                ->where('numero_fin', '>=', $inicio)
                ->exists();

            if ($yaAnulado) {
                throw ValidationException::withMessages(['numero_inicio' => 'Ese rango (o parte de él) ya fue anulado anteriormente.']);
            }

            // No se puede anular lo ya entregado a un contribuyente (misma serie, en cualquier distrito)
            $yaRealizado = Realizacion::where('tipo_especie_id', $lote->tipo_especie_id)
                ->where(fn($q) => $lote->serie === null ? $q->whereNull('serie') : $q->where('serie', $lote->serie))
                ->where('numero_inicio', '<=', $fin)
                ->where('numero_fin', '>=', $inicio)
                ->exists();

            if ($yaRealizado) {
                throw ValidationException::withMessages(['numero_inicio' => 'Ese rango (o parte de él) ya fue realizado y no puede anularse.']);
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

            // Que siga en el distrito a esa fecha (no enviado a otro lado) lo decide la historia del lote
            Inventario::asegurar([[(int) $request->distrito_id, $lote->id]]);
        });

        return redirect()->route('admin.especies.distritos.anulaciones.historial')
            ->with('success', 'Anulación registrada correctamente.');
    }

    public function anulacionDestroy(Nula $nula)
    {
        $nula->delete();

        // back() conserva la pagina y el filtro del historial
        return back()->with('success', 'Anulación eliminada correctamente.');
    }

    // ── AJAX ─────────────────────────────────────────────────────────────────

    /**
     * Devuelve los traslado_detalles de un distrito y tipo_especie con lo que de cada uno
     * sigue en el distrito (no realizado, no anulado, no enviado a otro lado).
     */
    public function ajaxDetallesDisponibles(Request $request)
    {
        $distId = (int) $request->distrito_id;
        $tipoId = (int) $request->tipo_especie_id;
        $inv    = Inventario::distrito($distId, $tipoId);

        $detalles = TrasladoDetalle::with('lote.tipoEspecie', 'lote.denominacion', 'traslado')
            ->whereHas('traslado', fn($q) => $q->where('distrito_id', $distId))
            ->whereHas('lote',     fn($q) => $q->where('tipo_especie_id', $tipoId))
            ->orderBy('numero_inicio')
            ->get()
            ->map(function ($d) use ($inv) {
                $rangos     = Inventario::interseccion($inv->get($d->lote_id)['intervalos'] ?? [],
                                                        [[$d->numero_inicio, $d->numero_fin]]);
                $disponible = Inventario::total($rangos);

                // Rangos ya anulados (para informar al usuario)
                $nulas = Nula::where('traslado_detalle_id', $d->id)
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

    // ── Stock por distrito ────────────────────────────────────────────────────

    public function stock(Request $request)
    {
        $distritos  = Distrito::where('activo', true)->orderBy('codigo')->get();
        $distFiltro = $request->distrito_id;

        // Una fila por distrito y lote, con los rangos que siguen en existencia
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
