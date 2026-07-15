@extends('frontend.layouts.admin')
@section('page_title', 'Registro de Realización')
@section('page_content')

    <div class="card">
        <div class="card-header"><h3 class="card-title">Datos de la realización</h3></div>
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tipo de especie <span class="text-danger">*</span></label>
                            <select name="tipo_especie_id" class="form-control">
                                <option value="">— Seleccione —</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Denominación <span class="text-danger">*</span></label>
                            <select name="denominacion_id" class="form-control">
                                <option value="">— Seleccione tipo primero —</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Número de serie <span class="text-danger">*</span></label>
                            <input type="number" name="numero_serie" class="form-control" placeholder="Ingrese el número del documento" min="1">
                        </div>
                    </div>
                </div>
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
                            <label>Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="fecha" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Monto cobrado ($) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                                <input type="number" name="monto_cobrado" class="form-control" placeholder="0.00" step="0.01" min="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Nombre del contribuyente <small class="text-muted">(opcional)</small></label>
                            <input type="text" name="nombre_contribuyente" class="form-control" placeholder="Nombre completo">
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" disabled>
                    <i class="fas fa-save mr-1"></i>Registrar realización
                </button>
            </form>
        </div>
    </div>

@stop
