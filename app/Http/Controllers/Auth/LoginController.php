<?php

namespace App\Http\Controllers\Auth;

//controlador de autenticacion, usa el campo usuario en lugar de email
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    //retorna la vista del formulario de login ubicada en frontend/auth/login.blade.php
    public function showLoginForm()
    {
        return view('frontend.auth.login');
    }

    //valida credenciales y redirige segun el rol del usuario autenticado
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'usuario'  => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return Auth::user()->hasRole('admin')
                ? redirect()->route('admin.dashboard')
                : redirect()->route('empleado.dashboard');
        }

        //si las credenciales fallan regresa con error sin exponer detalles
        return back()->withErrors(['usuario' => 'Credenciales incorrectas.'])->onlyInput('usuario');
    }

    //invalida la sesion activa y redirige al login
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    }
}
