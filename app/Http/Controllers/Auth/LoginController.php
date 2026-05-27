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
            //si el usuario no es admin se rechaza el acceso y se muestra error
            if (!Auth::user()->hasRole('admin')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['usuario' => 'El acceso es exclusivo para administradores.'])->onlyInput('usuario');
            }

            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
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
