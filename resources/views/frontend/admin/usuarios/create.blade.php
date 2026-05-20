{{--formulario para crear un nuevo usuario, el rol se asigna como empleado por defecto--}}
@extends('frontend.layouts.admin')

@section('page_title', 'Crear Usuario')

@section('page_content')

    <div class="card mx-auto" style="max-width:480px">
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('admin.usuarios.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Usuario</label>
                    <input type="text" name="usuario"
                           class="form-control @error('usuario') is-invalid @enderror"
                           value="{{ old('usuario') }}" required>
                </div>

                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           required>
                </div>

                <div class="form-group">
                    <label>Confirmar Contraseña</label>
                    <input type="password" name="password_confirmation"
                           class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary">Guardar</button>
                <a href="{{ route('admin.usuarios.index') }}" class="btn btn-secondary ml-2">Cancelar</a>
            </form>

        </div>
    </div>

@stop
