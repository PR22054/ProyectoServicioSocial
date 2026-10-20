@extends('frontend.layouts.admin')
@section('page_title', 'Reporte: Especies Realizadas')

@section('page_content')

{{-- FILTROS PARA GENERAR EL REPORTE: ESPECIES REALIZADAS --}}
@if($errors->any())
<div class="alert alert-danger py-2">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

<div class="card">
    <div class="card-header"><h3 class="card-title">Filtros — Especies Realizadas</h3></div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.especies.reportes.realizaciones') }}" target="_blank">
            <input type="hidden" name="generar" value="1">
            <div class="row justify-content-center">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Distrito <span class="text-danger">*</span></label>
                        <select name="distrito_id" class="form-control @error('distrito_id') is-invalid @enderror" required>
                            <option value="">— Seleccione —</option>
                            @foreach($distritos as $d)
                                <option value="{{ $d->id }}" {{ request('distrito_id') == $d->id ? 'selected' : '' }}>
                                    {{ $d->nombre }} ({{ $d->codigo }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Tipo de especie <span class="text-danger">*</span></label>
                        <select name="tipo_especie_id" class="form-control @error('tipo_especie_id') is-invalid @enderror" required>
                            <option value="">— Seleccione —</option>
                            <option value="todos" {{ request('tipo_especie_id') === 'todos' ? 'selected' : '' }}>Todas las especies</option>
                            @foreach($tipos as $t)
                                <option value="{{ $t->id }}" {{ request('tipo_especie_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->nombre }}{{ $t->activo ? "" : " (inactivo)" }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
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
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Año <span class="text-danger">*</span></label>
                        <input type="number" name="anio" class="form-control @error('anio') is-invalid @enderror"
                               value="{{ request('anio', date('Y')) }}" min="2020" required>
                    </div>
                </div>
            </div>
            <div class="row mt-1">
                <div class="col-md-6 d-flex align-items-center">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="detallado" name="detallado" value="1" {{ request('detallado') ? 'checked' : '' }}>
                        <label class="custom-control-label" for="detallado">Reporte detallado
                            <small class="text-muted">(cada realización y nula con su fecha, serie y rango)</small></label>
                    </div>
                </div>
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
