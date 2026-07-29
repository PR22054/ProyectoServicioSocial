@extends('frontend.layouts.admin')
@section('page_title', 'Traslado #' . $traslado->id)

@section('page_content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('success_detalle'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success_detalle') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error_detalle'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error_detalle') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- encabezado del traslado --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Traslado #{{ $traslado->id }}</h3>
            <a href="{{ route('admin.especies.bodega.traslado.historial') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver al historial
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>Fecha</strong><br>
                    {{ $traslado->fecha->format('d/m/Y') }}
                </div>
                <div class="col-md-4">
                    <strong>Distrito destino</strong><br>
                    {{ $traslado->distrito->nombre ?? '—' }}
                    @if($traslado->distrito && $traslado->distrito->codigo)
                        <span class="text-muted">({{ $traslado->distrito->codigo }})</span>
                    @endif
                </div>
                <div class="col-md-3">
                    <strong>Registrado por</strong><br>
                    {{ $traslado->usuario->usuario ?? '—' }}
                </div>
                <div class="col-md-2">
                    <strong>Total especies</strong><br>
                    <span class="badge badge-primary badge-lg" style="font-size:.9rem">
                        {{ number_format($traslado->detalles->sum('cantidad')) }}
                    </span>
                </div>
            </div>
            @if($traslado->observaciones)
                <div class="mt-2 text-muted"><em>{{ $traslado->observaciones }}</em></div>
            @endif
        </div>
    </div>

    {{-- detalles --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Detalles ({{ $traslado->detalles->count() }})</h3>
            <a href="{{ route('admin.especies.bodega.traslado.detalle.crear', $traslado) }}" class="btn btn-sm btn-success">
                <i class="fas fa-plus mr-1"></i> Agregar detalle
            </a>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width:4%">#</th>
                        <th>Tipo de especie</th>
                        <th>Denominación</th>
                        <th class="text-center">Rango</th>
                        <th class="text-right">Cantidad</th>
                        <th class="text-center" style="width:8%">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($traslado->detalles as $detalle)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $detalle->lote->tipoEspecie->nombre ?? '—' }}</td>
                        <td>
                            ${{ number_format($detalle->lote->denominacion->valor ?? 0, 2) }}
                        </td>
                        <td class="text-center">
                            <span class="badge badge-secondary">
                                {{ number_format($detalle->numero_inicio) }} — {{ number_format($detalle->numero_fin) }}
                            </span>
                        </td>
                        <td class="text-right">{{ number_format($detalle->cantidad) }}</td>
                        <td class="text-center">
                            <form method="POST"
                                  action="{{ route('admin.especies.bodega.traslado.detalle.destroy', [$traslado, $detalle]) }}"
                                  id="del-det-{{ $detalle->id }}" style="display:inline">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-xs btn-danger"
                                    data-swal-delete
                                    data-form="del-det-{{ $detalle->id }}"
                                    data-msg="¿Eliminar este detalle ({{ number_format($detalle->numero_inicio) }}–{{ number_format($detalle->numero_fin) }})?">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center text-muted py-3">Sin detalles. Use el botón para agregar.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@stop
