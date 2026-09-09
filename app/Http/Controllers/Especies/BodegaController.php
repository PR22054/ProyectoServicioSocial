<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;
use App\Models\Distrito;
use App\Models\Lote;
use App\Models\LoteRango;
use App\Models\Realizacion;
use App\Models\TipoEspecie;
use App\Models\Traslado;
use App\Models\TrasladoDetalle;
use Illuminate\Http\Request;

class BodegaController extends Controller
{
    // ── Traslados ─────────────────────────────────────────────────────────────

    public function trasladoHistorial()
    {
        $traslados = Traslado::with('distrito', 'origenDistrito', 'usuario')
            ->withCount('detalles')
            ->withSum('detalles', 'cantidad')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get();

        $distritos = Distrito::where('activo', true)->orderBy('codigo')->get();

        return view('frontend.admin.especies.bodega.traslados.historial', compact('traslados', 'distritos'));
    }

    public function trasladoCrear()
    {
        $distritos = Distrito::where('activo', true)->orderBy('codigo')->get();
        $tipo      = request('tipo', 'bodega_distrito');
        return view('frontend.admin.especies.bodega.traslados.crear', compact('distritos', 'tipo'));
    }

    public function trasladoStore(Request $request)
    {
        $tipo = $request->tipo;

        $rules = [
            'tipo'          => 'required|in:bodega_distrito,distrito_bodega,distrito_distrito',
            'fecha'         => 'required|date',
            'observaciones' => 'nullable|string|max:500',
        ];

        if ($tipo === 'bodega_distrito') {
            $rules['distrito_id'] = 'required|exists:distritos,id';
        } elseif ($tipo === 'distrito_bodega') {
            $rules['origen_distrito_id'] = 'required|exists:distritos,id';
        } elseif ($tipo === 'distrito_distrito') {
            $rules['origen_distrito_id'] = 'required|exists:distritos,id';
            $rules['distrito_id']        = 'required|exists:distritos,id|different:origen_distrito_id';
        }

        $request->validate($rules, [
            'distrito_id.required'        => 'Seleccione un distrito destino.',
            'distrito_id.different'       => 'El distrito destino debe ser diferente al de origen.',
            'origen_distrito_id.required' => 'Seleccione el distrito de origen.',
        ]);

        $traslado = Traslado::create([
            'tipo'               => $tipo,
            'distrito_id'        => in_array($tipo, ['bodega_distrito', 'distrito_distrito']) ? $request->distrito_id : null,
            'origen_distrito_id' => in_array($tipo, ['distrito_bodega', 'distrito_distrito'])  ? $request->origen_distrito_id : null,
            'fecha'              => $request->fecha,
            'observaciones'      => $request->observaciones,
            'usuario_id'         => auth()->id(),
        ]);

        return redirect()->route('admin.especies.bodega.traslado.show', $traslado)
            ->with('success', 'Traslado creado. Ahora agregue los detalles.');
    }

    public function trasladoUpdate(Request $request, Traslado $traslado)
    {
        // El tipo no se puede cambiar despues de crear; solo fecha, observaciones y districtos
        $tipo  = $traslado->tipo;
        $rules = [
            'fecha'         => 'required|date',
            'observaciones' => 'nullable|string|max:500',
        ];

        if ($tipo === 'bodega_distrito') {
            $rules['distrito_id'] = 'required|exists:distritos,id';
        } elseif ($tipo === 'distrito_bodega') {
            $rules['origen_distrito_id'] = 'required|exists:distritos,id';
        } elseif ($tipo === 'distrito_distrito') {
            $rules['origen_distrito_id'] = 'required|exists:distritos,id';
            $rules['distrito_id']        = 'required|exists:distritos,id|different:origen_distrito_id';
        }

        $request->validate($rules, [
            'distrito_id.required'        => 'Seleccione un distrito destino.',
            'distrito_id.different'       => 'El distrito destino debe ser diferente al de origen.',
            'origen_distrito_id.required' => 'Seleccione el distrito de origen.',
        ]);

        $data = ['fecha' => $request->fecha, 'observaciones' => $request->observaciones];

        if (in_array($tipo, ['bodega_distrito', 'distrito_distrito'])) {
            $data['distrito_id'] = $request->distrito_id;
        }
        if (in_array($tipo, ['distrito_bodega', 'distrito_distrito'])) {
            $data['origen_distrito_id'] = $request->origen_distrito_id;
        }

        $traslado->update($data);

        return redirect()->route('admin.especies.bodega.traslado.historial')
            ->with('success', 'Traslado actualizado correctamente.');
    }

