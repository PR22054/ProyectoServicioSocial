@extends('frontend.layouts.admin')

@section('page_title', 'Registro de Realización')

@section('page_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-cash-register mr-2"></i>Registro de Realización
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Formulario para registrar el <strong>cobro de una especie municipal a un ciudadano</strong>.
                El cajero ingresará el número de serie del documento físico, el tipo de especie,
                la denominación, el distrito, el nombre del contribuyente y el monto cobrado.
            </p>
            <p class="text-muted mb-0">
                El sistema validará que el número de serie pertenezca a un lote trasladado al distrito
                correspondiente, que no haya sido realizado previamente y que no esté anulado.
            </p>
        </div>
    </div>
@stop
