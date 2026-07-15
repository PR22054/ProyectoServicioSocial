@extends('frontend.layouts.admin')
@section('page_title', 'Registrar Compra')
@section('page_content')

    {{-- Cabecera de la compra --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Datos de la compra</h3></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha de compra <span class="text-danger">*</span></label>
                        <input type="date" name="fecha" class="form-control">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Número de factura <span class="text-danger">*</span></label>
                        <input type="text" name="numero_factura" class="form-control" placeholder="Ej. F-001234">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Monto total ($) <small class="text-muted">(opcional)</small></label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">$</span></div>
                            <input type="number" name="monto_total" class="form-control" placeholder="Se calcula de los lotes" step="0.01" min="0">
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Observaciones <small class="text-muted">(opcional)</small></label>
                        <textarea name="observaciones" class="form-control" rows="1" placeholder="Notas adicionales"></textarea>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Lotes de la compra --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Lotes</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-success" disabled>
                    <i class="fas fa-plus mr-1"></i>Agregar lote
                </button>
            </div>
        </div>
        <div class="card-body">

            {{-- Lote #1 (ejemplo de estructura) --}}
            <div class="border rounded p-3 mb-3 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Lote #1</strong>
                    <button type="button" class="btn btn-sm btn-outline-danger" disabled>
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tipo de especie <span class="text-danger">*</span></label>
                            <select name="lotes[0][tipo_especie_id]" class="form-control">
                                <option value="">— Seleccione —</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Denominación <span class="text-danger">*</span></label>
                            <select name="lotes[0][denominacion_id]" class="form-control">
                                <option value="">— Seleccione tipo primero —</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Serie <small class="text-muted">(opcional)</small></label>
                            <input type="text" name="lotes[0][serie]" class="form-control" placeholder="A, B, C…" maxlength="5">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Cantidad total</label>
                            <input type="number" name="lotes[0][cantidad_total]" class="form-control bg-white" placeholder="Auto" readonly>
                        </div>
                    </div>
                </div>

                {{-- Rangos del lote --}}
                <div class="mt-2">
                    <label class="font-weight-bold">Rangos <span class="text-danger">*</span></label>
                    <div class="row mb-1">
                        <div class="col-md-1 text-muted small pt-1">#</div>
                        <div class="col-md-4"><small class="text-muted">Número inicio</small></div>
                        <div class="col-md-4"><small class="text-muted">Número fin</small></div>
                        <div class="col-md-3"></div>
                    </div>
                    {{-- Rango #1 --}}
                    <div class="row mb-2 align-items-center">
                        <div class="col-md-1 text-muted">1</div>
                        <div class="col-md-4">
                            <input type="number" name="lotes[0][rangos][0][numero_inicio]" class="form-control form-control-sm" placeholder="Ej. 33601" min="1">
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="lotes[0][rangos][0][numero_fin]" class="form-control form-control-sm" placeholder="Ej. 34000" min="1">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-sm btn-outline-danger" disabled>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary mt-1" disabled>
                        <i class="fas fa-plus mr-1"></i>Agregar rango
                    </button>
                </div>
            </div>

        </div>
        <div class="card-footer text-right">
            <button type="submit" class="btn btn-primary" disabled>
                <i class="fas fa-save mr-1"></i>Registrar compra
            </button>
        </div>
    </div>

@stop