    public function trasladoDestroy(Traslado $traslado)
    {
        $tieneNulas = \DB::table('nulas')
            ->whereIn('traslado_detalle_id', $traslado->detalles()->pluck('id'))
            ->exists();

        if ($tieneNulas) {
            return back()->with('error', 'No se puede eliminar: el traslado tiene anulaciones registradas.');
        }

        $traslado->detalles()->delete();
        $traslado->delete();

        return redirect()->route('admin.especies.bodega.traslado.historial')
            ->with('success', 'Traslado eliminado correctamente.');
    }

    public function trasladoShow(Traslado $traslado)
    {
        $traslado->load(
            'distrito',
            'origenDistrito',
            'usuario',
            'detalles.lote.tipoEspecie',
            'detalles.lote.denominacion',
            'detalles.lote.compra'
        );

        return view('frontend.admin.especies.bodega.traslados.show', compact('traslado'));
    }

    public function trasladoDetalleCrear(Traslado $traslado)
    {
        $tipos = TipoEspecie::where('activo', true)->orderBy('nombre')->get();
        return view('frontend.admin.especies.bodega.traslados.detalles.crear', compact('traslado', 'tipos'));
    }

    public function trasladoDetalleStore(Request $request, Traslado $traslado)
    {
        $request->validate([
            'lote_id'       => 'required|exists:lotes,id',
            'numero_inicio' => 'required|integer|min:1',
            'numero_fin'    => 'required|integer|min:1',
        ], [
            'lote_id.required'       => 'Seleccione un lote.',
            'numero_inicio.required' => 'El número de inicio es obligatorio.',
            'numero_fin.required'    => 'El número de fin es obligatorio.',
        ]);

        $inicio = (int) $request->numero_inicio;
        $fin    = (int) $request->numero_fin;

        if ($inicio > $fin) {
            return back()
                ->withErrors(['numero_fin' => 'El número fin debe ser mayor al inicio.'])
                ->withInput();
        }

        $cantidad = $fin - $inicio + 1;
        $lote     = Lote::with('rangos')->findOrFail($request->lote_id);

        if ($traslado->tipo === 'bodega_distrito') {
            return $this->storeDetalleBodegaDistrito($request, $traslado, $lote, $inicio, $fin, $cantidad);
        } else {
            return $this->storeDetalleDesdeDistrito($request, $traslado, $lote, $inicio, $fin, $cantidad);
        }
    }

