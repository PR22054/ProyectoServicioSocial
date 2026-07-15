@extends('frontend.layouts.admin')
@section('page_title', 'Libro de Especies')
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
                        <label>Tipo de especie</label>
                        <select name="tipo_especie_id" class="form-control">
                            <option value="">Todos</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Denominación <small class="text-muted">(opcional)</small></label>
                        <select name="denominacion_id" class="form-control">
                            <option value="">Todas</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mb-0">
                        <label>Mes <span class="text-danger">*</span></label>
                        <select name="mes" class="form-control">
                            <option value="">— Mes —</option>
                            @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $m)
                                <option value="{{ $i+1 }}">{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group mb-0">
                        <label>Año <span class="text-danger">*</span></label>
                        <input type="number" name="anio" class="form-control" placeholder="{{ date('Y') }}" min="2020">
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-primary btn-block" disabled>
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body text-center text-muted py-4">
            Seleccione los filtros y genere el reporte para visualizarlo aquí.
        </div>
    </div>

@stop
