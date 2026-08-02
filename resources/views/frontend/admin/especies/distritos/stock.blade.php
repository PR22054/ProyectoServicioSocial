@extends('frontend.layouts.admin')
@section('page_title', 'Stock por Distrito')

@section('page_content')

    {{-- filtro --}}
    <div class="card">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.especies.distritos.stock') }}" class="form-inline">
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
                    <a href="{{ route('admin.especies.distritos.stock') }}" class="btn btn-sm btn-secondary">Limpiar</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Stock en distritos ({{ $detalles->count() }} detalle(s))</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width:4%">#</th>
                        <th>Distrito</th>
                        <th>Tipo de especie</th>
                        <th>Denominación</th>
                        <th>Factura</th>
                        <th class="text-center">Rango recibido</th>
                        <th class="text-right">Recibido</th>
                        <th class="text-right">Anulado</th>
                        <th class="text-right">Disponible</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($detalles as $d)
                    <tr class="{{ $d->disponible == 0 ? 'text-muted' : '' }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $d->traslado->distrito->nombre ?? '—' }}</td>
                        <td>{{ $d->lote->tipoEspecie->nombre ?? '—' }}</td>
                        <td>${{ number_format($d->lote->denominacion->valor ?? 0, 2) }}</td>
                        <td>{{ $d->lote->compra->numero_factura ?? '—' }}</td>
                        <td class="text-center">
                            <span class="badge badge-secondary">
                                {{ number_format($d->numero_inicio) }} – {{ number_format($d->numero_fin) }}
                            </span>
                        </td>
                        <td class="text-right">{{ number_format($d->cantidad) }}</td>
                        <td class="text-right">
                            @if($d->anulado > 0)
                                <span class="text-danger">{{ number_format($d->anulado) }}</span>
                            @else
                                0
                            @endif
                        </td>
                        <td class="text-right">
                            @if($d->disponible > 0)
                                <span class="badge badge-success" style="font-size:.85rem">
                                    {{ number_format($d->disponible) }}
                                </span>
                            @else
                                <span class="badge badge-secondary">0</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-3">Sin datos de stock en distritos</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($detalles->count() > 0)
                <tfoot class="font-weight-bold">
                    <tr>
                        <td colspan="6" class="text-right">Totales:</td>
                        <td class="text-right">{{ number_format($detalles->sum('cantidad')) }}</td>
                        <td class="text-right">{{ number_format($detalles->sum('anulado')) }}</td>
                        <td class="text-right">{{ number_format($detalles->sum('disponible')) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

@stop