    private function storeDetalleBodegaDistrito(Request $request, Traslado $traslado, Lote $lote, int $inicio, int $fin, int $cantidad)
    {
        // Rango debe estar contenido en algun bloque del lote
        $rangoValido = LoteRango::where('lote_id', $lote->id)
            ->where('numero_inicio', '<=', $inicio)
            ->where('numero_fin', '>=', $fin)
            ->exists();

        if (!$rangoValido) {
            return back()
                ->withErrors(['numero_inicio' => 'El rango ingresado no pertenece a ningún bloque de este lote.'])
                ->withInput();
        }

        // Overlap con traslados salientes desde bodega (sin contar devoluciones que lo retornaron)
        $overlap = TrasladoDetalle::where('lote_id', $lote->id)
            ->whereHas('traslado', fn($q) => $q->where('tipo', 'bodega_distrito'))
            ->where('numero_inicio', '<=', $fin)
            ->where('numero_fin', '>=', $inicio)
            ->exists();

        if ($overlap) {
            // Permitir si el rango fue devuelto a bodega, aunque haya sido en varias devoluciones parciales
            $devueltos = TrasladoDetalle::where('lote_id', $lote->id)
                ->whereHas('traslado', fn($q) => $q->where('tipo', 'distrito_bodega'))
                ->where('numero_inicio', '<=', $fin)
                ->where('numero_fin', '>=', $inicio)
                ->orderBy('numero_inicio')
                ->get(['numero_inicio', 'numero_fin']);

            if (!$this->rangoCubiertoPor($devueltos, $inicio, $fin)) {
                return back()
                    ->withErrors(['numero_inicio' => 'Ese rango (o parte de él) ya fue transferido a un distrito.'])
                    ->withInput();
            }
        }

        // Stock disponible en bodega (descontando lo enviado y sumando lo devuelto)
        $enviado   = TrasladoDetalle::where('lote_id', $lote->id)
            ->whereHas('traslado', fn($q) => $q->where('tipo', 'bodega_distrito'))->sum('cantidad');
        $devuelto  = TrasladoDetalle::where('lote_id', $lote->id)
            ->whereHas('traslado', fn($q) => $q->where('tipo', 'distrito_bodega'))->sum('cantidad');
        $disponible = $lote->cantidad_total - $enviado + $devuelto;

        if ($cantidad > $disponible) {
            return back()
                ->withErrors(['numero_fin' => "La cantidad solicitada ($cantidad) supera el stock disponible en bodega ($disponible)."])
                ->withInput();
        }

        TrasladoDetalle::create([
            'traslado_id'   => $traslado->id,
            'lote_id'       => $lote->id,
            'numero_inicio' => $inicio,
            'numero_fin'    => $fin,
            'cantidad'      => $cantidad,
        ]);

        return redirect()->route('admin.especies.bodega.traslado.show', $traslado)
            ->with('success_detalle', 'Detalle agregado correctamente.');
    }

    private function storeDetalleDesdeDistrito(Request $request, Traslado $traslado, Lote $lote, int $inicio, int $fin, int $cantidad)
    {
        $origenId = $traslado->origen_distrito_id;

        // El rango debe haber sido recibido en el distrito origen (puede venir de varios traslados parciales)
        $recibidos = TrasladoDetalle::where('lote_id', $lote->id)
            ->whereHas('traslado', fn($q) =>
                $q->whereIn('tipo', ['bodega_distrito', 'distrito_distrito'])
                  ->where('distrito_id', $origenId)
            )
            ->where('numero_inicio', '<=', $fin)
            ->where('numero_fin', '>=', $inicio)
            ->orderBy('numero_inicio')
            ->get(['numero_inicio', 'numero_fin']);

        if (!$this->rangoCubiertoPor($recibidos, $inicio, $fin)) {
            return back()
                ->withErrors(['numero_inicio' => 'El rango no fue trasladado a este distrito o no está disponible.'])
                ->withInput();
        }

        // No debe haber sido ya devuelto o transferido desde el origen
        $yaTransferido = TrasladoDetalle::where('lote_id', $lote->id)
            ->whereHas('traslado', fn($q) =>
                $q->whereIn('tipo', ['distrito_bodega', 'distrito_distrito'])
                  ->where('origen_distrito_id', $origenId)
            )
            ->where('numero_inicio', '<=', $fin)
            ->where('numero_fin', '>=', $inicio)
            ->exists();

        if ($yaTransferido) {
            return back()
                ->withErrors(['numero_inicio' => 'Ese rango ya fue devuelto o transferido desde este distrito.'])
                ->withInput();
        }

        // No debe haber sido realizado en el distrito origen
        $realizado = Realizacion::where('tipo_especie_id', $lote->tipo_especie_id)
            ->where('distrito_id', $origenId)
            ->where('numero_inicio', '<=', $fin)
            ->where('numero_fin', '>=', $inicio)
            ->exists();

        if ($realizado) {
            return back()
                ->withErrors(['numero_inicio' => 'Parte de ese rango ya fue realizado (entregado a contribuyente) en el distrito origen.'])
                ->withInput();
        }

        TrasladoDetalle::create([
            'traslado_id'   => $traslado->id,
            'lote_id'       => $lote->id,
            'numero_inicio' => $inicio,
            'numero_fin'    => $fin,
            'cantidad'      => $cantidad,
        ]);

        return redirect()->route('admin.especies.bodega.traslado.show', $traslado)
            ->with('success_detalle', 'Detalle agregado correctamente.');
    }

