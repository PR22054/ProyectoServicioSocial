@extends('frontend.layouts.admin')
@section('page_title', 'Historial de Compras')

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

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Compras registradas ({{ $compras->count() }})</h3>
            <div class="card-tools">
                <a href="{{ route('admin.especies.compras.crear') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i>Registrar compra
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width:12%">Fecha</th>
                        <th>N° Factura</th>
                        <th class="text-center" style="width:10%">Lotes</th>
                        <th class="text-right" style="width:15%">Monto total</th>
                        <th class="text-muted" style="width:25%">Observaciones</th>
                        <th class="text-center" style="width:14%">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($compras as $compra)
                    <tr>
                        <td>{{ $compra->fecha->format('d/m/Y') }}</td>
                        <td><strong>{{ $compra->numero_factura }}</strong></td>
                        <td class="text-center">
                            <span class="badge badge-info">{{ $compra->lotes_count }}</span>
                        </td>
                        <td class="text-right font-weight-bold text-success">
                            ${{ number_format($compra->monto_total, 2) }}
                        </td>
                        <td class="text-muted small">{{ $compra->observaciones ?? '—' }}</td>
                        <td class="text-center">
                            <a href="{{ route('admin.especies.compras.show', $compra) }}"
                               class="btn btn-xs btn-info">
                                <i class="fas fa-eye"></i>
                            </a>
                            <button type="button" class="btn btn-xs btn-warning"
                                    onclick="abrirEditCompra(
                                        {{ $compra->id }},
                                        '{{ addslashes($compra->numero_factura) }}',
                                        '{{ $compra->fecha->format('Y-m-d') }}',
                                        '{{ addslashes($compra->observaciones ?? '') }}'
                                    )">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form method="POST"
                                  action="{{ route('admin.especies.compras.destroy', $compra) }}"
                                  id="del-compra-{{ $compra->id }}" style="display:inline">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-xs btn-danger"
                                    data-swal-delete
                                    data-form="del-compra-{{ $compra->id }}"
                                    data-msg="¿Eliminar la compra «{{ $compra->numero_factura }}»? Se eliminarán sus lotes y rangos.">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            Sin compras registradas aún.
                            <a href="{{ route('admin.especies.compras.crear') }}">Registrar la primera</a>.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- modal de edición --}}
    <div class="modal fade" id="modalEditCompra" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" id="formEditCompra">
                    @csrf @method('PATCH')
                    <div class="modal-header">
                        <h5 class="modal-title">Editar compra</h5>
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
                            <label>N° Factura <span class="text-danger">*</span></label>
                            <input type="text" name="numero_factura" id="edit_numero_factura"
                                   class="form-control" maxlength="100" required>
                        </div>
                        <div class="form-group">
                            <label>Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" id="edit_fecha" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label>Observaciones <small class="text-muted">(opcional)</small></label>
                            <textarea name="observaciones" id="edit_observaciones"
                                      class="form-control" rows="2" maxlength="500"></textarea>
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
function abrirEditCompra(id, factura, fecha, observaciones) {
    document.getElementById('edit_numero_factura').value  = factura;
    document.getElementById('edit_fecha').value           = fecha;
    document.getElementById('edit_observaciones').value   = observaciones;
    document.getElementById('formEditCompra').action      =
        '{{ url("admin/especies/compras") }}/' + id;
    $('#modalEditCompra').modal('show');
}
</script>
@endpush
