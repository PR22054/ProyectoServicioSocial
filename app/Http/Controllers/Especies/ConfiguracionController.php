<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;

class ConfiguracionController extends Controller
{
    public function tipos()
    {
        return view('frontend.admin.especies.configuracion.tipos');
    }

    public function denominaciones()
    {
        return view('frontend.admin.especies.configuracion.denominaciones');
    }
}
