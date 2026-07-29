@extends('frontend.layouts.admin')
@section('page_title', 'Stock Disponible en Bodega')

@section('page_content')

    {{-- filtro --}}
    <div class="card">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.especies.bodega.stock') }}" class="form-inline">
                <label class="mr-2">Filtrar por tipo:</label>
                <select name="tipo_especie_id" class="form-control form-control-sm mr-2">
                    <option value="">— Todos —</option>
                    @foreach($tipos as $tipo)
                        <option value="{{ $tipo->id }}" {{ $tipoFiltro == $tipo->id ? 'selected' : '' }}>
                            {{ $tipo->nombre }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-primary mr-2">Filtrar</button>
                @if($tipoFiltro)
                    <a href="{{ route('admin.especies.bodega.stock') }}" class="btn btn-sm btn-secondary">Limpiar</a>
                @endif
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Existencias en bodega ({{ $lotes->count() }} lotes)</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width:4%">#</th>
                        <th>Tipo de especie</th>
                        <th>Denominación</th>
                        <th>Factura</th>
                        <th>Serie</th>
                        <th class="text-right">Total comprado</th>
                        <th class="text-right">Trasladado</th>
                        <th class="text-right">Disponible</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($lotes as $i => $lote)
                    <tr class="{{ $lote->stock_disponible == 0 ? 'text-muted' : '' }}">
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $lote->tipoEspecie->nombre ?? '—' }}</td>
                        <td>${{ number_format($lote->denominacion->valor ?? 0, 2) }}</td>
                        <td>{{ $lote->compra->numero_factura ?? '—' }}</td>
                        <td>{{ $lote->serie ?? '—' }}</td>
                        <td class="text-right">{{ number_format($lote->cantidad_total) }}</td>
                        <td class="text-right">{{ number_format($lote->stock_trasladado) }}</td>
                        <td class="text-right">
                            @if($lote->stock_disponible > 0)
                                <span class="badge badge-success" style="font-size:.85rem">
                                    {{ number_format($lote->stock_disponible) }}
                                </span>
                            @else
                                <span class="badge badge-secondary">0</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-3">Sin lotes registrados</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($lotes->count() > 0)
                <tfoot class="font-weight-bold">
                    <tr>
                        <td colspan="5" class="text-right">Totales:</td>
                        <td class="text-right">{{ number_format($lotes->sum('cantidad_total')) }}</td>
                        <td class="text-right">{{ number_format($lotes->sum('stock_trasladado')) }}</td>
                        <td class="text-right">{{ number_format($lotes->sum('stock_disponible')) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

@stop
