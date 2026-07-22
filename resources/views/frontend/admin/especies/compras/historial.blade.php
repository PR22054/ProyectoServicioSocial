@extends('frontend.layouts.admin')
@section('page_title', 'Historial de Compras')

@section('page_content')

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
                        <th class="text-muted" style="width:30%">Observaciones</th>
                        <th class="text-center" style="width:10%">Detalle</th>
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

@stop
