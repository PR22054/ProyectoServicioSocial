@extends('frontend.layouts.admin')
@section('page_title', 'Stock por Distrito')
@section('page_content')

    <div class="card">
        <div class="card-header"><h3 class="card-title">Filtros</h3></div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <div class="form-group mb-0">
                        <label>Distrito <span class="text-danger">*</span></label>
                        <select name="distrito_id" class="form-control">
                            <option value="">— Seleccione —</option>
                            <option>Distrito Metapán</option>
                            <option>Distrito Masahuat</option>
                            <option>Distrito Santa Rosa Guachipilín</option>
                            <option>Distrito Texistepeque</option>
                        </select>
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
                        <i class="fas fa-search mr-1"></i>Consultar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Existencias por distrito</h3></div>
        <div class="card-body p-0">
            <table class="table table-striped table-sm mb-0">
                <thead>
                    <tr>
                        <th>Tipo de especie</th>
                        <th>Lote / Serie</th>
                        <th>Rango recibido</th>
                        <th class="text-center">Realizados</th>
                        <th class="text-center">Anulados</th>
                        <th class="text-center">Disponible real</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="6" class="text-center text-muted py-3">Seleccione un distrito para ver su stock</td></tr>
                </tbody>
            </table>
        </div>
    </div>

@stop
