@extends('frontend.layouts.admin')
@section('page_title', 'Registrar Realización')

@section('page_content')

{{-- FORMULARIO PARA REGISTRAR LA REALIZACION DE DOCUMENTOS A UN CONTRIBUYENTE --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Nueva realización</h3>
            <div class="card-tools">
                <a href="{{ route('admin.especies.realizaciones.historial') }}" class="btn btn-sm btn-primary">
                    Volver al historial
                </a>
            </div>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger py-2">
                    @foreach(array_unique($errors->all()) as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.especies.realizaciones.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Distrito <span class="text-danger">*</span></label>
                            <select name="distrito_id" id="distrito_id"
                                    class="form-control @error('distrito_id') is-invalid @enderror" required>
                                <option value="">— Seleccione —</option>
                                @foreach($distritos as $d)
                                    <option value="{{ $d->id }}" {{ old('distrito_id') == $d->id ? 'selected' : '' }}>
                                        {{ $d->nombre }} ({{ $d->codigo }})
                                    </option>
                                @endforeach
                            </select>
                            @error('distrito_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Tipo de especie <span class="text-danger">*</span></label>
                            <select name="tipo_especie_id" id="tipo_especie_id"
                                    class="form-control @error('tipo_especie_id') is-invalid @enderror" required>
                                <option value="">— Seleccione un distrito primero —</option>
                                @foreach($tipos as $t)
                                    <option value="{{ $t->id }}" {{ old('tipo_especie_id') == $t->id ? 'selected' : '' }}>
                                        {{ $t->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tipo_especie_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                {{-- info de stock disponible --}}
                <div id="stockInfo" class="alert alert-info py-2" style="display:none">
                    <div class="row">
                        <div class="col-sm-3"><strong>Recibido:</strong> <span id="stockRecibido">—</span></div>
                        <div class="col-sm-3"><strong>Anulado:</strong> <span id="stockAnulado">—</span></div>
                        <div class="col-sm-3"><strong>Realizado:</strong> <span id="stockRealizado">—</span></div>
                        <div class="col-sm-3"><strong>Disponible:</strong> <span id="stockDisp" class="font-weight-bold">—</span></div>
                    </div>
                    <div class="mt-1"><strong>Rangos recibidos:</strong> <span id="stockRangos">—</span></div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Denominación <span class="text-danger">*</span></label>
                            <select name="denominacion_id" id="denominacion_id"
                                    class="form-control @error('denominacion_id') is-invalid @enderror" required disabled>
                                <option value="">— Seleccione tipo primero —</option>
                            </select>
                            @error('denominacion_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="fecha"
                                   class="form-control @error('fecha') is-invalid @enderror"
                                   value="{{ old('fecha', date('Y-m-d')) }}" required>
                            @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Número inicio <span class="text-danger">*</span></label>
                            <input type="number" name="numero_inicio" id="numero_inicio"
                                   class="form-control @error('numero_inicio') is-invalid @enderror"
                                   value="{{ old('numero_inicio') }}" min="1" required>
                            @error('numero_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Número fin <span class="text-danger">*</span></label>
                            <input type="number" name="numero_fin" id="numero_fin"
                                   class="form-control @error('numero_fin') is-invalid @enderror"
                                   value="{{ old('numero_fin') }}" min="1" required>
                            @error('numero_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Cantidad / Monto estimado</label>
                            <input type="text" id="montoPreview" class="form-control" readonly placeholder="—">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nombre del contribuyente <small class="text-muted">(opcional)</small></label>
                    <input type="text" name="nombre_contribuyente"
                           class="form-control" maxlength="200"
                           value="{{ old('nombre_contribuyente') }}"
                           placeholder="Nombre de quien realizó el trámite">
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-success">
                        Registrar realización
                    </button>
                </div>
            </form>
        </div>
    </div>

@stop

@push('js')
<script>
const ajaxUrl = '{{ route("admin.especies.ajax.realizacion-info") }}';
let valorDenom = 0;
//CARGA STOCK E INFO DEL DISTRITO+TIPO VIA AJAX Y CALCULA EL MONTO ESTIMADO

function cargarInfo(restoreDenomId) {
    const distId  = document.getElementById('distrito_id').value;
    const tipoId  = document.getElementById('tipo_especie_id').value;
    const infoEl  = document.getElementById('stockInfo');
    const denomEl = document.getElementById('denominacion_id');

    infoEl.style.display = 'none';
    denomEl.innerHTML = '<option value="">Cargando...</option>';
    denomEl.disabled = true;
    valorDenom = 0;

    if (!distId || !tipoId) {
        denomEl.innerHTML = '<option value="">— Seleccione distrito y tipo —</option>';
        return;
    }

    fetch(ajaxUrl + '?distrito_id=' + distId + '&tipo_especie_id=' + tipoId)
        .then(r => r.json())
        .then(data => {
            document.getElementById('stockRecibido').textContent  = Number(data.recibido).toLocaleString();
            document.getElementById('stockAnulado').textContent   = Number(data.anulado).toLocaleString();
            document.getElementById('stockRealizado').textContent = Number(data.realizado).toLocaleString();
            document.getElementById('stockDisp').textContent      = Number(data.disponible).toLocaleString();
            document.getElementById('stockRangos').textContent    =
                data.rangos.map(r => r.inicio.toLocaleString() + '–' + r.fin.toLocaleString()).join(' | ');
            infoEl.style.display = '';

            if (data.denominaciones.length === 0) {
                denomEl.innerHTML = '<option value="">Sin denominaciones activas</option>';
            } else {
                denomEl.innerHTML = '<option value="">— Seleccione —</option>';
                data.denominaciones.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.id;
                    opt.textContent = '$' + parseFloat(d.valor).toFixed(2);
                    opt.dataset.valor = d.valor;
                    denomEl.appendChild(opt);
                });
                denomEl.disabled = false;

                // Restaurar selección previa (old()) o auto-seleccionar si hay una sola
                if (restoreDenomId) {
                    denomEl.value = restoreDenomId;
                } else if (data.denominaciones.length === 1) {
                    denomEl.selectedIndex = 1;
                }
                const sel = denomEl.options[denomEl.selectedIndex];
                valorDenom = sel && sel.dataset.valor ? parseFloat(sel.dataset.valor) : 0;
                calcMonto();
            }
        });
}

document.getElementById('distrito_id').addEventListener('change', () => cargarInfo(null));
document.getElementById('tipo_especie_id').addEventListener('change', () => cargarInfo(null));

document.getElementById('denominacion_id').addEventListener('change', function () {
    const sel = this.options[this.selectedIndex];
    valorDenom = sel.dataset.valor ? parseFloat(sel.dataset.valor) : 0;
    calcMonto();
});

function calcMonto() {
    const ini = parseInt(document.getElementById('numero_inicio').value);
    const fin = parseInt(document.getElementById('numero_fin').value);
    const prev = document.getElementById('montoPreview');
    if (!isNaN(ini) && !isNaN(fin) && fin >= ini && valorDenom > 0) {
        const cant = fin - ini + 1;
        prev.value = cant.toLocaleString() + ' doc — $' + (cant * valorDenom).toLocaleString('es-SV', {minimumFractionDigits: 2});
    } else if (!isNaN(ini) && !isNaN(fin) && fin >= ini) {
        prev.value = (fin - ini + 1).toLocaleString() + ' doc';
    } else {
        prev.value = '';
    }
}

document.getElementById('numero_inicio').addEventListener('input', calcMonto);
document.getElementById('numero_fin').addEventListener('input', calcMonto);

// Auto-restaurar estado tras validación fallida (old())
document.addEventListener('DOMContentLoaded', function () {
    const distId = document.getElementById('distrito_id').value;
    const tipoId = document.getElementById('tipo_especie_id').value;
    if (distId && tipoId) {
        cargarInfo({{ old('denominacion_id') ?? 'null' }});
    }
    calcMonto();
});
</script>
@endpush
