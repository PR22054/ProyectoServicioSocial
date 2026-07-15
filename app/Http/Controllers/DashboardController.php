<?php

namespace App\Http\Controllers;

//controlador que gestiona la redireccion post-login y las vistas de dashboard por rol
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    //redirige al dashboard correcto segun el rol del usuario autenticado
    public function redirect()
    {
        $user = auth()->user();

        if ($user->hasRole('admin') || $user->hasRole('empleado')) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('empleado.dashboard');
    }

    //retorna la vista del dashboard para el rol admin
    public function admin()
    {
        return view('frontend.admin.dashboard');
    }

    //retorna la vista del dashboard para el rol empleado
    public function empleado()
    {
        return view('frontend.empleado.dashboard');
    }
}
