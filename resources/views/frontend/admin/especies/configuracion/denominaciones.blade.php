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
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
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
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Descripción <small class="text-muted">(como aparece en los libros)</small></label>
                            <input type="text" name="descripcion" maxlength="150"
                                   class="form-control @error('descripcion') is-invalid @enderror"
                                   value="{{ old('descripcion') }}"
                                   placeholder="Ej: DE 30 COLONES $ 3.43 — FÓRMULAS CONTINUAS — JUEGO DE CARTULINA">
                        </div>
                    </div>
                </div>
                <div class="row align-items-end">
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label>Valor en el libro <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                <input type="number" name="valor"
                                       class="form-control @error('valor') is-invalid @enderror"
                                       value="{{ old('valor') }}" placeholder="0.00" step="0.01" min="0">
                            </div>
                            <small class="text-muted">Unitario, nunca el total del bloque.</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label>Precio de venta</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                <input type="number" name="precio_venta"
                                       class="form-control @error('precio_venta') is-invalid @enderror"
                                       value="{{ old('precio_venta') }}" placeholder="no se vende" step="0.0001" min="0">
                            </div>
                            <small class="text-muted">Vacío si no se vende.</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-0">
                            <label>Precio de costo</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                <input type="number" name="precio_costo"
                                       class="form-control @error('precio_costo') is-invalid @enderror"
                                       value="{{ old('precio_costo') }}" placeholder="0.0000" step="0.0001" min="0">
                            </div>
                            <small class="text-muted">Costo unitario al M.H. (base de descargos).</small>
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-center">
                        <div class="custom-control custom-switch mt-1">
                            <input type="checkbox" class="custom-control-input" id="activo_new" name="activo" value="1"
                                   {{ old('activo', '1') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="activo_new">Activo</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary btn-block">
                            Agregar
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
                        <th style="width:4%">#</th>
                        <th>Tipo de especie</th>
                        <th>Descripción</th>
                        <th class="text-right" style="width:10%">Valor</th>
                        <th class="text-right" style="width:11%">P. venta</th>
                        <th class="text-right" style="width:11%">P. costo</th>
                        <th class="text-center" style="width:9%">Estado</th>
                        <th class="text-center" style="width:10%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($denominaciones as $den)
                    <tr>
                        <td>{{ $den->id }}</td>
                        <td>{{ $den->tipoEspecie->nombre }}</td>
                        <td>
                            @if($den->descripcion)
                                {{ $den->descripcion }}
                            @else
                                <span class="text-warning" title="Sin descripción: los reportes mostrarán solo el valor">
                                    <i class="fas fa-exclamation-triangle"></i> sin descripción
                                </span>
                            @endif
                        </td>
                        <td class="text-right font-weight-bold">${{ number_format($den->valor, 2) }}</td>
                        <td class="text-right">
                            @if($den->precio_venta !== null)
                                ${{ rtrim(rtrim(number_format($den->precio_venta, 4), '0'), '.') }}
                            @else
                                <span class="text-muted">no se vende</span>
                            @endif
                        </td>
                        <td class="text-right">
                            @if($den->precio_costo !== null)
                                ${{ number_format($den->precio_costo, 4) }}
                            @else
                                <span class="text-warning" title="Sin precio de costo: no se pueden calcular descargos">
                                    <i class="fas fa-exclamation-triangle"></i> —
                                </span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($den->activo)
                                <span class="badge badge-success">Activo</span>
                            @else
                                <span class="badge badge-secondary">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-xs btn-warning"
                                    onclick="abrirEditDen({{ $den->id }}, {{ $den->tipo_especie_id }}, {{ Js::from($den->descripcion) }}, {{ $den->valor }}, {{ $den->precio_costo !== null ? $den->precio_costo : 'null' }}, {{ $den->precio_venta !== null ? $den->precio_venta : 'null' }}, {{ $den->activo }}, {{ ($den->lotes_exists || $den->realizaciones_exists) ? 'true' : 'false' }})">
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
                    <tr><td colspan="8" class="text-center text-muted py-3">Sin denominaciones registradas</td></tr>
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
                        <div id="edit_en_uso" class="alert alert-warning py-1 small" style="display:none">
                            Ya tiene compras o realizaciones: el tipo y el valor no se pueden cambiar.
                            Para un valor nuevo cree otra denominación y desactive esta.
                        </div>
                        <div class="form-group">
                            <label>Tipo de especie <span class="text-danger">*</span></label>
                            <select name="tipo_especie_id" id="edit_tipo" class="form-control" required>
                                @foreach($tipos as $t)
                                    <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                @endforeach
                            </select>
                            {{-- un select deshabilitado no se envia; si esta en uso el tipo viaja en este oculto --}}
                            <input type="hidden" name="tipo_especie_id" id="edit_tipo_fijo" disabled>
                        </div>
                        <div class="form-group">
                            <label>Descripción <small class="text-muted">(como aparece en los libros)</small></label>
                            <input type="text" name="descripcion" id="edit_descripcion" class="form-control" maxlength="150"
                                   placeholder="Ej: DE 30 COLONES $ 3.43">
                        </div>
                        <div class="row">
                            <div class="col-4">
                                <div class="form-group">
                                    <label>Valor en el libro <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                        <input type="number" name="valor" id="edit_valor" class="form-control"
                                               step="0.01" min="0" required>
                                    </div>
                                    <small class="text-muted">Unitario, no el total del bloque.</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label>Precio de venta</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                        <input type="number" name="precio_venta" id="edit_precio_venta" class="form-control"
                                               step="0.0001" min="0" placeholder="no se vende">
                                    </div>
                                    <small class="text-muted">Vacío si no se vende.</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-group">
                                    <label>Precio de costo</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                        <input type="number" name="precio_costo" id="edit_precio_costo" class="form-control"
                                               step="0.0001" min="0">
                                    </div>
                                    <small class="text-muted">Costo unitario al M.H.</small>
                                </div>
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
function abrirEditDen(id, tipoId, descripcion, valor, precioCosto, precioVenta, activo, enUso) {
    document.getElementById('edit_tipo').value        = tipoId;
    document.getElementById('edit_tipo').disabled     = enUso;
    document.getElementById('edit_tipo_fijo').disabled = !enUso;
    document.getElementById('edit_tipo_fijo').value   = tipoId;
    document.getElementById('edit_valor').readOnly    = enUso;
    document.getElementById('edit_en_uso').style.display = enUso ? '' : 'none';
    document.getElementById('edit_descripcion').value = descripcion ?? '';
    document.getElementById('edit_valor').value       = valor;
    document.getElementById('edit_precio_costo').value = precioCosto ?? '';
    document.getElementById('edit_precio_venta').value = precioVenta ?? '';
    document.getElementById('edit_activo').checked = activo == 1;
    document.getElementById('formEditDen').action =
        '{{ url("admin/especies/configuracion/denominaciones") }}/' + id;
    $('#modalEditDen').modal('show');
}
</script>
@endpush
