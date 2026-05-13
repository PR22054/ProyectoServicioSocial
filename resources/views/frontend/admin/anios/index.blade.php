{{--vista CRUD de anos: formulario para agregar y tabla con editar/eliminar--}}
@extends('frontend.layouts.admin')

@section('page_title', 'Años')

@section('page_content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="card">
        {{--formulario para agregar un nuevo ano en la parte superior de la tabla--}}
        <div class="card-header">
            <form action="{{ route('admin.anios.store') }}" method="POST" class="form-inline">
                @csrf
                <input type="number" name="anio"
                       class="form-control mr-2"
                       placeholder="Ej. 2025"
                       min="1900" max="2100"
                       value="{{ old('anio') }}"
                       style="width:130px"
                       required>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-plus"></i> Agregar Año
                </button>
            </form>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>Año</th>
                        <th style="width:120px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($anios as $anio)
                    <tr>
                        <td class="align-middle">{{ $anio->anio }}</td>
                        <td>
                            <a href="{{ route('admin.anios.edit', $anio) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.anios.destroy', $anio) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('¿Eliminar el año {{ $anio->anio }}?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted py-3">Sin años registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@stop
