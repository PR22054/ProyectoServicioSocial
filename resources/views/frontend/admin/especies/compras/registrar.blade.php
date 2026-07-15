@extends('frontend.layouts.admin')

@section('page_title', 'Registrar Compra')

@section('page_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-shopping-cart mr-2"></i>Registrar Compra
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Formulario para registrar una nueva <strong>compra de especies al Ministerio de Hacienda</strong>.
                Se capturará el número de factura, fecha, monto total y los lotes adquiridos.
            </p>
            <p class="text-muted mb-0">
                Cada lote corresponde a un tipo de especie con una denominación específica, una serie
                (A, B, C...) y uno o varios bloques de rangos de numeración consecutivos
                (ej. 33601&ndash;34000 y 887101&ndash;887300).
            </p>
        </div>
    </div>
@stop
