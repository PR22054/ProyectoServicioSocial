@extends('frontend.layouts.admin')
@section('page_title', 'Stock Disponible en Bodega')
@section('page_content')

    <div class="card">
        <div class="card-header"><h3 class="card-title">Filtros</h3></div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label>Tipo de especie</label>
                        <select name="tipo_especie_id" class="form-control">
                            <option value="">Todos</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-primary btn-block" disabled>
                        <i class="fas fa-search mr-1"></i>Buscar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Existencias en bodega central</h3></div>
        <div class="card-body p-0">
            <table class="table table-striped table-sm mb-0">
                <thead>
                    <tr>
                        <th>Lote / Factura</th>
                        <th>Tipo de especie</th>
                        <th>Denominación</th>
                        <th>Serie</th>
                        <th>Rangos originales</th>
                        <th class="text-center">Anulaciones</th>
                        <th class="text-center">Trasladados</th>
                        <th class="text-center">Stock disponible</th>
                        <th class="text-right">Valor ($)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="9" class="text-center text-muted py-3">Sin datos aún</td></tr>
                </tbody>
            </table>
        </div>
    </div>

@stop
