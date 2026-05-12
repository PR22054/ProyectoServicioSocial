<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function redirect()
    {
        if (auth()->user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('empleado.dashboard');
    }

    public function admin()
    {
        return view('frontend.admin.dashboard');
    }

    public function empleado()
    {
        return view('frontend.empleado.dashboard');
    }
}
