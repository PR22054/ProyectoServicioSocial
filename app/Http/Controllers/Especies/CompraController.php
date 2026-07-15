<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;

class CompraController extends Controller
{
    public function registrar()
    {
        return view('frontend.admin.especies.compras.registrar');
    }

    public function historial()
    {
        return view('frontend.admin.especies.compras.historial');
    }
}