    /**
     * Verifica si [$inicio, $fin] queda cubierto por la union de los rangos dados,
     * permitiendo que varios detalles parciales sumen la cobertura completa.
     */
    private function rangoCubiertoPor($detalles, int $inicio, int $fin): bool
    {
        $cubierto = $inicio;
        foreach ($detalles as $d) {
            if ($d->numero_inicio > $cubierto) break;
            $cubierto = max($cubierto, $d->numero_fin + 1);
            if ($cubierto > $fin) return true;
        }
        return $cubierto > $fin;
    }

    public function trasladoDetalleDestroy(Traslado $traslado, TrasladoDetalle $detalle)
    {
        if (\DB::table('nulas')->where('traslado_detalle_id', $detalle->id)->exists()) {
            return back()->with('error_detalle', 'No se puede eliminar: este detalle tiene anulaciones registradas.');
        }

        $detalle->delete();

        return redirect()->route('admin.especies.bodega.traslado.show', $traslado)
            ->with('success_detalle', 'Detalle eliminado correctamente.');
    }

    // ── AJAX: stock en bodega ─────────────────────────────────────────────────

    public function ajaxLotesStock(Request $request)
    {
        $lotes = Lote::where('tipo_especie_id', $request->tipo_especie_id)
            ->with('denominacion', 'compra', 'rangos')
            ->get()
            ->map(function ($lote) {
                // Descontar enviados y sumar devueltos para el stock real en bodega
                $enviados   = TrasladoDetalle::where('lote_id', $lote->id)
                    ->whereHas('traslado', fn($q) => $q->where('tipo', 'bodega_distrito'))->sum('cantidad');
                $devueltos  = TrasladoDetalle::where('lote_id', $lote->id)
                    ->whereHas('traslado', fn($q) => $q->where('tipo', 'distrito_bodega'))->sum('cantidad');
                $disponible = $lote->cantidad_total - $enviados + $devueltos;

                $detalles   = TrasladoDetalle::where('lote_id', $lote->id)
                    ->whereHas('traslado', fn($q) => $q->where('tipo', 'bodega_distrito'))
                    ->orderBy('numero_inicio')
                    ->get(['numero_inicio', 'numero_fin', 'cantidad']);

                return [
                    'id'            => $lote->id,
                    'label'         => 'Factura ' . $lote->compra->numero_factura
                                     . ' — $' . number_format($lote->denominacion->valor, 2)
                                     . ($lote->serie ? ' — Serie ' . $lote->serie : '')
                                     . ' — Stock: ' . number_format($disponible),
                    'disponible'    => $disponible,
                    'rangos'        => $lote->rangos->map(fn($r) => [
                        'inicio' => $r->numero_inicio,
                        'fin'    => $r->numero_fin,
                    ]),
                    'rangos_usados' => $detalles->map(fn($d) => [
                        'inicio' => $d->numero_inicio,
                        'fin'    => $d->numero_fin,
                    ]),
                ];
            })
            ->filter(fn($l) => $l['disponible'] > 0)
            ->values();

        return response()->json($lotes);
    }

    // ── AJAX: stock en un distrito ────────────────────────────────────────────

