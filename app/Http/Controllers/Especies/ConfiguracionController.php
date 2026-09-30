<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;
use App\Models\Denominacion;
use App\Models\TipoEspecie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConfiguracionController extends Controller
{
    // ── Tipos de especie ──────────────────────────────────────────────────────

    public function tipos()
    {
        $tipos = TipoEspecie::orderBy('orden')->orderBy('nombre')->get();
        return view('frontend.admin.especies.configuracion.tipos', compact('tipos'));
    }

    public function storeTipo(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:tipo_especies,nombre',
            'orden'  => 'nullable|integer|min:0|max:999',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique'   => 'Ya existe un tipo con ese nombre.',
            'nombre.max'      => 'El nombre no puede superar los 100 caracteres.',
            'orden.integer'   => 'El orden debe ser un número entero.',
        ]);

        TipoEspecie::create([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo'      => $request->boolean('activo'),
            'unidades'    => $request->boolean('unidades'),
            'orden'       => (int) $request->orden,
        ]);

        return back()->with('success_tipo', 'Tipo de especie creado correctamente.');
    }

    public function updateTipo(Request $request, TipoEspecie $tipo)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:tipo_especies,nombre,' . $tipo->id,
            'orden'  => 'nullable|integer|min:0|max:999',
        ], [
            'nombre.required' => 'El nombre es obligatorio.',
            'nombre.unique'   => 'Ya existe un tipo con ese nombre.',
            'nombre.max'      => 'El nombre no puede superar los 100 caracteres.',
            'orden.integer'   => 'El orden debe ser un número entero.',
        ]);

        $tipo->update([
            'nombre'      => $request->nombre,
            'descripcion' => $request->descripcion,
            'activo'      => $request->boolean('activo'),
            'unidades'    => $request->boolean('unidades'),
            'orden'       => (int) $request->orden,
        ]);

        return back()->with('success_tipo', 'Tipo de especie actualizado correctamente.');
    }

    public function destroyTipo(TipoEspecie $tipo)
    {
        $enUso = $tipo->denominaciones()->exists()
            || DB::table('lotes')->where('tipo_especie_id', $tipo->id)->exists()
            || DB::table('realizaciones')->where('tipo_especie_id', $tipo->id)->exists();

        if ($enUso) {
            return back()->with('error_tipo', 'No se puede eliminar: el tipo tiene denominaciones, compras o realizaciones asociadas.');
        }

        $tipo->delete();
        return back()->with('success_tipo', 'Tipo de especie eliminado correctamente.');
    }

    // ── Denominaciones ────────────────────────────────────────────────────────

    public function denominaciones()
    {
        $denominaciones = Denominacion::with('tipoEspecie')->withExists(['lotes', 'realizaciones'])
            ->orderBy('tipo_especie_id')->orderBy('valor')->get();
        $tipos          = TipoEspecie::orderBy('nombre')->get();
        return view('frontend.admin.especies.configuracion.denominaciones', compact('denominaciones', 'tipos'));
    }

    private function reglasDenominacion(): array
    {
        return [
            'tipo_especie_id' => 'required|exists:tipo_especies,id',
            'descripcion'     => 'nullable|string|max:150',
            'valor'           => 'required|numeric|min:0',
            'precio_costo'    => 'nullable|numeric|min:0',
            'precio_venta'    => 'nullable|numeric|min:0',
        ];
    }

    private function mensajesDenominacion(): array
    {
        return [
            'tipo_especie_id.required' => 'Seleccione un tipo de especie.',
            'tipo_especie_id.exists'   => 'El tipo seleccionado no existe.',
            'valor.required'           => 'El valor es obligatorio.',
            'valor.numeric'            => 'El valor debe ser numérico.',
            'valor.min'                => 'El valor no puede ser negativo.',
            'precio_venta.numeric'     => 'El precio de venta debe ser numérico.',
            'precio_venta.min'         => 'El precio de venta no puede ser negativo.',
            'precio_costo.numeric'     => 'El precio de costo debe ser numérico.',
            'precio_costo.min'         => 'El precio de costo no puede ser negativo.',
            'descripcion.max'          => 'La descripción no puede superar los 150 caracteres.',
        ];
    }

    public function storeDenominacion(Request $request)
    {
        $request->validate($this->reglasDenominacion(), $this->mensajesDenominacion());

        Denominacion::create([
            'tipo_especie_id' => $request->tipo_especie_id,
            'descripcion'     => $request->descripcion,
            'valor'           => $request->valor,
            'precio_costo'    => $request->precio_costo,
            'precio_venta'    => $request->precio_venta,
            'activo'          => $request->boolean('activo'),
        ]);

        return back()->with('success_den', 'Denominación creada correctamente.');
    }

    public function updateDenominacion(Request $request, Denominacion $denominacion)
    {
        $request->validate($this->reglasDenominacion(), $this->mensajesDenominacion());

        // Con movimientos registrados, cambiar tipo o valor reescribiria el historial de los libros
        $enUso   = $denominacion->lotes()->exists() || $denominacion->realizaciones()->exists();
        $cambia  = $request->tipo_especie_id != $denominacion->tipo_especie_id
                || round((float) $request->valor, 2) != round((float) $denominacion->valor, 2);

        if ($enUso && $cambia) {
            return back()->with('error_den', 'Esta denominación ya tiene compras o realizaciones: no se puede cambiar su tipo ni su valor. '
                . 'Para un valor nuevo cree otra denominación y desactive esta.');
        }

        $denominacion->update([
            'tipo_especie_id' => $request->tipo_especie_id,
            'descripcion'     => $request->descripcion,
            'valor'           => $request->valor,
            'precio_costo'    => $request->precio_costo,
            'precio_venta'    => $request->precio_venta,
            'activo'          => $request->boolean('activo'),
        ]);

        return back()->with('success_den', 'Denominación actualizada correctamente.');
    }

    public function ajaxDenominaciones(Request $request)
    {
        $dens = Denominacion::where('tipo_especie_id', $request->tipo_especie_id)
            ->where('activo', true)
            ->orderBy('valor')
            ->get(['id', 'descripcion', 'valor', 'precio_costo', 'precio_venta']);
        return response()->json($dens);
    }

    public function destroyDenominacion(Denominacion $denominacion)
    {
        if ($denominacion->lotes()->exists() || $denominacion->realizaciones()->exists()) {
            return back()->with('error_den', 'No se puede eliminar: la denominación tiene compras o realizaciones asociadas.');
        }

        $denominacion->delete();
        return back()->with('success_den', 'Denominación eliminada correctamente.');
    }
}
