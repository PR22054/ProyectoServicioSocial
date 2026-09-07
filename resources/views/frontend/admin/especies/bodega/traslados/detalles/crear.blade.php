@extends('frontend.layouts.admin')
@section('page_title', 'Agregar Detalle — Traslado #' . $traslado->id)

@section('page_content')

{{-- FORMULARIO PARA AGREGAR UN LOTE Y RANGO AL TRASLADO; EL AJAX CAMBIA SEGUN EL TIPO --}}
    <div class="card mb-3">
        <div class="card-body py-2 d-flex justify-content-between align-items-center">
            <span>
                <strong>Traslado #{{ $traslado->id }}</strong>
                @if($traslado->tipo === 'bodega_distrito')
                    <span class="badge badge-success">Bodega → Distrito</span>
                    — {{ $traslado->distrito->nombre ?? '—' }}
                @elseif($traslado->tipo === 'distrito_bodega')
                    <span class="badge badge-warning">Devolución</span>
                    — {{ $traslado->origenDistrito->nombre ?? '—' }} → Bodega
                @else
                    <span class="badge badge-info">Entre Distritos</span>
                    — {{ $traslado->origenDistrito->nombre ?? '—' }} → {{ $traslado->distrito->nombre ?? '—' }}
                @endif
                — {{ $traslado->fecha->format('d/m/Y') }}
            </span>
            <a href="{{ route('admin.especies.bodega.traslado.show', $traslado) }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3 class="card-title">Nuevo detalle</h3></div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger py-2">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.especies.bodega.traslado.detalle.store', $traslado) }}">
                @csrf

                <div class="form-group">
                    <label>Tipo de especie <span class="text-danger">*</span></label>
                    <select id="tipo_especie_id" name="tipo_especie_id" class="form-control">
                        <option value="">— Seleccione un tipo —</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}" {{ old('tipo_especie_id') == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Lote <span class="text-danger">*</span></label>
                    <select name="lote_id" id="lote_id" class="form-control @error('lote_id') is-invalid @enderror" required disabled>
                        <option value="">— Seleccione un tipo primero —</option>
                    </select>
                    @error('lote_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div id="rangosInfo" class="alert alert-info py-2" style="display:none">
                    <strong>Rangos disponibles:</strong> <span id="rangosList"></span>
                    <br><strong>Stock disponible:</strong> <span id="stockDisp"></span>
                    <span id="rangosUsadosRow" style="display:none">
                        <br><strong>Ya transferidos:</strong> <span id="rangosUsadosList"></span>
                    </span>
                </div>

                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Número inicio <span class="text-danger">*</span></label>
                            <input type="number" name="numero_inicio" id="numero_inicio"
                                   class="form-control @error('numero_inicio') is-invalid @enderror"
                                   value="{{ old('numero_inicio') }}" min="1" required>
                            @error('numero_inicio')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <label>Número fin <span class="text-danger">*</span></label>
                            <input type="number" name="numero_fin" id="numero_fin"
                                   class="form-control @error('numero_fin') is-invalid @enderror"
                                   value="{{ old('numero_fin') }}" min="1" required>
                            @error('numero_fin')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="form-group w-100">
                            <label>Cantidad</label>
                            <input type="text" id="cantidad_preview" class="form-control" readonly placeholder="—">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.especies.bodega.traslado.show', $traslado) }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus mr-1"></i> Agregar detalle
                    </button>
                </div>
            </form>
        </div>
    </div>

@stop

@push('js')
<script>
// Tipo del traslado para elegir el endpoint correcto
const TRASLADO_TIPO       = '{{ $traslado->tipo }}';
const TRASLADO_ORIGEN_ID  = {{ $traslado->origen_distrito_id ?? 'null' }};

const ajaxBodegaUrl    = '{{ route("admin.especies.ajax.lotes-stock") }}';
const ajaxDistritoUrl  = '{{ route("admin.especies.ajax.lotes-distrito-stock") }}';

function mostrarInfoLote() {
    const loteEl = document.getElementById('lote_id');
    const infoEl = document.getElementById('rangosInfo');
    if (!loteEl.value) { infoEl.style.display = 'none'; return; }

    const selected     = loteEl.options[loteEl.selectedIndex];
    const rangos       = JSON.parse(selected.dataset.rangos || '[]');
    const rangosUsados = JSON.parse(selected.dataset.rangosUsados || '[]');

    document.getElementById('rangosList').textContent =
        rangos.map(r => r.inicio.toLocaleString() + ' – ' + r.fin.toLocaleString()).join(' | ');
    document.getElementById('stockDisp').textContent = Number(selected.dataset.disponible).toLocaleString();

    const usadosRow = document.getElementById('rangosUsadosRow');
    if (rangosUsados.length > 0) {
        document.getElementById('rangosUsadosList').textContent =
            rangosUsados.map(r => r.inicio.toLocaleString() + ' – ' + r.fin.toLocaleString()).join(' | ');
        usadosRow.style.display = '';
    } else {
        usadosRow.style.display = 'none';
    }
    infoEl.style.display = '';
}

function cargarLotes(restoreLoteId) {
    const tipoId = document.getElementById('tipo_especie_id').value;
    const loteEl = document.getElementById('lote_id');
    const infoEl = document.getElementById('rangosInfo');

    loteEl.innerHTML = '<option value="">Cargando...</option>';
    loteEl.disabled  = true;
    infoEl.style.display = 'none';

    if (!tipoId) {
        loteEl.innerHTML = '<option value="">— Seleccione un tipo primero —</option>';
        return;
    }

    // Endpoint y parametros segun tipo de traslado
    let url;
    if (TRASLADO_TIPO === 'bodega_distrito') {
        url = ajaxBodegaUrl + '?tipo_especie_id=' + tipoId;
    } else {
        url = ajaxDistritoUrl + '?tipo_especie_id=' + tipoId + '&distrito_id=' + TRASLADO_ORIGEN_ID;
    }

    fetch(url)
        .then(r => r.json())
        .then(lotes => {
            if (lotes.length === 0) {
                loteEl.innerHTML = '<option value="">Sin stock disponible para este tipo</option>';
            } else {
                loteEl.innerHTML = '<option value="">— Seleccione un lote —</option>';
                lotes.forEach(l => {
                    const opt             = document.createElement('option');
                    opt.value             = l.id;
                    opt.textContent       = l.label;
                    opt.dataset.disponible    = l.disponible;
                    opt.dataset.rangos        = JSON.stringify(l.rangos);
                    opt.dataset.rangosUsados  = JSON.stringify(l.rangos_usados);
                    loteEl.appendChild(opt);
                });
                loteEl.disabled = false;

                if (restoreLoteId) {
                    loteEl.value = restoreLoteId;
                    mostrarInfoLote();
                }
            }
        });
}

document.getElementById('tipo_especie_id').addEventListener('change', () => cargarLotes(null));
document.getElementById('lote_id').addEventListener('change', mostrarInfoLote);

function calcCantidad() {
    const ini  = parseInt(document.getElementById('numero_inicio').value);
    const fin  = parseInt(document.getElementById('numero_fin').value);
    const prev = document.getElementById('cantidad_preview');
    prev.value = (!isNaN(ini) && !isNaN(fin) && fin >= ini)
        ? (fin - ini + 1).toLocaleString() : '';
}

document.getElementById('numero_inicio').addEventListener('input', calcCantidad);
document.getElementById('numero_fin').addEventListener('input', calcCantidad);

document.addEventListener('DOMContentLoaded', function () {
    const tipoId = document.getElementById('tipo_especie_id').value;
    if (tipoId) cargarLotes({{ old('lote_id') ?? 'null' }});
    calcCantidad();
});
</script>
@endpush
