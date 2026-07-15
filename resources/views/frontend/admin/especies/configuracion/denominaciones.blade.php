@extends('frontend.layouts.admin')
@section('page_title', 'Denominaciones')

@section('page_content')

    {{-- alertas --}}
    @if(session('success_den'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success_den') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error_den'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error_den') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- formulario de creacion --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Nueva denominación</h3></div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger py-2">
                    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('admin.especies.configuracion.denominaciones.store') }}">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label>Tipo de especie <span class="text-danger">*</span></label>
                            <select name="tipo_especie_id" class="form-control @error('tipo_especie_id') is-invalid @enderror">
                                <option value="">— Seleccione —</option>
                                @foreach($tipos as $t)
                                    <option value="{{ $t->id }}" {{ old('tipo_especie_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mb-0">
                            <label>Valor ($) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                <input type="number" name="valor"
                                       class="form-control @error('valor') is-invalid @enderror"
                                       value="{{ old('valor') }}" placeholder="0.00" step="0.01" min="0.01">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-center">
                        <div class="custom-control custom-switch mt-1">
                            <input type="checkbox" class="custom-control-input" id="activo_new" name="activo" value="1"
                                   {{ old('activo', '1') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="activo_new">Activo</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-plus mr-1"></i>Agregar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- tabla de denominaciones --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Denominaciones registradas ({{ $denominaciones->count() }})</h3></div>
        <div class="card-body p-0">
            <table class="table table-striped table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width:5%">#</th>
                        <th>Tipo de especie</th>
                        <th class="text-right" style="width:15%">Valor</th>
                        <th class="text-center" style="width:10%">Estado</th>
                        <th class="text-center" style="width:12%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($denominaciones as $den)
                    <tr>
                        <td>{{ $den->id }}</td>
                        <td>{{ $den->tipoEspecie->nombre }}</td>
                        <td class="text-right font-weight-bold">${{ number_format($den->valor, 2) }}</td>
                        <td class="text-center">
                            @if($den->activo)
                                <span class="badge badge-success">Activo</span>
                            @else
                                <span class="badge badge-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-warning"
                                    onclick="abrirEditDen({{ $den->id }}, {{ $den->tipo_especie_id }}, {{ $den->valor }}, {{ $den->activo }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST" action="{{ route('admin.especies.configuracion.denominaciones.destroy', $den) }}"
                                  id="del-den-{{ $den->id }}" style="display:inline">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-xs btn-danger"
                                    data-swal-delete data-form="del-den-{{ $den->id }}"
                                    data-msg="¿Eliminar la denominación ${{ number_format($den->valor, 2) }} de {{ $den->tipoEspecie->nombre }}?">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Sin denominaciones registradas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- modal de edicion --}}
    <div class="modal fade" id="modalEditDen" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="formEditDen">
                    @csrf @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar denominación</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tipo de especie <span class="text-danger">*</span></label>
                            <select name="tipo_especie_id" id="edit_tipo" class="form-control" required>
                                @foreach($tipos as $t)
                                    <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Valor ($) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                <input type="number" name="valor" id="edit_valor" class="form-control"
                                       step="0.01" min="0.01" required>
                            </div>
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
function abrirEditDen(id, tipoId, valor, activo) {
    document.getElementById('edit_tipo').value   = tipoId;
    document.getElementById('edit_valor').value  = valor;
    document.getElementById('edit_activo').checked = activo == 1;
    document.getElementById('formEditDen').action =
        '{{ url("admin/especies/configuracion/denominaciones") }}/' + id;
    $('#modalEditDen').modal('show');
}
</script>
@endpush
