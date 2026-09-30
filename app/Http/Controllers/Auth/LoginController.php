<?php

namespace App\Http\Controllers\Auth;

//controlador de autenticacion, usa el campo usuario en lugar de email
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

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

        //limite de intentos fallidos: 10 por usuario desde una IP y 30 por IP en total (para quien pruebe varios usuarios),
        //ambos en ventanas de 1 minuto
        $porUsuario = 'login|' . Str::transliterate(Str::lower($request->usuario)) . '|' . $request->ip();
        $porIp      = 'login-ip|' . $request->ip();

        if (RateLimiter::tooManyAttempts($porUsuario, 10) || RateLimiter::tooManyAttempts($porIp, 30)) {
            $minutos = (int) ceil(max(RateLimiter::availableIn($porUsuario), RateLimiter::availableIn($porIp)) / 60);
            return back()->withErrors(['usuario' => "Demasiados intentos fallidos. Intente de nuevo en {$minutos} "
                . ($minutos === 1 ? 'minuto.' : 'minutos.')])->onlyInput('usuario');
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($porUsuario);
            $user = Auth::user();

            if ($user->hasRole('admin')) {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('empleado')) {
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard');
            }

            if ($user->hasRole('visitante')) {
                $request->session()->regenerate();
                return redirect()->route('empleado.dashboard');
            }

            //rol no reconocido: se deniega el acceso
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return back()->withErrors(['usuario' => 'No tiene acceso al sistema.'])->onlyInput('usuario');
        }

        //si las credenciales fallan regresa con error sin exponer detalles
        RateLimiter::hit($porUsuario, 60);
        RateLimiter::hit($porIp, 60);
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
