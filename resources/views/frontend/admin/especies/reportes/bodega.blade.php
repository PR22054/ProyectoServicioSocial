@extends('frontend.layouts.admin')
@section('page_title', 'Existencias en Bodega')
@section('page_content')

    <div class="card">
        <div class="card-header"><h3 class="card-title">Filtros</h3></div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label>Fecha de corte <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_corte" class="form-control">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label>Tipo de especie</label>
                        <select name="tipo_especie_id" class="form-control">
                            <option value="">Todos</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <button type="button" class="btn btn-primary btn-block" disabled>
                        <i class="fas fa-file-pdf mr-1"></i>Generar reporte
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body text-center text-muted py-4">
            Muestra lotes disponibles en bodega, rangos, series, cantidad y valor total a la fecha de corte indicada.
        </div>
    </div>

@stop
