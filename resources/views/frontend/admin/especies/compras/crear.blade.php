@extends('frontend.layouts.admin')
@section('page_title', 'Registrar Compra')

@section('page_content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Paso 1 — Datos de la compra</h3>
            <div class="card-tools">
                <a href="{{ route('admin.especies.compras.historial') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>Volver al historial
                </a>
            </div>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger py-2">
                    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif
            <form method="POST" action="{{ route('admin.especies.compras.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>N° de Factura <span class="text-danger">*</span></label>
                            <input type="text" name="numero_factura"
                                   class="form-control @error('numero_factura') is-invalid @enderror"
                                   value="{{ old('numero_factura') }}"
                                   placeholder="Ej. 06585" maxlength="50">
                            <small class="text-muted">Número de factura del Ministerio de Hacienda</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fecha de compra <span class="text-danger">*</span></label>
                            <input type="date" name="fecha"
                                   class="form-control @error('fecha') is-invalid @enderror"
                                   value="{{ old('fecha') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Observaciones <small class="text-muted">(opcional)</small></label>
                            <input type="text" name="observaciones" class="form-control"
                                   value="{{ old('observaciones') }}"
                                   placeholder="Ej. Según acuerdo N° 19..." maxlength="500">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>Guardar — agregar lotes
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@stop
