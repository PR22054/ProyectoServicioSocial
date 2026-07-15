@extends('frontend.layouts.admin')

@section('page_title', 'Existencias por Distrito')

@section('page_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-map mr-2"></i>Existencias por Distrito
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Reporte del <strong>inventario disponible en cada distrito</strong>, agrupado por
                tipo de especie y denominación. Muestra el total recibido por traslado, los ya
                realizados, los anulados y la existencia actual.
            </p>
            <p class="text-muted mb-0">
                Permitirá filtrar por distrito específico o mostrar todos a la vez. Se generará en PDF.
            </p>
        </div>
    </div>
@stop
