<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;
use App\Models\Denominacion;
use App\Models\TipoEspecie;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    // ── Tipos de especie ──────────────────────────────────────────────────────

    public function tipos()
    {
        $tipos = TipoEspecie::orderBy('nombre')->get();
        return view('frontend.admin.especies.configuracion.tipos', compact('tipos'));
    }

    public function storeTipo(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:tipo_especies,nombre',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique'   => 'Ya existe un tipo con ese nombre.',
            'nombre.max'      => 'El nombre no puede superar los 100 caracteres.',
        ]);

        TipoEspecie::create([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo'      => $request->boolean('activo'),
        ]);

        return back()->with('success_tipo', 'Tipo de especie creado correctamente.');
    }

    public function updateTipo(Request $request, TipoEspecie $tipo)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:tipo_especies,nombre,' . $tipo->id,
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique'   => 'Ya existe un tipo con ese nombre.',
            'nombre.max'      => 'El nombre no puede superar los 100 caracteres.',
        ]);

        $tipo->update([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo'      => $request->boolean('activo'),
        ]);

        return back()->with('success_tipo', 'Tipo de especie actualizado correctamente.');
    }

    public function destroyTipo(TipoEspecie $tipo)
    {
        if ($tipo->denominaciones()->exists()) {
            return back()->with('error_tipo', 'No se puede eliminar: el tipo tiene denominaciones asociadas.');
        }

        $tipo->delete();
        return back()->with('success_tipo', 'Tipo de especie eliminado correctamente.');
    }

    // ── Denominaciones ────────────────────────────────────────────────────────

    public function denominaciones()
    {
        $denominaciones = Denominacion::with('tipoEspecie')->orderBy('tipo_especie_id')->orderBy('valor')->get();
        $tipos          = TipoEspecie::orderBy('nombre')->get();
        return view('frontend.admin.especies.configuracion.denominaciones', compact('denominaciones', 'tipos'));
    }

    public function storeDenominacion(Request $request)
    {
        $request->validate([
            'tipo_especie_id' => 'required|exists:tipo_especies,id',
            'valor'           => 'required|numeric|min:0.01',
        ], [
            'tipo_especie_id.required' => 'Seleccione un tipo de especie.',
            'tipo_especie_id.exists'   => 'El tipo seleccionado no existe.',
            'valor.required'           => 'El valor es obligatorio.',
            'valor.numeric'            => 'El valor debe ser numérico.',
            'valor.min'                => 'El valor debe ser mayor a cero.',
        ]);

        Denominacion::create([
            'tipo_especie_id' => $request->tipo_especie_id,
            'valor'           => $request->valor,
            'activo'          => $request->boolean('activo'),
        ]);

        return back()->with('success_den', 'Denominación creada correctamente.');
    }

    public function updateDenominacion(Request $request, Denominacion $denominacion)
    {
        $request->validate([
            'tipo_especie_id' => 'required|exists:tipo_especies,id',
            'valor'           => 'required|numeric|min:0.01',
        ], [
            'tipo_especie_id.required' => 'Seleccione un tipo de especie.',
            'tipo_especie_id.exists'   => 'El tipo seleccionado no existe.',
            'valor.required'           => 'El valor es obligatorio.',
            'valor.numeric'            => 'El valor debe ser numérico.',
            'valor.min'                => 'El valor debe ser mayor a cero.',
        ]);

        $denominacion->update([
            'tipo_especie_id' => $request->tipo_especie_id,
            'valor'           => $request->valor,
            'activo'          => $request->boolean('activo'),
        ]);

        return back()->with('success_den', 'Denominación actualizada correctamente.');
    }

    public function destroyDenominacion(Denominacion $denominacion)
    {
        // proteger si ya está referenciada en lotes
        if (\DB::table('lotes')->where('denominacion_id', $denominacion->id)->exists()) {
            return back()->with('error_den', 'No se puede eliminar: la denominación está asociada a un lote de compra.');
        }

        $denominacion->delete();
        return back()->with('success_den', 'Denominación eliminada correctamente.');
    }
}
