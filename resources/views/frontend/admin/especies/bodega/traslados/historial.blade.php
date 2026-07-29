@extends('frontend.layouts.admin')
@section('page_title', 'Historial de Traslados')

@section('page_content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="mb-3">
        <a href="{{ route('admin.especies.bodega.traslado.crear') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Registrar traslado
        </a>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Traslados registrados ({{ $traslados->count() }})</h3></div>
        <div class="card-body p-0">
            <table class="table table-striped table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width:5%">#</th>
                        <th>Fecha</th>
                        <th>Distrito destino</th>
                        <th>Registrado por</th>
                        <th class="text-center">Detalles</th>
                        <th class="text-right">Total especies</th>
                        <th class="text-center" style="width:12%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($traslados as $traslado)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $traslado->fecha->format('d/m/Y') }}</td>
                        <td>{{ $traslado->distrito->nombre ?? '—' }}</td>
                        <td>{{ $traslado->usuario->usuario ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $traslado->detalles_count }}</span>
                        </td>
                        <td class="text-right">{{ number_format($traslado->detalles_sum_cantidad) }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.especies.bodega.traslado.show', $traslado) }}"
                               class="btn btn-xs btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button type="button" class="btn btn-xs btn-warning"
                                    onclick="abrirEditTraslado(
                                        {{ $traslado->id }},
                                        {{ $traslado->distrito_id }},
                                        '{{ $traslado->fecha->format('Y-m-d') }}',
                                        '{{ addslashes($traslado->observaciones ?? '') }}'
                                    )">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST"
                                  action="{{ route('admin.especies.bodega.traslado.destroy', $traslado) }}"
                                  id="del-traslado-{{ $traslado->id }}" style="display:inline">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-xs btn-danger"
                                    data-swal-delete
                                    data-form="del-traslado-{{ $traslado->id }}"
                                    data-msg="¿Eliminar el traslado #{{ $traslado->id }} ({{ $traslado->distrito->nombre ?? '' }})? Se eliminarán también sus detalles.">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-3">Sin traslados registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- modal de edición --}}
    <div class="modal fade" id="modalEditTraslado" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="formEditTraslado">
                    @csrf @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar traslado</h5>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        @if($errors->any())
                            <div class="alert alert-danger py-2">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $e)
                                        <li>{{ $e }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="form-group">
                            <label>Distrito destino <span class="text-danger">*</span></label>
                            <select name="distrito_id" id="edit_distrito_id" class="form-control" required>
                                @foreach($distritos as $d)
                                    <option value="{{ $d->id }}">
                                        {{ $d->nombre }}{{ $d->codigo ? ' (' . $d->codigo . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" id="edit_fecha" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Observaciones <small class="text-muted">(opcional)</small></label>
                            <textarea name="observaciones" id="edit_observaciones" class="form-control" rows="2" maxlength="500"></textarea>
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
function abrirEditTraslado(id, distritoId, fecha, observaciones) {
    document.getElementById('edit_distrito_id').value   = distritoId;
    document.getElementById('edit_fecha').value         = fecha;
    document.getElementById('edit_observaciones').value = observaciones;
    document.getElementById('formEditTraslado').action  =
        '{{ url("admin/especies/bodega/traslados") }}/' + id;
    $('#modalEditTraslado').modal('show');
}
</script>
@endpush
