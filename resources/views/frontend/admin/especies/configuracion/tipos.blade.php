@extends('frontend.layouts.admin')
@section('page_title', 'Tipos de Especie')

@section('page_content')

    {{-- alertas --}}
    @if(session('success_tipo'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success_tipo') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error_tipo'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error_tipo') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- formulario de creacion --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Nuevo tipo de especie</h3></div>
        <div class="card-body">
            @if($errors->has('nombre'))
                <div class="alert alert-danger py-2">{{ $errors->first('nombre') }}</div>
            @endif
            <form method="POST" action="{{ route('admin.especies.configuracion.tipos.store') }}">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label>Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror"
                                   value="{{ old('nombre') }}" placeholder="Ej. Fondo Vialidad" maxlength="100">
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group mb-0">
                            <label>Descripción <small class="text-muted">(opcional)</small></label>
                            <input type="text" name="descripcion" class="form-control"
                                   value="{{ old('descripcion') }}" placeholder="Descripción breve" maxlength="255">
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <div class="custom-control custom-switch mt-1">
                            <input type="checkbox" class="custom-control-input" id="activo_new" name="activo" value="1"
                                   {{ old('activo', '1') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="activo_new">Activo</label>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- tabla de tipos --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Tipos registrados ({{ $tipos->count() }})</h3></div>
        <div class="card-body p-0">
            <table class="table table-striped table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width:5%">#</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th class="text-center" style="width:10%">Estado</th>
                        <th class="text-center" style="width:12%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tipos as $tipo)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $tipo->nombre }}</td>
                        <td class="text-muted">{{ $tipo->descripcion ?? '—' }}</td>
                        <td class="text-center">
                            @if($tipo->activo)
                                <span class="badge badge-success">Activo</span>
                            @else
                                <span class="badge badge-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-warning"
                                    onclick="abrirEditTipo({{ $tipo->id }}, '{{ addslashes($tipo->nombre) }}', '{{ addslashes($tipo->descripcion ?? '') }}', {{ $tipo->activo }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.especies.configuracion.tipos.destroy', $tipo) }}"
                                  id="del-tipo-{{ $tipo->id }}" style="display:inline">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-xs btn-danger"
                                    data-swal-delete data-form="del-tipo-{{ $tipo->id }}"
                                    data-msg="¿Eliminar el tipo «{{ $tipo->nombre }}»?">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Sin tipos registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- modal de edicion --}}
    <div class="modal fade" id="modalEditTipo" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="formEditTipo">
                    @csrf @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar tipo de especie</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Nombre <span class="text-danger">*</span></label>
                            <input type="text" name="nombre" id="edit_nombre" class="form-control" maxlength="100" required>
                        </div>
                        <div class="form-group">
                            <label>Descripción <small class="text-muted">(opcional)</small></label>
                            <input type="text" name="descripcion" id="edit_descripcion" class="form-control" maxlength="255">
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="edit_activo" name="activo" value="1">
                            <label class="custom-control-label" for="edit_activo">Activo</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@stop

@push('js')
<script>
function abrirEditTipo(id, nombre, descripcion, activo) {
    document.getElementById('edit_nombre').value      = nombre;
    document.getElementById('edit_descripcion').value = descripcion;
    document.getElementById('edit_activo').checked    = activo == 1;
    document.getElementById('formEditTipo').action    =
        '{{ url("admin/especies/configuracion/tipos") }}/' + id;
    $('#modalEditTipo').modal('show');
}
</script>
@endpush
