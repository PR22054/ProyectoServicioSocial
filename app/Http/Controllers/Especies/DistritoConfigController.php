<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;
use App\Models\Denominacion;
use App\Models\Distrito;
use App\Models\DistritoDenominacion;
use App\Models\DistritoTipoEspecie;
use App\Models\Realizacion;
use App\Models\TipoEspecie;
use App\Services\Tarifas;
use Carbon\Carbon;
use Illuminate\Http\Request;

//como lleva cada distrito su libro de especies y sus reportes: rotulos y tarifas
class DistritoConfigController extends Controller
{
    //pantalla de configuracion del distrito elegido: datos, tipos y tarifas
    public function index(Request $request)
    {
        $distritos = Distrito::orderBy('codigo')->get();
        $distrito  = $distritos->firstWhere('id', (int) $request->distrito_id) ?? $distritos->first();
        abort_unless($distrito, 404);

        $tipos          = TipoEspecie::orderBy('orden')->orderBy('nombre')->get();
        $denominaciones = Denominacion::with('tipoEspecie')->orderBy('tipo_especie_id')->orderBy('valor')->get();
        $config         = DistritoTipoEspecie::with('tipoEspecie')->where('distrito_id', $distrito->id)->orderBy('orden')->get();
        $tarifas        = DistritoDenominacion::with('denominacion.tipoEspecie')->where('distrito_id', $distrito->id)->get()
            ->sortBy(fn($t) => [$config->firstWhere('tipo_especie_id', $t->denominacion->tipo_especie_id)->orden ?? 999,
                                $t->denominacion->tipo_especie_id, $t->orden])
            ->values();

        return view('frontend.admin.especies.configuracion.distritos', compact(
            'distritos', 'distrito', 'tipos', 'denominaciones', 'config', 'tarifas'));
    }

    //guarda el nombre oficial, el lugar de la firma y si el saldo del libro se ordena por valor
    public function update(Request $request, Distrito $distrito)
    {
        $datos = $request->validate([
            'nombre_oficial' => 'nullable|string|max:150',
            'lugar_firma'    => 'nullable|string|max:150',
        ]);
        $distrito->update(array_merge($datos, ['saldo_por_valor' => $request->boolean('saldo_por_valor')]));

        return back()->with('success', 'Datos del distrito guardados.');
    }

    //agrega o actualiza los titulos, el orden y la regla de nulas de un tipo en el distrito
    public function guardarTipo(Request $request, Distrito $distrito)
    {
        $datos = $request->validate([
            'tipo_especie_id' => 'required|exists:tipo_especies,id',
            'titulo_libro'    => 'nullable|string|max:150',
            'titulo_reporte'  => 'nullable|string|max:150',
            'titulo_realizaciones' => 'nullable|string|max:150',
            'orden'           => 'nullable|integer|min:0|max:999',
        ], ['tipo_especie_id.required' => 'Seleccione un tipo de especie.']);

        DistritoTipoEspecie::updateOrCreate(
            ['distrito_id' => $distrito->id, 'tipo_especie_id' => $datos['tipo_especie_id']],
            ['titulo_libro' => $datos['titulo_libro'] ?? null, 'titulo_reporte' => $datos['titulo_reporte'] ?? null,
             'titulo_realizaciones' => $datos['titulo_realizaciones'] ?? null, 'orden' => (int) ($datos['orden'] ?? 0), 'nulas_en_realizaciones' => $request->boolean('nulas_en_realizaciones')]);

        return back()->with('success', 'Tipo guardado.');
    }

    //quita la configuracion de un tipo en el distrito
    public function quitarTipo(DistritoTipoEspecie $config)
    {
        $config->delete();
        return back()->with('success', 'El tipo ya no tiene rótulos propios en este distrito.');
    }

    //agrega o actualiza la tarifa de una denominacion en el distrito; si cambia el precio de venta puede recalcular el monto cobrado
    public function guardarTarifa(Request $request, Distrito $distrito)
    {
        $datos = $request->validate([
            'denominacion_id'   => 'required|exists:denominaciones,id',
            'descripcion'       => 'nullable|string|max:150',
            'precio_costo'      => 'nullable|numeric|min:0',
            'precio_venta'      => 'nullable|numeric|min:0',
            'orden'             => 'nullable|integer|min:0|max:999',
            'descripcion_nulas' => 'nullable|string|max:150',
            'orden_nulas'       => 'nullable|integer|min:0|max:999',
            'descripcion_realizaciones' => 'nullable|string|max:150',
            'orden_realizaciones'       => 'nullable|integer|min:0|max:999',
            'nulas_realizaciones'       => 'nullable|string|max:150',
            'orden_nulas_realizaciones' => 'nullable|integer|min:0|max:999',
            'aplicar_desde'             => 'nullable|date',
        ], ['denominacion_id.required' => 'Seleccione una denominación.', 'aplicar_desde.date' => 'La fecha para recalcular no es válida.']);

        DistritoDenominacion::updateOrCreate(
            ['distrito_id' => $distrito->id, 'denominacion_id' => $datos['denominacion_id']],
            ['descripcion'       => $datos['descripcion'] ?? null,
             'precio_costo'      => $datos['precio_costo'] ?? null,
             'precio_venta'      => $datos['precio_venta'] ?? null,
             'orden'             => (int) ($datos['orden'] ?? 0),
             'descripcion_nulas' => $datos['descripcion_nulas'] ?? null,
             'orden_nulas'       => $datos['orden_nulas'] ?? null,
             'nulas_con_venta'   => $request->boolean('nulas_con_venta'),
             'descripcion_realizaciones' => $datos['descripcion_realizaciones'] ?? null,
             'orden_realizaciones'       => $datos['orden_realizaciones'] ?? null,
             'nulas_realizaciones'       => $datos['nulas_realizaciones'] ?? null,
             'orden_nulas_realizaciones' => $datos['orden_nulas_realizaciones'] ?? null]);

        if (empty($datos['aplicar_desde'])) {
            return back()->with('success', 'Tarifa guardada. Las realizaciones nuevas se cobran con el precio de venta de la tarifa.');
        }

        //cambio de precio de venta: el monto cobrado de las realizaciones desde esa fecha se recalcula con el precio nuevo
        $desde  = Carbon::parse($datos['aplicar_desde'])->startOfDay();
        $precio = Tarifas::precioVenta($distrito->id, Denominacion::findOrFail($datos['denominacion_id'])) ?? 0;
        $realizaciones = Realizacion::where('distrito_id', $distrito->id)->where('denominacion_id', $datos['denominacion_id'])
            ->where('fecha', '>=', $desde)->get();
        foreach ($realizaciones as $r) {
            $r->update(['monto_cobrado' => round($r->cantidad * $precio, 2)]);
        }

        return back()->with('success', "Tarifa guardada. Se recalculó el monto cobrado de {$realizaciones->count()} realización(es) "
            . "desde el {$desde->format('d/m/Y')} con el precio de venta $" . number_format($precio, 2) . '.');
    }

    //quita la tarifa del distrito; se vuelven a usar los valores generales de la denominacion
    public function quitarTarifa(DistritoDenominacion $tarifa)
    {
        $tarifa->delete();
        return back()->with('success', 'Tarifa quitada: se usarán los valores generales de la denominación.');
    }
}
