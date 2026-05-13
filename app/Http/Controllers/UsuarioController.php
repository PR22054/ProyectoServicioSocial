<?php

namespace App\Http\Controllers;

//CRUD completo para la tabla users accesible solo para el rol admin
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    //muestra la lista de todos los usuarios registrados
    public function index()
    {
        $users = User::all();
        return view('frontend.admin.usuarios.index', compact('users'));
    }

    //muestra el formulario para crear un nuevo usuario
    public function create()
    {
        return view('frontend.admin.usuarios.create');
    }

    //valida y guarda el nuevo usuario con rol empleado por defecto y contrasena hasheada
    public function store(Request $request)
    {
        $request->validate([
            'usuario'  => 'required|string|max:255|unique:users,usuario',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'usuario.unique'     => 'Ese nombre de usuario ya existe.',
            'password.confirmed' => 'Las contrasenas no coinciden.',
            'password.min'       => 'La contrasena debe tener al menos 6 caracteres.',
        ]);

        $user = User::create([
            'usuario'  => $request->usuario,
            'password' => $request->password,
            'rol'      => 'empleado',
        ]);
        $user->assignRole('empleado');

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario creado correctamente.');
    }

    //muestra el formulario de edicion de un usuario existente
    public function edit(User $usuario)
    {
        return view('frontend.admin.usuarios.edit', ['user' => $usuario]);
    }

    //actualiza usuario y/o contrasena, si el campo password viene vacio no lo modifica
    public function update(Request $request, User $usuario)
    {
        $rules = [
            'usuario' => 'required|string|max:255|unique:users,usuario,' . $usuario->id,
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6|confirmed';
        }

        $request->validate($rules, [
            'usuario.unique'     => 'Ese nombre de usuario ya existe.',
            'password.confirmed' => 'Las contrasenas no coinciden.',
            'password.min'       => 'La contrasena debe tener al menos 6 caracteres.',
        ]);

        $data = ['usuario' => $request->usuario];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $usuario->update($data);

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    //elimina un usuario, si es el propio admin cierra sesion y redirige al login con mensaje
    public function destroy(User $usuario)
    {
        $isSelf = Auth::id() === $usuario->id;

        $usuario->delete();

        if ($isSelf) {
            Auth::logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
            return redirect()->route('login')->with('info', 'Se ha eliminado el usuario, vuelva a iniciar sesion.');
        }

        return back()->with('success', 'Usuario eliminado.');
    }
}
