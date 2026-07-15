@extends('frontend.layouts.admin')

@section('page_title', 'Stock Disponible en Bodega')

@section('page_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-warehouse mr-2"></i>Stock Disponible en Bodega
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Resumen del <strong>inventario actual en bodega central</strong>: documentos comprados
                al Ministerio de Hacienda que aún no han sido trasladados a ningún distrito.
            </p>
            <p class="text-muted mb-0">
                Se mostrará agrupado por tipo de especie y denominación, con los rangos de números
                disponibles y la cantidad total pendiente de traslado.
            </p>
        </div>
    </div>
@stop
