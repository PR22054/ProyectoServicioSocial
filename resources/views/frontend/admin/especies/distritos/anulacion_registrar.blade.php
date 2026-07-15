@extends('frontend.layouts.admin')

@section('page_title', 'Registrar Anulación')

@section('page_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-ban mr-2"></i>Registrar Anulación
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Formulario para registrar la <strong>anulación de uno o varios documentos en un distrito</strong>.
                Las anulaciones ocurren cuando un documento sufre error de impresión, daño físico u otro
                motivo que impide su uso.
            </p>
            <p class="text-muted mb-0">
                Se deberá indicar el traslado de origen (de donde provienen los documentos), el distrito,
                el rango de números anulados y el motivo de la anulación. Un documento anulado no podrá
                registrarse como realización.
            </p>
        </div>
    </div>
@stop
