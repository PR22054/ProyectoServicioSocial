<?php

namespace App\Http\Controllers\Especies;

use App\Http\Controllers\Controller;

class ReporteController extends Controller
{
    public function libro()
    {
        return view('frontend.admin.especies.reportes.libro');
    }

    public function bodega()
    {
        return view('frontend.admin.especies.reportes.bodega');
    }

    public function distritos()
    {
        return view('frontend.admin.especies.reportes.distritos');
    }

    public function realizaciones()
    {
        return view('frontend.admin.especies.reportes.realizaciones');
    }

    public function traslados()
    {
        return view('frontend.admin.especies.reportes.traslados');
    }

    public function mensual()
    {
        return view('frontend.admin.especies.reportes.mensual');
    }
}
