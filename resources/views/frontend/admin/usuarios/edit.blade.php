{{--formulario de edicion de usuario: permite cambiar el nombre de usuario y la contrasena--}}
{{--si el campo password se deja en blanco la contrasena actual no se modifica--}}
@extends('frontend.layouts.admin')

@section('page_title', 'Editar Usuario')

@section('page_content')

    <div class="card" style="max-width:480px">
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('admin.usuarios.update', $user) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label>Usuario</label>
                    <input type="text" name="usuario"
                           class="form-control @error('usuario') is-invalid @enderror"
                           value="{{ old('usuario', $user->usuario) }}" required>
                </div>

                <div class="form-group">
                    <label>Nueva Contraseña
                        <small class="text-muted">(dejar en blanco para no cambiar)</small>
                    </label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror">
                </div>

                <div class="form-group">
                    <label>Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary ml-2">Cancelar</a>
            </form>

        </div>
    </div>

@stop
