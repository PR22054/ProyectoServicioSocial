<?php

namespace App\Http\Controllers;

//muestra el historial de busquedas de NIT/DUI realizadas en la seccion de retencion
use App\Models\Consulta;

class ConsultaController extends Controller
{
    //lista todas las consultas ordenadas de la mas reciente a la mas antigua
    public function index()
    {
        $consultas = Consulta::orderBy('buscado_en', 'desc')->paginate(25);
        return view('frontend.admin.consultas.index', compact('consultas'));
    }
}
