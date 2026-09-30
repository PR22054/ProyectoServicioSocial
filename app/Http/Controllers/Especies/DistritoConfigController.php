<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;
use App\Models\Denominacion;
use App\Models\Distrito;
use App\Models\DistritoDenominacion;
use App\Models\DistritoTipoEspecie;
use App\Models\TipoEspecie;
use App\Models\Ubicacion;
use App\Models\UbicacionRango;
use App\Services\Inventario;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

// Como lleva cada distrito su libro de especies y su reporte mensual: rotulos, tarifas y cajas
class DistritoConfigController extends Controller
{
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
        $cajas          = Ubicacion::withCount('rangos')->where('distrito_id', $distrito->id)->orderBy('orden')->get();
        $asignaciones   = UbicacionRango::with('ubicacion', 'tipoEspecie', 'usuario')->whereIn('ubicacion_id', $cajas->pluck('id'))
            ->orderByDesc('fecha')->orderByDesc('id')->paginate(20)->withQueryString();

        return view('frontend.admin.especies.configuracion.distritos', compact(
            'distritos', 'distrito', 'tipos', 'denominaciones', 'config', 'tarifas', 'cajas', 'asignaciones'));
    }

    public function update(Request $request, Distrito $distrito)
    {
        $datos = $request->validate([
            'nombre_oficial' => 'nullable|string|max:150',
            'lugar_firma'    => 'nullable|string|max:150',
        ]);
        $distrito->update(array_merge($datos, ['saldo_por_valor' => $request->boolean('saldo_por_valor')]));

        return back()->with('success', 'Datos del distrito guardados.');
    }

    public function guardarTipo(Request $request, Distrito $distrito)
    {
        $datos = $request->validate([
            'tipo_especie_id' => 'required|exists:tipo_especies,id',
            'titulo_libro'    => 'nullable|string|max:150',
            'titulo_reporte'  => 'nullable|string|max:150',
            'orden'           => 'nullable|integer|min:0|max:999',
        ], ['tipo_especie_id.required' => 'Seleccione un tipo de especie.']);

        DistritoTipoEspecie::updateOrCreate(
            ['distrito_id' => $distrito->id, 'tipo_especie_id' => $datos['tipo_especie_id']],
            ['titulo_libro' => $datos['titulo_libro'] ?? null, 'titulo_reporte' => $datos['titulo_reporte'] ?? null,
             'orden' => (int) ($datos['orden'] ?? 0), 'nulas_en_realizaciones' => $request->boolean('nulas_en_realizaciones')]);

        return back()->with('success', 'Tipo guardado.');
    }

    public function quitarTipo(DistritoTipoEspecie $config)
    {
        $config->delete();
        return back()->with('success', 'El tipo ya no tiene rótulos propios en este distrito.');
    }

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
        ], ['denominacion_id.required' => 'Seleccione una denominación.']);

        DistritoDenominacion::updateOrCreate(
            ['distrito_id' => $distrito->id, 'denominacion_id' => $datos['denominacion_id']],
            ['descripcion'       => $datos['descripcion'] ?? null,
             'precio_costo'      => $datos['precio_costo'] ?? null,
             'precio_venta'      => $datos['precio_venta'] ?? null,
             'orden'             => (int) ($datos['orden'] ?? 0),
             'descripcion_nulas' => $datos['descripcion_nulas'] ?? null,
             'orden_nulas'       => $datos['orden_nulas'] ?? null,
             'nulas_con_venta'   => $request->boolean('nulas_con_venta')]);

        return back()->with('success', 'Tarifa guardada. Las realizaciones nuevas se cobran con el precio de venta de la tarifa.');
    }

    public function quitarTarifa(DistritoDenominacion $tarifa)
    {
        $tarifa->delete();
        return back()->with('success', 'Tarifa quitada: se usarán los valores generales de la denominación.');
    }

    public function guardarCaja(Request $request, Distrito $distrito)
    {
        $request->merge(['nombre' => mb_strtoupper(trim((string) $request->nombre))]);
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:60', Rule::unique('ubicaciones')->where('distrito_id', $distrito->id)],
            'orden'  => 'nullable|integer|min:0|max:999',
        ], ['nombre.required' => 'El nombre de la caja es obligatorio.', 'nombre.unique' => 'El distrito ya tiene una caja con ese nombre.']);

        Ubicacion::create(['distrito_id' => $distrito->id, 'nombre' => $datos['nombre'], 'orden' => (int) ($datos['orden'] ?? 0)]);

        return back()->with('success', 'Caja agregada.');
    }

    public function quitarCaja(Ubicacion $ubicacion)
    {
        if ($ubicacion->rangos()->exists()) {
            return back()->with('error', 'La caja tiene rangos asignados; quite primero sus asignaciones.');
        }
        $ubicacion->delete();
        return back()->with('success', 'Caja eliminada.');
    }

    // Desde la fecha, el rango se rotula con esa caja en el libro; no mueve existencias
    public function asignar(Request $request, Distrito $distrito)
    {
        $datos = $request->validate([
            'ubicacion_id'    => ['required', Rule::exists('ubicaciones', 'id')->where('distrito_id', $distrito->id)],
            'tipo_especie_id' => 'required|exists:tipo_especies,id',
            'serie'           => 'nullable|string|max:10',
            'numero_inicio'   => 'required|integer|min:1',
            'numero_fin'      => 'required|integer|gte:numero_inicio',
            'fecha'           => 'required|date|before_or_equal:today',
        ], [
            'ubicacion_id.required' => 'Seleccione la caja.',
            'ubicacion_id.exists'   => 'La caja no pertenece a este distrito.',
            'numero_fin.gte'        => 'El número final no puede ser menor que el inicial.',
            'fecha.before_or_equal' => 'La fecha no puede ser futura.',
        ]);

        UbicacionRango::create(array_merge($datos, ['serie' => Inventario::serie($datos['serie'] ?? null), 'usuario_id' => auth()->id()]));

        return back()->with('success', 'Rango asignado a la caja.');
    }

    public function quitarAsignacion(UbicacionRango $rango)
    {
        $rango->delete();
        return back()->with('success', 'Asignación eliminada.');
    }
}
