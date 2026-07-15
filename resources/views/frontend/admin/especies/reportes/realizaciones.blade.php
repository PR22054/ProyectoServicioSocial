@extends('frontend.layouts.admin')

@section('page_title', 'Realizaciones por Periodo')

@section('page_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-receipt mr-2"></i>Realizaciones por Periodo
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Reporte de todos los <strong>cobros de especies realizados en un rango de fechas</strong>,
                con detalle del número de serie, tipo de especie, denominación, distrito, nombre
                del contribuyente y monto cobrado.
            </p>
            <p class="text-muted mb-0">
                Filtrable por distrito, tipo de especie y rango de fechas. Se generará en PDF
                con totales por tipo y monto global del periodo.
            </p>
        </div>
    </div>
@stop
