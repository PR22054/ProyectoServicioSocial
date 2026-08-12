@extends('frontend.layouts.admin')
@section('page_title', 'Reporte: Existencias por Distrito')

@section('page_content')

{{-- FILTROS PARA GENERAR EL REPORTE: EXISTENCIAS POR DISTRITO --}}
@if($errors->any())
<div class="alert alert-danger py-2">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

<div class="card">
    <div class="card-header"><h3 class="card-title">Filtros — Existencias por Distrito</h3></div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.especies.reportes.distritos') }}" target="_blank">
            <input type="hidden" name="generar" value="1">
            <div class="row">
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
                            @foreach($tipos as $t)
                                <option value="{{ $t->id }}" {{ request('tipo_especie_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Fecha de corte <span class="text-danger">*</span></label>
                        <input type="date" name="fecha_corte" class="form-control @error('fecha_corte') is-invalid @enderror"
                               value="{{ request('fecha_corte', date('Y-m-d')) }}" required>
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
        Seleccione los filtros y presione el botón para generar el PDF en una nueva pestaña.
    </div>
</div>

@stop
