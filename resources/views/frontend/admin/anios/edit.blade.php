{{--formulario de edicion para un registro existente en la tabla anios--}}
@extends('frontend.layouts.admin')

@section('page_title', 'Editar Año')

@section('page_content')

    <div class="card mx-auto" style="max-width:360px">
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('admin.anios.update', $anio) }}" method="POST">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label>Año</label>
                    <input type="number" name="anio"
                           class="form-control @error('anio') is-invalid @enderror"
                           value="{{ old('anio', $anio->anio) }}"
                           min="1900" max="2100" required>
                </div>

                <button type="submit" class="btn btn-primary">Actualizar</button>
                <a href="{{ route('admin.anios.index') }}" class="btn btn-secondary ml-2">Cancelar</a>
            </form>

        </div>
    </div>

@stop
