<?php

namespace App\Http\Controllers;

//CRUD para la tabla anios, accesible solo para el rol admin
use App\Models\Anio;
use Illuminate\Http\Request;

class AnioController extends Controller
{
    //lista todos los anos ordenados de forma descendente
    public function index()
    {
        $anios = Anio::orderBy('anio', 'desc')->get();
        return view('frontend.admin.anios.index', compact('anios'));
    }

    //valida que el ano no este duplicado y lo guarda en la BD
    public function store(Request $request)
    {
        $request->validate([
            'anio' => 'required|integer|digits:4|min:1900|max:2100|unique:anios,anio',
        ], [
            'anio.unique' => 'Ese ano ya esta registrado.',
            'anio.digits' => 'El ano debe tener 4 digitos.',
        ]);

        Anio::create(['anio' => $request->anio]);

        return back()->with('success', 'Ano agregado correctamente.');
    }

    //muestra el formulario de edicion para un ano existente
    public function edit(Anio $anio)
    {
        return view('frontend.admin.anios.edit', compact('anio'));
    }

    //valida y actualiza el campo anio ignorando el registro actual en la regla unique
    public function update(Request $request, Anio $anio)
    {
        $request->validate([
            'anio' => 'required|integer|digits:4|min:1900|max:2100|unique:anios,anio,' . $anio->id,
        ], [
            'anio.unique' => 'Ese ano ya esta registrado.',
            'anio.digits' => 'El ano debe tener 4 digitos.',
        ]);

        $anio->update(['anio' => $request->anio]);

        return redirect()->route('admin.anios.index')->with('success', 'Ano actualizado correctamente.');
    }

    //elimina el registro de ano de la BD
    public function destroy(Anio $anio)
    {
        $anio->delete();
        return back()->with('success', 'Ano eliminado.');
    }
}
