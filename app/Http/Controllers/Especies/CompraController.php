<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;
use App\Models\Compra;
use App\Models\Denominacion;
use App\Models\Lote;
use App\Models\LoteRango;
use App\Models\TipoEspecie;
use App\Services\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CompraController extends Controller
{
    public function historial()
    {
        $compras = Compra::withCount('lotes')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(25);

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
            'fecha'          => 'required|date|before_or_equal:today',
            'observaciones'  => 'nullable|string|max:500',
        ], [
            'numero_factura.required' => 'El número de factura es obligatorio.',
            'numero_factura.unique'   => 'Ya existe una compra con ese número de factura.',
            'fecha.required'          => 'La fecha es obligatoria.',
            'fecha.before_or_equal'   => 'La fecha no puede ser posterior a hoy.',
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
            'rangos.*.numero_inicio'   => 'required|integer|min:1|max:999999999',
            'rangos.*.numero_fin'      => 'required|integer|min:1|max:999999999',
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

        // Reindexar: al quitar filas en el formulario los indices llegan con huecos (0, 2, 3...)
        $rangos = array_values($request->rangos);

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

        // El correlativo es unico por tipo y serie: la misma numeracion se repite entre series distintas
        $tipoId = (int) $request->tipo_especie_id;
        $serie  = Inventario::serie($request->serie);

        DB::transaction(function () use ($request, $compra, $rangos, $tipoId, $serie) {
            Inventario::bloquear($tipoId);

            foreach ($rangos as $rango) {
                $inicio = (int) $rango['numero_inicio'];
                $fin    = (int) $rango['numero_fin'];

                $overlap = LoteRango::whereHas('lote', fn($q) => $q->where('tipo_especie_id', $tipoId)
                        ->where(fn($q2) => $serie === null ? $q2->whereNull('serie') : $q2->where('serie', $serie)))
                    ->where('numero_inicio', '<=', $fin)
                    ->where('numero_fin', '>=', $inicio)
                    ->exists();

                if ($overlap) {
                    throw ValidationException::withMessages(['rangos' => "El rango {$inicio}–{$fin} ya existe en otro lote del mismo tipo"
                        . ($serie ? " y serie «{$serie}»" : ' sin serie') . '.']);
                }
            }

            $lote = Lote::create([
                'compra_id'       => $compra->id,
                'tipo_especie_id' => $tipoId,
                'denominacion_id' => $request->denominacion_id,
                'serie'           => $serie,
                'cantidad_total'  => collect($rangos)->sum(fn($r) => (int) $r['numero_fin'] - (int) $r['numero_inicio'] + 1),
            ]);

            foreach ($rangos as $rango) {
                LoteRango::create([
                    'lote_id'       => $lote->id,
                    'numero_inicio' => (int) $rango['numero_inicio'],
                    'numero_fin'    => (int) $rango['numero_fin'],
                ]);
            }

            $this->recalcularMonto($compra);
        });

        return redirect()->route('admin.especies.compras.show', $compra)
            ->with('success_lote', 'Lote agregado correctamente.');
    }

    public function update(Request $request, Compra $compra)
    {
        $request->validate([
            'numero_factura' => 'required|string|max:50|unique:compras,numero_factura,' . $compra->id,
            'fecha'          => 'required|date|before_or_equal:today',
            'observaciones'  => 'nullable|string|max:500',
        ], [
            'numero_factura.required' => 'El número de factura es obligatorio.',
            'numero_factura.unique'   => 'Ya existe una compra con ese número de factura.',
            'fecha.required'          => 'La fecha es obligatoria.',
            'fecha.before_or_equal'   => 'La fecha no puede ser posterior a hoy.',
        ]);

        // Mover la fecha de compra no puede dejar envios de bodega anteriores a la compra
        try {
            DB::transaction(function () use ($request, $compra) {
                Inventario::bloquear(...$compra->lotes()->distinct()->pluck('tipo_especie_id')->all());

                $compra->update([
                    'numero_factura' => $request->numero_factura,
                    'fecha'          => $request->fecha,
                    'observaciones'  => $request->observaciones,
                ]);

                Inventario::asegurar($compra->lotes()->pluck('id')->map(fn($id) => [null, $id])->all(), 'fecha');
            });
        } catch (ValidationException $e) {
            return back()->with('error', 'No se guardó el cambio: con esa fecha, ' . lcfirst(collect($e->errors())->flatten()->first()));
        }

        return redirect()->route('admin.especies.compras.historial')
            ->with('success', 'Compra actualizada correctamente.');
    }

    public function destroy(Compra $compra)
    {
        $eliminada = DB::transaction(function () use ($compra) {
            Inventario::bloquear(...$compra->lotes()->distinct()->pluck('tipo_especie_id')->all());

            if (DB::table('traslado_detalles')->whereIn('lote_id', $compra->lotes()->pluck('id'))->exists()) {
                return false;
            }

            foreach ($compra->lotes as $lote) {
                $lote->rangos()->delete();
                $lote->delete();
            }
            $compra->delete();
            return true;
        });

        if (!$eliminada) {
            return back()->with('error', 'No se puede eliminar: la compra tiene lotes con traslados asociados.');
        }

        return redirect()->route('admin.especies.compras.historial')
            ->with('success', 'Compra eliminada correctamente.');
    }

    public function destroyLote(Compra $compra, Lote $lote)
    {
        abort_if($lote->compra_id != $compra->id, 404);

        $eliminado = DB::transaction(function () use ($compra, $lote) {
            Inventario::bloquear($lote->tipo_especie_id);

            if (DB::table('traslado_detalles')->where('lote_id', $lote->id)->exists()) {
                return false;
            }

            $lote->rangos()->delete();
            $lote->delete();
            $this->recalcularMonto($compra);
            return true;
        });

        if (!$eliminado) {
            return back()->with('error_lote', 'No se puede eliminar: el lote ya tiene traslados asociados.');
        }

        return redirect()->route('admin.especies.compras.show', $compra)
            ->with('success_lote', 'Lote eliminado correctamente.');
    }

    private function recalcularMonto(Compra $compra): void
    {
        $compra->load('lotes.denominacion');
        $compra->update(['monto_total' => $compra->lotes->sum(fn($l) => $l->cantidad_total * $l->denominacion->valor)]);
    }
}
