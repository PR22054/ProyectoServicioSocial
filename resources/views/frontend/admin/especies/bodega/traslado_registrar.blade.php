@extends('frontend.layouts.admin')

@section('page_title', 'Registrar Traslado')

@section('page_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-truck mr-2"></i>Registrar Traslado
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Formulario para registrar el <strong>envío de especies desde la bodega central hacia un distrito</strong>.
                Se seleccionará el distrito de destino, la fecha y los detalles del traslado.
            </p>
            <p class="text-muted mb-0">
                Cada línea del traslado indica qué lote se envía y el rango de números trasladados
                (ej. del lote X, números 33601 al 33700). El sistema validará que los números
                existan en bodega y no hayan sido trasladados previamente.
            </p>
        </div>
    </div>
@stop
