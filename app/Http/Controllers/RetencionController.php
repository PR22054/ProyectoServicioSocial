<?php

namespace App\Http\Controllers;

//controlador de la vista de retencion, compartida entre los roles admin y empleado
//el layout se selecciona dinamicamente segun el rol del usuario autenticado
use App\Models\Anio;

class RetencionController extends Controller
{
    //carga los anos disponibles y determina el layout segun el rol
    public function index()
    {
        $anios  = Anio::orderBy('anio', 'desc')->get();
        $layout = auth()->user()->hasRole('admin')
            ? 'frontend.layouts.admin'
            : 'frontend.layouts.empleado';

        return view('frontend.retencion.index', compact('anios', 'layout'));
    }
}
