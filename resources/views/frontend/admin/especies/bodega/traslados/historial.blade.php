@extends('frontend.layouts.admin')
@section('page_title', 'Historial de Traslados')

@section('page_content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
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
                        <th class="text-center" style="width:8%">Ver</th>
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
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted py-3">Sin traslados registrados</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@stop
