<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\Denominacion;
use App\Models\Lote;
use App\Models\LoteRango;
use App\Models\TipoEspecie;
use Illuminate\Http\Request;

class CompraController extends Controller
{
    public function historial()
    {
        $compras = Compra::withCount('lotes')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->get();

        return view('frontend.admin.especies.compras.historial', compact('compras'));
    }

    public function crear()
    {
        return view('frontend.admin.especies.compras.crear');
    }

    public function store(Request $request)
    {
        $request->validate([
            'numero_factura' => 'required|string|max:50|unique:compras,numero_factura',
            'fecha'          => 'required|date',
            'observaciones'  => 'nullable|string|max:500',
        ], [
            'numero_factura.required' => 'El número de factura es obligatorio.',
            'numero_factura.unique'   => 'Ya existe una compra con ese número de factura.',
            'fecha.required'          => 'La fecha es obligatoria.',
        ]);

        $compra = Compra::create([
            'numero_factura' => $request->numero_factura,
            'fecha'          => $request->fecha,
            'observaciones'  => $request->observaciones,
            'monto_total'    => 0,
            'user_id'        => auth()->id(),
        ]);

        return redirect()->route('admin.especies.compras.show', $compra)
            ->with('success', 'Compra creada. Ahora agregue los lotes.');
    }

    public function show(Compra $compra)
    {
        $compra->load('lotes.tipoEspecie', 'lotes.denominacion', 'lotes.rangos', 'user');
        return view('frontend.admin.especies.compras.show', compact('compra'));
    }

    public function crearLote(Compra $compra)
    {
        $tipos = TipoEspecie::where('activo', true)->orderBy('nombre')->get();
        return view('frontend.admin.especies.compras.lotes.crear', compact('compra', 'tipos'));
    }

    public function storeLote(Request $request, Compra $compra)
    {
        $request->validate([
            'tipo_especie_id'          => 'required|exists:tipo_especies,id',
            'denominacion_id'          => 'required|exists:denominaciones,id',
            'serie'                    => 'nullable|string|max:10',
            'rangos'                   => 'required|array|min:1',
            'rangos.*.numero_inicio'   => 'required|integer|min:1',
            'rangos.*.numero_fin'      => 'required|integer|min:1',
        ], [
            'tipo_especie_id.required' => 'Seleccione un tipo de especie.',
            'denominacion_id.required' => 'Seleccione una denominación.',
            'rangos.required'          => 'Agregue al menos un rango.',
            'rangos.*.numero_inicio.required' => 'El número de inicio es obligatorio.',
            'rangos.*.numero_fin.required'    => 'El número de fin es obligatorio.',
        ]);

        // Denominacion pertenece al tipo
        $denominacion = Denominacion::find($request->denominacion_id);
        if ($denominacion->tipo_especie_id != $request->tipo_especie_id) {
            return back()->withErrors(['denominacion_id' => 'La denominación no pertenece al tipo seleccionado.'])->withInput();
        }

        $rangos = $request->rangos;

        // Inicio <= Fin en cada rango
        foreach ($rangos as $i => $rango) {
            if ((int) $rango['numero_inicio'] > (int) $rango['numero_fin']) {
                return back()
                    ->withErrors(["rangos.$i.numero_fin" => "Rango " . ($i + 1) . ": el número fin debe ser mayor al inicio."])
                    ->withInput();
            }
        }

        // Sin solapamiento entre rangos del mismo envío
        for ($i = 0; $i < count($rangos); $i++) {
            for ($j = $i + 1; $j < count($rangos); $j++) {
                if ((int)$rangos[$i]['numero_inicio'] <= (int)$rangos[$j]['numero_fin'] &&
                    (int)$rangos[$i]['numero_fin']    >= (int)$rangos[$j]['numero_inicio']) {
                    return back()
                        ->withErrors(['rangos' => 'Hay rangos que se solapan entre sí en este lote.'])
                        ->withInput();
                }
            }
        }

        // Sin solapamiento con lote_rangos existentes del mismo tipo
        $tipoId = $request->tipo_especie_id;
        foreach ($rangos as $rango) {
            $inicio = (int) $rango['numero_inicio'];
            $fin    = (int) $rango['numero_fin'];

            $overlap = LoteRango::whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipoId))
                ->where('numero_inicio', '<=', $fin)
                ->where('numero_fin', '>=', $inicio)
                ->exists();

            if ($overlap) {
                return back()
                    ->withErrors(['rangos' => "El rango $inicio–$fin se solapa con un lote existente del mismo tipo de especie."])
                    ->withInput();
            }
        }

        $cantidadTotal = collect($rangos)->sum(
            fn($r) => (int)$r['numero_fin'] - (int)$r['numero_inicio'] + 1
        );

        $lote = Lote::create([
            'compra_id'       => $compra->id,
            'tipo_especie_id' => $request->tipo_especie_id,
            'denominacion_id' => $request->denominacion_id,
            'serie'           => $request->serie,
            'cantidad_total'  => $cantidadTotal,
        ]);

        foreach ($rangos as $rango) {
            LoteRango::create([
                'lote_id'       => $lote->id,
                'numero_inicio' => (int) $rango['numero_inicio'],
                'numero_fin'    => (int) $rango['numero_fin'],
            ]);
        }

        // Recalcular monto total de la compra
        $compra->load('lotes.denominacion');
        $monto = $compra->lotes->sum(fn($l) => $l->cantidad_total * $l->denominacion->valor);
        $compra->update(['monto_total' => $monto]);

        return redirect()->route('admin.especies.compras.show', $compra)
            ->with('success_lote', 'Lote agregado correctamente.');
    }

    public function destroyLote(Compra $compra, Lote $lote)
    {
        if (\DB::table('traslado_detalles')->where('lote_id', $lote->id)->exists()) {
            return back()->with('error_lote', 'No se puede eliminar: el lote ya tiene traslados asociados.');
        }

        $lote->rangos()->delete();
        $lote->delete();

        // Recalcular monto
        $compra->load('lotes.denominacion');
        $monto = $compra->lotes->sum(fn($l) => $l->cantidad_total * $l->denominacion->valor);
        $compra->update(['monto_total' => $monto]);

        return redirect()->route('admin.especies.compras.show', $compra)
            ->with('success_lote', 'Lote eliminado correctamente.');
    }
}
