@extends('frontend.layouts.admin')

@section('page_title', 'Historial de Traslados')

@section('page_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-exchange-alt mr-2"></i>Historial de Traslados
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Reporte detallado de <strong>todos los traslados desde bodega central hacia los distritos</strong>
                en un periodo seleccionado. Muestra fecha, distrito destino, tipo de especie,
                denominación, serie y rangos de números enviados.
            </p>
            <p class="text-muted mb-0">
                Filtrable por distrito y rango de fechas. Se generará en PDF con totales de
                documentos trasladados por tipo.
            </p>
        </div>
    </div>
@stop
