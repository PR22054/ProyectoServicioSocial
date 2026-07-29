<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;
use App\Models\Distrito;
use App\Models\Lote;
use App\Models\LoteRango;
use App\Models\TipoEspecie;
use App\Models\Traslado;
use App\Models\TrasladoDetalle;
use Illuminate\Http\Request;

class BodegaController extends Controller
{
    // ── Traslados ─────────────────────────────────────────────────────────────

    public function trasladoHistorial()
    {
        $traslados = Traslado::with('distrito', 'usuario')
            ->withCount('detalles')
            ->withSum('detalles', 'cantidad')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get();

        return view('frontend.admin.especies.bodega.traslados.historial', compact('traslados'));
    }

    public function trasladoCrear()
    {
        $distritos = Distrito::where('activo', true)->orderBy('codigo')->get();
        return view('frontend.admin.especies.bodega.traslados.crear', compact('distritos'));
    }

    public function trasladoStore(Request $request)
    {
        $request->validate([
            'distrito_id'  => 'required|exists:distritos,id',
            'fecha'        => 'required|date',
            'observaciones' => 'nullable|string|max:500',
        ], [
            'distrito_id.required' => 'Seleccione un distrito destino.',
            'fecha.required'       => 'La fecha es obligatoria.',
        ]);

        $traslado = Traslado::create([
            'distrito_id'  => $request->distrito_id,
            'fecha'        => $request->fecha,
            'observaciones' => $request->observaciones,
            'usuario_id'   => auth()->id(),
        ]);

        return redirect()->route('admin.especies.bodega.traslado.show', $traslado)
            ->with('success', 'Traslado creado. Ahora agregue los detalles.');
    }

    public function trasladoShow(Traslado $traslado)
    {
        $traslado->load(
            'distrito',
            'usuario',
            'detalles.lote.tipoEspecie',
            'detalles.lote.denominacion'
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

        // El rango debe estar contenido en alguno de los rangos del lote
        $rangoValido = LoteRango::where('lote_id', $lote->id)
            ->where('numero_inicio', '<=', $inicio)
            ->where('numero_fin', '>=', $fin)
            ->exists();

        if (!$rangoValido) {
            return back()
                ->withErrors(['numero_inicio' => 'El rango ingresado no pertenece a ningún bloque de este lote.'])
                ->withInput();
        }

        // Sin solapamiento con traslados ya registrados para este lote
        $overlap = TrasladoDetalle::where('lote_id', $lote->id)
            ->where('numero_inicio', '<=', $fin)
            ->where('numero_fin', '>=', $inicio)
            ->exists();

        if ($overlap) {
            return back()
                ->withErrors(['numero_inicio' => 'Ese rango (o parte de él) ya fue incluido en otro traslado.'])
                ->withInput();
        }

        // Stock disponible
        $trasladado = TrasladoDetalle::where('lote_id', $lote->id)->sum('cantidad');
        $disponible = $lote->cantidad_total - $trasladado;

        if ($cantidad > $disponible) {
            return back()
                ->withErrors(['numero_fin' => "La cantidad solicitada ($cantidad) supera el stock disponible ($disponible)." ])
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

    public function trasladoDetalleDestroy(Traslado $traslado, TrasladoDetalle $detalle)
    {
        // Verificar si hay anulaciones que afecten este rango del lote
        $tieneNulas = \DB::table('nulas')
            ->where('lote_id', $detalle->lote_id)
            ->where('numero_inicio', '<=', $detalle->numero_fin)
            ->where('numero_fin', '>=', $detalle->numero_inicio)
            ->exists();

        if ($tieneNulas) {
            return back()->with('error_detalle', 'No se puede eliminar: existen anulaciones registradas en este rango.');
        }

        $detalle->delete();

        return redirect()->route('admin.especies.bodega.traslado.show', $traslado)
            ->with('success_detalle', 'Detalle eliminado correctamente.');
    }

    // ── AJAX ─────────────────────────────────────────────────────────────────

    public function ajaxLotesStock(Request $request)
    {
        $lotes = Lote::where('tipo_especie_id', $request->tipo_especie_id)
            ->with('denominacion', 'compra', 'rangos')
            ->get()
            ->map(function ($lote) {
                $trasladado = TrasladoDetalle::where('lote_id', $lote->id)->sum('cantidad');
                $disponible = $lote->cantidad_total - $trasladado;
                return [
                    'id'          => $lote->id,
                    'label'       => 'Factura ' . $lote->compra->numero_factura
                                   . ' — $' . number_format($lote->denominacion->valor, 2)
                                   . ($lote->serie ? ' — Serie ' . $lote->serie : '')
                                   . ' — Stock: ' . number_format($disponible),
                    'disponible'  => $disponible,
                    'rangos'      => $lote->rangos->map(fn($r) => [
                        'inicio' => $r->numero_inicio,
                        'fin'    => $r->numero_fin,
                    ]),
                ];
            })
            ->filter(fn($l) => $l['disponible'] > 0)
            ->values();

        return response()->json($lotes);
    }

    // ── Stock disponible en bodega ────────────────────────────────────────────

    public function stock(Request $request)
    {
        $tipos         = TipoEspecie::where('activo', true)->orderBy('nombre')->get();
        $tipoFiltro    = $request->tipo_especie_id;

        $query = Lote::with('tipoEspecie', 'denominacion', 'compra', 'rangos')
            ->when($tipoFiltro, fn($q) => $q->where('tipo_especie_id', $tipoFiltro))
            ->orderBy('tipo_especie_id')
            ->orderBy('id');

        $lotes = $query->get()->map(function ($lote) {
            $trasladado = TrasladoDetalle::where('lote_id', $lote->id)->sum('cantidad');
            $lote->stock_trasladado = $trasladado;
            $lote->stock_disponible = $lote->cantidad_total - $trasladado;
            return $lote;
        })->filter(fn($l) => $l->cantidad_total > 0);

        return view('frontend.admin.especies.bodega.stock', compact('lotes', 'tipos', 'tipoFiltro'));
    }
}
