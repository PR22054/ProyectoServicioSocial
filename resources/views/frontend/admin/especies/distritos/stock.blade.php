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
            <h3 class="card-title">Stock en distritos ({{ $filas->count() }} lote(s))</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width:4%">#</th>
                        <th>Distrito</th>
                        <th>Tipo de especie</th>
                        <th>Denominación</th>
                        <th class="text-center">Serie</th>
                        <th>Factura</th>
                        <th>Rangos en existencia</th>
                        <th class="text-right">Recibido</th>
                        <th class="text-right">Salidas</th>
                        <th class="text-right">Anulado</th>
                        <th class="text-right">Realizado</th>
                        <th class="text-right">Disponible</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filas as $f)
                    <tr class="{{ $f['cantidad'] == 0 ? 'text-muted' : '' }}">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $f['distrito']->nombre }}</td>
                        <td>{{ $f['lote']->tipoEspecie->nombre ?? '—' }}</td>
                        <td>${{ number_format($f['valor'], 2) }}</td>
                        <td class="text-center">{{ $f['lote']->serie ?? '—' }}</td>
                        <td>{{ $f['lote']->compra->numero_factura ?? '—' }}</td>
                        <td>
                            @foreach($f['intervalos'] as [$i, $fin])
                                <span class="badge badge-secondary">{{ number_format($i) }} – {{ number_format($fin) }}</span>
                            @endforeach
                        </td>
                        <td class="text-right">{{ number_format($f['recibido']) }}</td>
                        <td class="text-right">{{ number_format($f['salido']) }}</td>
                        <td class="text-right">
                            @if($f['anulado'] > 0)
                                <span class="text-danger">{{ number_format($f['anulado']) }}</span>
                            @else
                                0
                            @endif
                        </td>
                        <td class="text-right">
                            @if($f['realizado'] > 0)
                                <span class="text-primary">{{ number_format($f['realizado']) }}</span>
                            @else
                                0
                            @endif
                        </td>
                        <td class="text-right">
                            @if($f['cantidad'] > 0)
                                <span class="badge badge-success" style="font-size:.85rem">
                                    {{ number_format($f['cantidad']) }}
                                </span>
                            @else
                                <span class="badge badge-secondary">0</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" class="text-center text-muted py-3">Sin datos de stock en distritos</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($filas->count() > 0)
                <tfoot class="font-weight-bold">
                    <tr>
                        <td colspan="7" class="text-right">Totales:</td>
                        <td class="text-right">{{ number_format($filas->sum('recibido')) }}</td>
                        <td class="text-right">{{ number_format($filas->sum('salido')) }}</td>
                        <td class="text-right">{{ number_format($filas->sum('anulado')) }}</td>
                        <td class="text-right">{{ number_format($filas->sum('realizado')) }}</td>
                        <td class="text-right">{{ number_format($filas->sum('cantidad')) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

@stop