    public function ajaxLotesDistritoStock(Request $request)
    {
        $distritoId = (int) $request->distrito_id;
        $tipoId     = (int) $request->tipo_especie_id;

        // Rangos que llegaron a este distrito (bodega→distrito o distrito→distrito entrante)
        $recibidos = TrasladoDetalle::whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipoId))
            ->whereHas('traslado', fn($q) =>
                $q->whereIn('tipo', ['bodega_distrito', 'distrito_distrito'])
                  ->where('distrito_id', $distritoId)
            )
            ->with('lote.compra', 'lote.denominacion', 'lote.rangos')
            ->get();

        // Cantidades ya enviadas de vuelta desde este distrito
        $yaEnviados = TrasladoDetalle::whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipoId))
            ->whereHas('traslado', fn($q) =>
                $q->whereIn('tipo', ['distrito_bodega', 'distrito_distrito'])
                  ->where('origen_distrito_id', $distritoId)
            )
            ->selectRaw('lote_id, SUM(cantidad) as total')
            ->groupBy('lote_id')
            ->pluck('total', 'lote_id');

        $lotes = $recibidos->groupBy('lote_id')->map(function ($rows) use ($yaEnviados, $distritoId) {
            $lote       = $rows->first()->lote;
            $recibido   = $rows->sum('cantidad');
            $disponible = $recibido - $yaEnviados->get($lote->id, 0);

            // Rangos recibidos en el distrito
            $rangosRecibidos = $rows->map(fn($d) => [
                'inicio' => $d->numero_inicio,
                'fin'    => $d->numero_fin,
            ])->values();

            // Rangos ya enviados de vuelta (usados)
            $rangosUsados = TrasladoDetalle::where('lote_id', $lote->id)
                ->whereHas('traslado', fn($q) =>
                    $q->whereIn('tipo', ['distrito_bodega', 'distrito_distrito'])
                      ->where('origen_distrito_id', $distritoId)
                )
                ->get(['numero_inicio', 'numero_fin'])
                ->map(fn($d) => ['inicio' => $d->numero_inicio, 'fin' => $d->numero_fin])
                ->values();

            return [
                'id'            => $lote->id,
                'label'         => 'Factura ' . ($lote->compra->numero_factura ?? '—')
                                 . ' — $' . number_format($lote->denominacion->valor ?? 0, 2)
                                 . ($lote->serie ? ' — Serie ' . $lote->serie : '')
                                 . ' — Disponible: ' . number_format($disponible),
                'disponible'    => $disponible,
                'rangos'        => $rangosRecibidos,
                'rangos_usados' => $rangosUsados,
            ];
        })->filter(fn($l) => $l['disponible'] > 0)->values();

        return response()->json($lotes);
    }

    // ── Stock disponible en bodega ────────────────────────────────────────────

    public function stock(Request $request)
    {
        $tipos      = TipoEspecie::where('activo', true)->orderBy('nombre')->get();
        $tipoFiltro = $request->tipo_especie_id;

        $lotes = Lote::with('tipoEspecie', 'denominacion', 'compra', 'rangos')
            ->when($tipoFiltro, fn($q) => $q->where('tipo_especie_id', $tipoFiltro))
            ->orderBy('tipo_especie_id')
            ->orderBy('id')
            ->get()
            ->map(function ($lote) {
                $enviado  = TrasladoDetalle::where('lote_id', $lote->id)
                    ->whereHas('traslado', fn($q) => $q->where('tipo', 'bodega_distrito'))->sum('cantidad');
                $devuelto = TrasladoDetalle::where('lote_id', $lote->id)
                    ->whereHas('traslado', fn($q) => $q->where('tipo', 'distrito_bodega'))->sum('cantidad');

                $lote->stock_trasladado = $enviado - $devuelto;
                $lote->stock_disponible = $lote->cantidad_total - $lote->stock_trasladado;
                return $lote;
            })
            ->filter(fn($l) => $l->cantidad_total > 0);

        return view('frontend.admin.especies.bodega.stock', compact('lotes', 'tipos', 'tipoFiltro'));
    }
}
