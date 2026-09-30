<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;
use App\Models\Distrito;
use App\Models\Lote;
use App\Models\TipoEspecie;
use App\Models\Traslado;
use App\Models\TrasladoDetalle;
use App\Services\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
            ->paginate(25);

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
            'fecha'         => 'required|date|before_or_equal:today',
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
            'fecha.before_or_equal'       => 'La fecha no puede ser posterior a hoy.',
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
            'fecha'         => 'required|date|before_or_equal:today',
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
            'fecha.before_or_equal'       => 'La fecha no puede ser posterior a hoy.',
        ]);

        $data = ['fecha' => $request->fecha, 'observaciones' => $request->observaciones];

        if (in_array($tipo, ['bodega_distrito', 'distrito_distrito'])) {
            $data['distrito_id'] = $request->distrito_id;
        }
        if (in_array($tipo, ['distrito_bodega', 'distrito_distrito'])) {
            $data['origen_distrito_id'] = $request->origen_distrito_id;
        }

        // Cambiar fecha o distritos mueve documentos en el tiempo: se revisan los poseedores de antes y de despues
        try {
            DB::transaction(function () use ($traslado, $data) {
                Inventario::bloquear(...$this->tipos($traslado));
                $antes = $this->pares($traslado);
                $traslado->update($data);
                Inventario::asegurar(array_merge($antes, $this->pares($traslado)), 'fecha');
            });
        } catch (ValidationException $e) {
            return back()->with('error', 'No se guardó el cambio: con esa modificación, ' . lcfirst(collect($e->errors())->flatten()->first()));
        }

        return redirect()->route('admin.especies.bodega.traslado.historial')
            ->with('success', 'Traslado actualizado correctamente.');
    }

    public function trasladoDestroy(Traslado $traslado)
    {
        $tieneNulas = DB::table('nulas')
            ->whereIn('traslado_detalle_id', $traslado->detalles()->pluck('id'))
            ->exists();

        if ($tieneNulas) {
            return back()->with('error', 'No se puede eliminar: el traslado tiene anulaciones registradas.');
        }

        try {
            DB::transaction(function () use ($traslado) {
                Inventario::bloquear(...$this->tipos($traslado));
                $pares = $this->pares($traslado);
                $traslado->detalles()->delete();
                $traslado->delete();
                Inventario::asegurar($pares);
            });
        } catch (ValidationException $e) {
            return back()->with('error', 'No se puede eliminar: sin este traslado, ' . lcfirst(collect($e->errors())->flatten()->first()));
        }

        return redirect()->route('admin.especies.bodega.traslado.historial')
            ->with('success', 'Traslado eliminado correctamente.');
    }

    // Poseedores que toca el traslado por cada lote: origen y destino (null = bodega)
    private function pares(Traslado $traslado): array
    {
        return $traslado->detalles()->pluck('lote_id')->unique()
            ->flatMap(fn($lote) => [[$traslado->origen_distrito_id, $lote], [$traslado->distrito_id, $lote]])
            ->all();
    }

    private function tipos(Traslado $traslado): array
    {
        return $traslado->detalles()->join('lotes', 'lotes.id', '=', 'traslado_detalles.lote_id')
            ->distinct()->pluck('lotes.tipo_especie_id')->all();
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
            'numero_inicio' => 'required|integer|min:1|max:999999999',
            'numero_fin'    => 'required|integer|min:1|max:999999999|gte:numero_inicio',
        ], [
            'lote_id.required'       => 'Seleccione un lote.',
            'numero_inicio.required' => 'El número de inicio es obligatorio.',
            'numero_fin.required'    => 'El número de fin es obligatorio.',
            'numero_fin.gte'         => 'El número fin debe ser mayor o igual al inicio.',
        ]);

        $inicio = (int) $request->numero_inicio;
        $fin    = (int) $request->numero_fin;
        $lote   = Lote::with('rangos')->findOrFail($request->lote_id);

        $propios = $lote->rangos->map(fn($r) => [$r->numero_inicio, $r->numero_fin])->all();
        if (!Inventario::contiene($propios, $inicio, $fin)) {
            return back()
                ->withErrors(['numero_inicio' => 'El rango ingresado no pertenece a este lote.'])
                ->withInput();
        }

        DB::transaction(function () use ($traslado, $lote, $inicio, $fin) {
            Inventario::bloquear($lote->tipo_especie_id);

            TrasladoDetalle::create([
                'traslado_id'   => $traslado->id,
                'lote_id'       => $lote->id,
                'numero_inicio' => $inicio,
                'numero_fin'    => $fin,
                'cantidad'      => $fin - $inicio + 1,
            ]);

            // El origen debia tener esos documentos a la fecha del traslado y el destino no tenerlos ya
            Inventario::asegurar([[$traslado->origen_distrito_id, $lote->id], [$traslado->distrito_id, $lote->id]]);
        });

        return redirect()->route('admin.especies.bodega.traslado.show', $traslado)
            ->with('success_detalle', 'Detalle agregado correctamente.');
    }

    public function trasladoDetalleDestroy(Traslado $traslado, TrasladoDetalle $detalle)
    {
        abort_if($detalle->traslado_id != $traslado->id, 404);

        if (DB::table('nulas')->where('traslado_detalle_id', $detalle->id)->exists()) {
            return back()->with('error_detalle', 'No se puede eliminar: este detalle tiene anulaciones registradas.');
        }

        try {
            DB::transaction(function () use ($traslado, $detalle) {
                Inventario::bloquear($detalle->lote->tipo_especie_id);
                $detalle->delete();
                Inventario::asegurar([[$traslado->origen_distrito_id, $detalle->lote_id], [$traslado->distrito_id, $detalle->lote_id]]);
            });
        } catch (ValidationException $e) {
            return back()->with('error_detalle', 'No se puede eliminar: sin este detalle, ' . lcfirst(collect($e->errors())->flatten()->first()));
        }

        return redirect()->route('admin.especies.bodega.traslado.show', $traslado)
            ->with('success_detalle', 'Detalle eliminado correctamente.');
    }

    // ── AJAX: stock en bodega ─────────────────────────────────────────────────

    public function ajaxLotesStock(Request $request)
    {
        return response()->json($this->opcionesLotes(
            Inventario::bodega((int) $request->tipo_especie_id), 'Stock'));
    }

    // ── AJAX: stock en un distrito ────────────────────────────────────────────

    public function ajaxLotesDistritoStock(Request $request)
    {
        return response()->json($this->opcionesLotes(
            Inventario::distrito((int) $request->distrito_id, (int) $request->tipo_especie_id), 'Disponible'));
    }

    // Lotes con existencia para el selector del detalle, con los rangos que realmente quedan
    private function opcionesLotes($existencias, string $rotulo)
    {
        return $existencias->filter(fn($r) => $r['cantidad'] > 0)
            ->map(fn($r) => [
                'id'         => $r['lote']->id,
                'label'      => 'Factura ' . ($r['lote']->compra->numero_factura ?? '—')
                              . ' — $' . number_format($r['valor'], 2)
                              . ($r['lote']->serie ? ' — Serie ' . $r['lote']->serie : '')
                              . " — {$rotulo}: " . number_format($r['cantidad']),
                'disponible' => $r['cantidad'],
                'rangos'     => array_map(fn($i) => ['inicio' => $i[0], 'fin' => $i[1]], $r['intervalos']),
            ])
            ->values();
    }

    // ── Stock disponible en bodega ────────────────────────────────────────────

    public function stock(Request $request)
    {
        $tipos      = TipoEspecie::where('activo', true)->orderBy('nombre')->get();
        $tipoFiltro = $request->tipo_especie_id;

        $lotes = Inventario::bodega($tipoFiltro ? (int) $tipoFiltro : null)
            ->map(function ($r) {
                $lote = $r['lote'];
                $lote->stock_trasladado = $r['enviado'] - $r['devuelto'];
                $lote->stock_disponible = $r['cantidad'];
                return $lote;
            })
            ->filter(fn($l) => $l->cantidad_total > 0);

        return view('frontend.admin.especies.bodega.stock', compact('lotes', 'tipos', 'tipoFiltro'));
    }
}
