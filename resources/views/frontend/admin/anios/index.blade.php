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
        {{--formulario para agregar un nuevo ano con seleccion de archivo Excel--}}
        <div class="card-header">
            <form action="{{ route('admin.anios.store') }}" method="POST">
                @csrf
                <div class="form-row align-items-end">
                    <div class="col-auto">
                        <label class="mb-1">Año</label>
                        <input type="number" name="anio"
                               class="form-control"
                               placeholder="Ej. 2025"
                               min="1900" max="2100"
                               value="{{ old('anio') }}"
                               style="width:130px"
                               required>
                    </div>
                    <div class="col">
                        <label class="mb-1">Archivo Excel</label>
                        <select name="archivo_excel" class="form-control">
                            <option value="">-- Sin archivo --</option>
                            @foreach($archivos as $archivo)
                                <option value="{{ $archivo }}" {{ old('archivo_excel') === $archivo ? 'selected' : '' }}>
                                    {{ $archivo }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus"></i> Agregar Año
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>Año</th>
                        <th>Archivo Excel</th>
                        <th style="width:120px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($anios as $anio)
                    <tr>
                        <td class="align-middle">{{ $anio->anio }}</td>
                        <td class="align-middle">
                            @if($anio->archivo_excel)
                                <span class="text-success"><i class="fas fa-file-excel mr-1"></i>{{ $anio->archivo_excel }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.anios.edit', $anio) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form id="del-anio-{{ $anio->id }}" action="{{ route('admin.anios.destroy', $anio) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger"
                                    data-swal-delete
                                    data-form="del-anio-{{ $anio->id }}"
                                    data-msg="¿Eliminar el año {{ $anio->anio }}?"
                                    data-icon="warning">
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
