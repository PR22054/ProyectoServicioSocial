@extends('frontend.layouts.admin')
@section('page_title', 'Historial de Traslados')
@section('page_content')

    <div class="card">
        <div class="card-header"><h3 class="card-title">Filtros</h3></div>
        <div class="card-body">
            <div class="row align-items-end">
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label>Distrito</label>
                        <select name="distrito_id" class="form-control">
                            <option value="">Todos</option>
                            <option>Distrito Metapán</option>
                            <option>Distrito Masahuat</option>
                            <option>Distrito Santa Rosa Guachipilín</option>
                            <option>Distrito Texistepeque</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label>Fecha desde</label>
                        <input type="date" name="fecha_desde" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <label>Fecha hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="button" class="btn btn-primary btn-block" disabled>
                        <i class="fas fa-search mr-1"></i>Buscar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Resultados</h3></div>
        <div class="card-body p-0">
            <table class="table table-striped table-sm mb-0">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Distrito</th>
                        <th class="text-center">Documentos enviados</th>
                        <th>Usuario</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="5" class="text-center text-muted py-3">Sin datos aún</td></tr>
                </tbody>
            </table>
        </div>
    </div>

@stop
