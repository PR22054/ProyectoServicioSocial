@extends('frontend.layouts.admin')
@section('page_title', 'Historial de Anulaciones')

@section('page_content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- filtro y botón --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('admin.especies.distritos.anulaciones.historial') }}" class="form-inline">
            <label class="mr-2">Filtrar por distrito:</label>
            <select name="distrito_id" class="form-control form-control-sm mr-2">
                <option value="">— Todos —</option>
                @foreach($distritos as $d)
                    <option value="{{ $d->id }}" {{ $distFiltro == $d->id ? 'selected' : '' }}>
                        {{ $d->nombre }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary mr-2">Filtrar</button>
            @if($distFiltro)
                <a href="{{ route('admin.especies.distritos.anulaciones.historial') }}" class="btn btn-sm btn-secondary">Limpiar</a>
            @endif
        </form>
        <a href="{{ route('admin.especies.distritos.anulaciones.crear') }}" class="btn btn-primary">
            <i class="fas fa-plus mr-1"></i> Registrar anulación
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Anulaciones registradas ({{ $nulas->count() }})</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width:4%">#</th>
                        <th>Fecha</th>
                        <th>Distrito</th>
                        <th>Tipo de especie</th>
                        <th>Denominación</th>
                        <th>Factura</th>
                        <th class="text-center">Rango anulado</th>
                        <th class="text-right">Cantidad</th>
                        <th>Motivo</th>
                        <th>Registrado por</th>
                        <th class="text-center" style="width:8%">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($nulas as $nula)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $nula->fecha->format('d/m/Y') }}</td>
                        <td>{{ $nula->distrito->nombre ?? '—' }}</td>
                        <td>{{ $nula->trasladoDetalle->lote->tipoEspecie->nombre ?? '—' }}</td>
                        <td>${{ number_format($nula->trasladoDetalle->lote->denominacion->valor ?? 0, 2) }}</td>
                        <td>{{ $nula->trasladoDetalle->lote->compra->numero_factura ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge badge-warning">
                                {{ number_format($nula->numero_inicio) }} – {{ number_format($nula->numero_fin) }}
                            </span>
                        </td>
                        <td class="text-right">{{ number_format($nula->cantidad) }}</td>
                        <td>{{ $nula->motivo ?? '—' }}</td>
                        <td>{{ $nula->usuario->usuario ?? '—' }}</td>
                        <td class="text-center">
                            <form method="POST"
                                  action="{{ route('admin.especies.distritos.anulaciones.destroy', $nula) }}"
                                  id="del-nula-{{ $nula->id }}" style="display:inline">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-xs btn-danger"
                                    data-swal-delete
                                    data-form="del-nula-{{ $nula->id }}"
                                    data-msg="¿Eliminar la anulación {{ number_format($nula->numero_inicio) }}–{{ number_format($nula->numero_fin) }}?">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-3">Sin anulaciones registradas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@stop
