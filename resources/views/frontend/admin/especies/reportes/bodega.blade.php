@extends('frontend.layouts.admin')
@section('page_title', 'Reporte: Existencias en Bodega')

@section('page_content')

{{-- FILTROS PARA GENERAR EL REPORTE: EXISTENCIAS EN BODEGA --}}
@if($errors->any())
<div class="alert alert-danger py-2">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

<div class="card">
    <div class="card-header"><h3 class="card-title">Filtros — Existencias en Bodega</h3></div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.especies.reportes.bodega') }}" target="_blank">
            <input type="hidden" name="generar" value="1">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tipo de especie <span class="text-danger">*</span></label>
                        <select name="tipo_especie_id" class="form-control @error('tipo_especie_id') is-invalid @enderror" required>
                            <option value="">— Seleccione —</option>
                            @foreach($tipos as $t)
                                <option value="{{ $t->id }}" {{ request('tipo_especie_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Fecha desde <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_desde" class="form-control @error('fecha_desde') is-invalid @enderror"
                               value="{{ request('fecha_desde', date('Y-01-01')) }}" required>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Fecha hasta <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_hasta" class="form-control @error('fecha_hasta') is-invalid @enderror"
                               value="{{ request('fecha_hasta', date('Y-m-d')) }}" required>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3 ml-auto">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-file-pdf mr-1"></i> Generar reporte
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body text-center text-muted py-4">
        <i class="fas fa-file-pdf fa-2x mb-2 d-block text-secondary"></i>
        Seleccione los filtros y presione el botón para generar el PDF en una nueva pestaña.
    </div>
</div>

@stop
