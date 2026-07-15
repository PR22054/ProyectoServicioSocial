@extends('frontend.layouts.admin')

@section('page_title', 'Reporte Mensual por Distrito')

@section('page_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-calendar-alt mr-2"></i>Reporte Mensual por Distrito
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Resumen mensual por distrito que consolida las <strong>realizaciones, anulaciones
                y existencias</strong> de cada tipo de especie durante el mes seleccionado.
            </p>
            <p class="text-muted mb-0">
                Incluirá el saldo inicial del mes, los movimientos del periodo (traslados recibidos,
                realizaciones y anulaciones) y el saldo final. Se generará en PDF con el encabezado
                oficial de la Alcaldía.
            </p>
        </div>
    </div>
@stop
