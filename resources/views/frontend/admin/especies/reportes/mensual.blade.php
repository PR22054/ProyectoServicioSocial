@extends('frontend.layouts.admin')
@section('page_title', 'Reporte Mensual')

@section('page_content')

{{-- FILTROS PARA GENERAR EL REPORTE: REPORTE MENSUAL POR DISTRITO --}}
@if($errors->any())
<div class="alert alert-danger py-2">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

<div class="card">
    <div class="card-header"><h3 class="card-title">Filtros — Reporte Mensual</h3></div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.especies.reportes.mensual') }}" target="_blank">
            <input type="hidden" name="generar" value="1">
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Mes <span class="text-danger">*</span></label>
                        <select name="mes" class="form-control @error('mes') is-invalid @enderror" required>
                            <option value="">— Seleccione mes —</option>
                            @foreach(['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'] as $i => $m)
                                <option value="{{ $i+1 }}" {{ request('mes') == $i+1 ? 'selected' : '' }}>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Año <span class="text-danger">*</span></label>
                        <input type="number" name="anio" class="form-control @error('anio') is-invalid @enderror"
                               value="{{ request('anio', date('Y')) }}" min="2020" required>
                    </div>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="form-group w-100">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-file-pdf mr-1"></i> Generar reporte
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body text-center text-muted py-4">
        <i class="fas fa-file-pdf fa-2x mb-2 d-block text-secondary"></i>
        Seleccione el mes y año y presione el botón para generar el PDF en una nueva pestaña.
    </div>
</div>

@stop
