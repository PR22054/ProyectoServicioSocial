<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;

class DistritoController extends Controller
{
    public function anulacionRegistrar()
    {
        return view('frontend.admin.especies.distritos.anulacion_registrar');
    }

    public function stock()
    {
        return view('frontend.admin.especies.distritos.stock');
    }
}
