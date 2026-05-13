<?php

namespace App\Http\Controllers;

//controlador para gestionar el cambio de roles de los usuarios con Spatie Permission
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RolController extends Controller
{
    //lista todos los usuarios mostrando su usuario y rol actual
    public function index()
    {
        $users = User::select('id', 'usuario', 'rol')->get();
        return view('frontend.admin.roles.index', compact('users'));
    }

    //actualiza el rol en la tabla users y lo sincroniza con Spatie
    //si el usuario que cambia su rol es el mismo que esta autenticado, cierra sesion
    public function update(Request $request, User $user)
    {
        $request->validate(['rol' => 'required|in:admin,empleado']);

        $user->update(['rol' => $request->rol]);
        $user->syncRoles([$request->rol]);

        if (Auth::id() === $user->id) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('login')->with('info', 'Tu rol fue modificado. Inicia sesion de nuevo.');
        }

        return back()->with('success', 'Rol actualizado correctamente.');
    }
}
