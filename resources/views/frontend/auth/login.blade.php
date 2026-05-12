@extends('adminlte::auth.auth-page', ['authType' => 'login'])

@section('auth_header', 'Iniciar Sesión')

@section('auth_body')
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            Usuario o contraseña incorrectos.
        </div>
    @endif

    <form action="{{ url('login') }}" method="post">
        @csrf

        <div class="input-group mb-3">
            <input type="text" name="usuario"
                   class="form-control @error('usuario') is-invalid @enderror"
                   value="{{ old('usuario') }}"
                   placeholder="Usuario" autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-user"></span>
                </div>
            </div>
        </div>

        <div class="input-group mb-3">
            <input type="password" name="password"
                   class="form-control @error('password') is-invalid @enderror"
                   placeholder="Contraseña">
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-primary btn-block btn-flat">
                    <span class="fas fa-sign-in-alt"></span> Entrar
                </button>
            </div>
        </div>
    </form>
@stop
