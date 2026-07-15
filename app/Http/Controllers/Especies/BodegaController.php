<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;

class BodegaController extends Controller
{
    public function trasladoRegistrar()
    {
        return view('frontend.admin.especies.bodega.traslado_registrar');
    }

    public function trasladoHistorial()
    {
        return view('frontend.admin.especies.bodega.traslado_historial');
    }

    public function stock()
    {
        return view('frontend.admin.especies.bodega.stock');
    }
}
