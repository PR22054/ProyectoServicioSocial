@extends('frontend.layouts.admin')

@section('page_title', 'Denominaciones')

@section('page_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-dollar-sign mr-2"></i>Denominaciones
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Gestión de los <strong>valores monetarios</strong> asociados a cada tipo de especie.
                Cada tipo puede tener una o varias denominaciones. Por ejemplo, el Fondo Vialidad
                maneja denominaciones de $0.57, $0.69, $1.14, $1.71, $2.29, $2.86 y $3.43.
            </p>
            <p class="text-muted mb-0">
                Aquí se podrán agregar nuevas denominaciones a un tipo existente y activar o desactivar
                las que ya no estén en uso.
            </p>
        </div>
    </div>
@stop
