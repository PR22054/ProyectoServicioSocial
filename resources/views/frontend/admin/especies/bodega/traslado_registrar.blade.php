@extends('frontend.layouts.admin')
@section('page_title', 'Registrar Traslado')
@section('page_content')

    {{-- Cabecera del traslado --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Datos del traslado</h3></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Distrito de destino <span class="text-danger">*</span></label>
                        <select name="distrito_id" class="form-control">
                            <option value="">— Seleccione —</option>
                            <option>Distrito Metapán</option>
                            <option>Distrito Masahuat</option>
                            <option>Distrito Santa Rosa Guachipilín</option>
                            <option>Distrito Texistepeque</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha de traslado <span class="text-danger">*</span></label>
                        <input type="date" name="fecha" class="form-control">
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Observaciones <small class="text-muted">(opcional)</small></label>
                        <input type="text" name="observaciones" class="form-control" placeholder="Notas adicionales">
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Detalle del traslado --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Detalle de documentos a trasladar</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-success" disabled>
                    <i class="fas fa-plus mr-1"></i>Agregar línea
                </button>
            </div>
        </div>
        <div class="card-body">

            {{-- Línea de detalle #1 --}}
            <div class="border rounded p-3 mb-3 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Línea #1</strong>
                    <button type="button" class="btn btn-sm btn-outline-danger" disabled>
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tipo de especie <span class="text-danger">*</span></label>
                            <select name="detalles[0][tipo_especie_id]" class="form-control">
                                <option value="">— Seleccione —</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Lote de origen <span class="text-danger">*</span></label>
                            <select name="detalles[0][lote_id]" class="form-control">
                                <option value="">— Seleccione tipo primero —</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Número inicio <span class="text-danger">*</span></label>
                            <input type="number" name="detalles[0][numero_inicio]" class="form-control" placeholder="Ej. 33601" min="1">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Número fin <span class="text-danger">*</span></label>
                            <input type="number" name="detalles[0][numero_fin]" class="form-control" placeholder="Ej. 33700" min="1">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>Cantidad</label>
                            <input type="number" class="form-control bg-white" placeholder="Auto" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i>
                            Stock disponible del lote: <strong>— documentos</strong>
                        </small>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary" disabled>
                <i class="fas fa-save mr-1"></i>Registrar traslado
            </button>
        </div>
    </div>

@stop
