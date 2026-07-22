@extends('frontend.layouts.admin')
@section('page_title', 'Compra — Factura N° ' . $compra->numero_factura)

@section('page_content')

    {{-- alertas --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('success_lote'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success_lote') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error_lote'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error_lote') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- cabecera --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Datos de la compra</h3>
            <div class="card-tools">
                <a href="{{ route('admin.especies.compras.historial') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>Historial
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <small class="text-muted d-block">N° Factura</small>
                    <strong>{{ $compra->numero_factura }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Fecha</small>
                    <strong>{{ $compra->fecha->format('d/m/Y') }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Monto total calculado</small>
                    <strong class="text-success">${{ number_format($compra->monto_total, 2) }}</strong>
                </div>
                <div class="col-md-3">
                    <small class="text-muted d-block">Registrada por</small>
                    <strong>{{ $compra->user->name ?? '—' }}</strong>
                </div>
                @if($compra->observaciones)
                <div class="col-md-12 mt-2">
                    <small class="text-muted d-block">Observaciones</small>
                    <span>{{ $compra->observaciones }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- lotes --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Paso 2 — Lotes ({{ $compra->lotes->count() }})</h3>
            <div class="card-tools">
                <a href="{{ route('admin.especies.compras.lotes.crear', $compra) }}"
                   class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i>Agregar lote
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @forelse($compra->lotes as $lote)
            <div class="p-3 border-bottom">
                <div class="row align-items-start">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Tipo de especie</small>
                        <strong>{{ $lote->tipoEspecie->nombre }}</strong>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted d-block">Denominación</small>
                        <strong>${{ number_format($lote->denominacion->valor, 2) }}</strong>
                    </div>
                    <div class="col-md-1">
                        <small class="text-muted d-block">Serie</small>
                        <span>{{ $lote->serie ?? '—' }}</span>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted d-block">Cantidad total</small>
                        <span>{{ number_format($lote->cantidad_total) }}</span>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted d-block">Valor total</small>
                        <span class="text-success font-weight-bold">
                            ${{ number_format($lote->cantidad_total * $lote->denominacion->valor, 2) }}
                        </span>
                    </div>
                    <div class="col-md-2 text-right">
                        <form method="POST"
                              action="{{ route('admin.especies.compras.lotes.destroy', [$compra, $lote]) }}"
                              id="del-lote-{{ $lote->id }}" style="display:inline">
                            @csrf @method('DELETE')
                        </form>
                        <button type="button" class="btn btn-xs btn-danger"
                                data-swal-delete
                                data-form="del-lote-{{ $lote->id }}"
                                data-msg="¿Eliminar este lote de {{ $lote->tipoEspecie->nombre }}?">
                            <i class="fas fa-trash"></i> Eliminar lote
                        </button>
                    </div>
                </div>

                {{-- rangos del lote --}}
                <div class="mt-2">
                    <small class="text-muted">Rangos:</small>
                    <div class="d-flex flex-wrap gap-1 mt-1">
                        @foreach($lote->rangos as $rango)
                            <span class="badge badge-light border">
                                {{ number_format($rango->numero_inicio, 0, '.', '') }}
                                →
                                {{ number_format($rango->numero_fin, 0, '.', '') }}
                                <small class="text-muted ml-1">
                                    ({{ number_format($rango->numero_fin - $rango->numero_inicio + 1) }} uds.)
                                </small>
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-muted">
                <i class="fas fa-box-open fa-2x mb-2 d-block"></i>
                Sin lotes aún — use el botón <strong>Agregar lote</strong> para comenzar.
            </div>
            @endforelse
        </div>
        @if($compra->lotes->count() > 0)
        <div class="card-footer text-right">
            <strong>Total de la compra:
                <span class="text-success">${{ number_format($compra->monto_total, 2) }}</span>
            </strong>
        </div>
        @endif
    </div>

@stop
