@extends('frontend.layouts.admin')

@section('page_title', 'Existencias en Bodega')

@section('page_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-boxes mr-2"></i>Existencias en Bodega
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Reporte del <strong>inventario actual en bodega central</strong> agrupado por tipo
                de especie y denominación, mostrando los rangos de números disponibles y la
                cantidad total pendiente de traslado a cualquier distrito.
            </p>
            <p class="text-muted mb-0">
                Se generará en PDF con el mismo formato de encabezado de la Alcaldía.
            </p>
        </div>
    </div>
@stop
