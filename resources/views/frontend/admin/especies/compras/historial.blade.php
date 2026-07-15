@extends('frontend.layouts.admin')

@section('page_title', 'Historial de Compras')

@section('page_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-history mr-2"></i>Historial de Compras
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Listado de todas las <strong>compras realizadas al Ministerio de Hacienda</strong>,
                con detalle del número de factura, fecha, monto total y los lotes contenidos
                en cada compra (tipo de especie, denominación, serie y rangos de numeración).
            </p>
            <p class="text-muted mb-0">
                Permitirá filtrar por fecha y consultar el detalle completo de cualquier compra anterior.
            </p>
        </div>
    </div>
@stop
