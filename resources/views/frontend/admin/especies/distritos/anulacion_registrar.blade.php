@extends('frontend.layouts.admin')
@section('page_title', 'Registrar Anulación')
@section('page_content')

    <div class="card">
        <div class="card-header"><h3 class="card-title">Datos de la anulación</h3></div>
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
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
                        <div class="form-group">
                            <label>Tipo de especie <span class="text-danger">*</span></label>
                            <select name="tipo_especie_id" class="form-control">
                                <option value="">— Seleccione —</option>
                            </select>
                            <small class="text-muted">Usado para filtrar el traslado de origen.</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Traslado de origen <span class="text-danger">*</span></label>
                            <select name="traslado_detalle_id" class="form-control">
                                <option value="">— Seleccione distrito y tipo primero —</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Número inicio <span class="text-danger">*</span></label>
                            <input type="number" name="numero_inicio" class="form-control" placeholder="Ej. 33601" min="1">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Número fin <span class="text-danger">*</span></label>
                            <input type="number" name="numero_fin" class="form-control" placeholder="Igual a inicio si es uno solo" min="1">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Fecha de anulación <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Motivo <small class="text-muted">(opcional)</small></label>
                            <input type="text" name="motivo" class="form-control" placeholder="Ej. Error de impresión">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" disabled>
                    <i class="fas fa-save mr-1"></i>Registrar anulación
                </button>
            </form>
        </div>
    </div>

@stop
