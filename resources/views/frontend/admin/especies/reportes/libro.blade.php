@extends('frontend.layouts.admin')
@section('page_title', 'Reporte: Libro de Especies')

@section('page_content')

{{-- FILTROS PARA GENERAR EL REPORTE: LIBRO DE ESPECIES --}}
@if($errors->any())
<div class="alert alert-danger py-2">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
</div>
@endif

<div class="card">
    <div class="card-header"><h3 class="card-title">Filtros — Libro de Especies</h3></div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.especies.reportes.libro') }}" target="_blank">
            <input type="hidden" name="generar" value="1">
            <div class="row justify-content-center">
                <div class="col-md-2">
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
                        <select name="tipo_especie_id" id="tipo_especie_id"
                                class="form-control @error('tipo_especie_id') is-invalid @enderror" required>
                            <option value="">— Seleccione —</option>
                            @foreach($tipos as $t)
                                <option value="{{ $t->id }}" {{ request('tipo_especie_id') == $t->id ? 'selected' : '' }}>
                                    {{ $t->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Denominación <small class="text-muted">(opcional)</small></label>
                        <select name="denominacion_id" id="denominacion_id" class="form-control" disabled>
                            <option value="">— Todas —</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label>Mes <span class="text-danger">*</span></label>
                        <select name="mes" class="form-control @error('mes') is-invalid @enderror" required>
                            <option value="">— Mes —</option>
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

@push('js')
<script>
const denomAjax = '{{ route("admin.especies.ajax.denominaciones") }}';
//CARGA LAS DENOMINACIONES DEL TIPO SELECCIONADO VIA AJAX PARA EL FILTRO OPCIONAL
document.getElementById('tipo_especie_id').addEventListener('change', function () {
    const denomEl = document.getElementById('denominacion_id');
    const tipoId  = this.value;
    denomEl.innerHTML = '<option value="">Cargando...</option>';
    denomEl.disabled  = true;
    if (!tipoId) { denomEl.innerHTML = '<option value="">— Todas —</option>'; return; }

    fetch(denomAjax + '?tipo_especie_id=' + tipoId)
        .then(r => r.json())
        .then(data => {
            denomEl.innerHTML = '<option value="">— Todas —</option>';
            data.forEach(d => {
                const o = document.createElement('option');
                o.value = d.id;
                o.textContent = '$' + parseFloat(d.valor).toFixed(2);
                denomEl.appendChild(o);
            });
            denomEl.disabled = false;
        });
});
</script>
@endpush
