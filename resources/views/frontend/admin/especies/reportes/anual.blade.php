@extends('frontend.layouts.admin')
@section('page_title', 'Reporte: Consolidado Anual por Distrito')

@section('page_content')

{{-- FILTROS PARA GENERAR EL CONSOLIDADO ANUAL: MATRIZ MESES x DENOMINACIONES --}}
@if($errors->any())
<div class="alert alert-danger py-2">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

<div class="card">
    <div class="card-header"><h3 class="card-title">Filtros — Consolidado Anual por Distrito</h3></div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.especies.reportes.anual') }}" target="_blank">
            <input type="hidden" name="generar" value="1">
            <div class="row">
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Tipo de especie <span class="text-danger">*</span></label>
                        <select name="tipo_especie_id" class="form-control @error('tipo_especie_id') is-invalid @enderror" required>
                            <option value="">— Seleccione —</option>
                            @foreach($tipos as $t)
                                <option value="{{ $t->id }}" {{ request('tipo_especie_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->nombre }}{{ $t->activo ? "" : " (inactivo)" }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Año <span class="text-danger">*</span></label>
                        <input type="number" name="anio" class="form-control @error('anio') is-invalid @enderror"
                               value="{{ request('anio', date('Y')) }}" min="2020" required>
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
        <i class="fas fa-table fa-2x mb-2 d-block text-secondary"></i>
        Genera la matriz de los 12 meses del año contra cada denominación, con su matriz de nulas.
    </div>
</div>

@stop
