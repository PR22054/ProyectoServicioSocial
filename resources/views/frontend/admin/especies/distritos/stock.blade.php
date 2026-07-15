@extends('frontend.layouts.admin')

@section('page_title', 'Stock por Distrito')

@section('page_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-map-marker-alt mr-2"></i>Stock por Distrito
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Vista del <strong>inventario disponible en cada distrito</strong>: documentos recibidos
                por traslado desde bodega central, descontando los ya realizados (cobrados al ciudadano)
                y los anulados.
            </p>
            <p class="text-muted mb-0">
                Permitirá seleccionar un distrito y ver el detalle por tipo de especie y denominación,
                con los rangos de números disponibles para realizar.
            </p>
        </div>
    </div>
@stop
