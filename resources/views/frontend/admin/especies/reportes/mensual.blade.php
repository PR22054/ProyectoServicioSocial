@extends('frontend.layouts.admin')
@section('page_title', 'Reporte Mensual por Distrito')
@section('page_content')

    <div class="card">
        <div class="card-header"><h3 class="card-title">Filtros</h3></div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label>Mes <span class="text-danger">*</span></label>
                        <select name="mes" class="form-control">
                            <option value="">— Seleccione —</option>
                            @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $m)
                                <option value="{{ $i+1 }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label>Año <span class="text-danger">*</span></label>
                        <input type="number" name="anio" class="form-control" placeholder="{{ date('Y') }}" min="2020">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary btn-block" disabled>
                        <i class="fas fa-file-pdf mr-1"></i>Generar reporte
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body text-center text-muted py-4">
            Genera un resumen mensual por distrito: saldo inicial, traslados recibidos, realizaciones, anulaciones y saldo final.
        </div>
    </div>

@stop
