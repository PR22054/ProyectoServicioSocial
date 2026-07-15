<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;

class RealizacionController extends Controller
{
    public function registrar()
    {
        return view('frontend.admin.especies.realizaciones.registrar');
    }
}
