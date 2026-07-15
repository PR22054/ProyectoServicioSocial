@extends('frontend.layouts.admin')

@section('page_title', 'Historial de Traslados')

@section('page_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-clipboard-list mr-2"></i>Historial de Traslados
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Listado de todos los <strong>traslados realizados desde la bodega central a los distritos</strong>,
                con detalle de fecha, distrito destino, lotes enviados y rangos de numeración.
            </p>
            <p class="text-muted mb-0">
                Permitirá filtrar por fecha y distrito, y consultar el detalle completo de cada traslado,
                incluyendo el usuario que lo registró.
            </p>
        </div>
    </div>
@stop
