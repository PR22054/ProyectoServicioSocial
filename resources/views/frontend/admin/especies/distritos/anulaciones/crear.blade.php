@extends('frontend.layouts.admin')
@section('page_title', 'Registrar Anulación')

@section('page_content')

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Nueva anulación</h3>
            <a href="{{ route('admin.especies.distritos.anulaciones.historial') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Volver al historial
            </a>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger py-2">
                    @foreach(array_unique($errors->all()) as $e)
                        <div>{{ $e }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.especies.distritos.anulaciones.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Distrito <span class="text-danger">*</span></label>
                            <select id="distrito_id" name="distrito_id"
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
                            <select id="tipo_especie_id" class="form-control" disabled>
                                <option value="">— Seleccione un distrito primero —</option>
                                @foreach($tipos as $t)
                                    <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Detalle de traslado (lote recibido) <span class="text-danger">*</span></label>
                    <select name="traslado_detalle_id" id="traslado_detalle_id"
                            class="form-control @error('traslado_detalle_id') is-invalid @enderror" required disabled>
                        <option value="">— Seleccione tipo y distrito primero —</option>
                    </select>
                    @error('traslado_detalle_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- info del detalle seleccionado --}}
                <div id="detalleInfo" class="alert alert-info py-2" style="display:none">
                    <strong>Rango del detalle:</strong>
                    <span id="detalleRango"></span>
                    <br><strong>Disponible para anular:</strong>
                    <span id="detalleDisp"></span>
                    <span id="yaAnuladosRow" style="display:none">
                        <br><strong>Ya anulados:</strong> <span id="yaAnuladosList"></span>
                    </span>
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
                            <label>Cantidad</label>
                            <input type="text" id="cantidad_preview" class="form-control" readonly placeholder="—">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fecha <span class="text-danger">*</span></label>
                            <input type="date" name="fecha"
                                   class="form-control @error('fecha') is-invalid @enderror"
                                   value="{{ old('fecha', date('Y-m-d')) }}" required>
                            @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Motivo <small class="text-muted">(opcional)</small></label>
                            <input type="text" name="motivo"
                                   class="form-control" maxlength="255"
                                   value="{{ old('motivo') }}"
                                   placeholder="Ej. Documentos dañados, extraviados...">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.especies.distritos.anulaciones.historial') }}" class="btn btn-secondary">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-ban mr-1"></i> Registrar anulación
                    </button>
                </div>
            </form>
        </div>
    </div>

@stop

@push('js')
<script>
const ajaxUrl = '{{ route("admin.especies.ajax.detalles-disponibles") }}';

const distritoEl = document.getElementById('distrito_id');
const tipoEl     = document.getElementById('tipo_especie_id');
const detalleEl  = document.getElementById('traslado_detalle_id');
const infoEl     = document.getElementById('detalleInfo');

function cargarDetalles() {
    const distId = distritoEl.value;
    const tipoId = tipoEl.value;
    detalleEl.innerHTML = '<option value="">Cargando...</option>';
    detalleEl.disabled  = true;
    infoEl.style.display = 'none';

    if (!distId || !tipoId) {
        detalleEl.innerHTML = '<option value="">— Seleccione distrito y tipo primero —</option>';
        return;
    }

    fetch(ajaxUrl + '?distrito_id=' + distId + '&tipo_especie_id=' + tipoId)
        .then(r => r.json())
        .then(items => {
            if (items.length === 0) {
                detalleEl.innerHTML = '<option value="">Sin detalles disponibles</option>';
            } else {
                detalleEl.innerHTML = '<option value="">— Seleccione —</option>';
                items.forEach(item => {
                    const opt = document.createElement('option');
                    opt.value = item.id;
                    opt.textContent = item.label;
                    opt.dataset.inicio      = item.inicio;
                    opt.dataset.fin         = item.fin;
                    opt.dataset.disponible  = item.disponible;
                    opt.dataset.yaAnulados  = JSON.stringify(item.ya_anulados);
                    detalleEl.appendChild(opt);
                });
                detalleEl.disabled = false;
            }
        });
}

distritoEl.addEventListener('change', function () {
    tipoEl.disabled = !this.value;
    if (this.value) tipoEl.disabled = false;
    cargarDetalles();
});

tipoEl.addEventListener('change', cargarDetalles);

// Habilita tipo_especie cuando hay distrito
distritoEl.addEventListener('change', function () {
    tipoEl.disabled = !this.value;
});

// Muestra info al seleccionar detalle
detalleEl.addEventListener('change', function () {
    if (!this.value) { infoEl.style.display = 'none'; return; }
    const sel        = this.options[this.selectedIndex];
    const yaAnulados = JSON.parse(sel.dataset.yaAnulados || '[]');

    document.getElementById('detalleRango').textContent =
        Number(sel.dataset.inicio).toLocaleString() + ' – ' + Number(sel.dataset.fin).toLocaleString();
    document.getElementById('detalleDisp').textContent =
        Number(sel.dataset.disponible).toLocaleString();

    const yaRow = document.getElementById('yaAnuladosRow');
    if (yaAnulados.length > 0) {
        document.getElementById('yaAnuladosList').textContent =
            yaAnulados.map(n => n.inicio.toLocaleString() + ' – ' + n.fin.toLocaleString()).join(' | ');
        yaRow.style.display = '';
    } else {
        yaRow.style.display = 'none';
    }
    infoEl.style.display = '';
});

// Preview cantidad
function calcCantidad() {
    const ini  = parseInt(document.getElementById('numero_inicio').value);
    const fin  = parseInt(document.getElementById('numero_fin').value);
    const prev = document.getElementById('cantidad_preview');
    prev.value = (!isNaN(ini) && !isNaN(fin) && fin >= ini)
        ? (fin - ini + 1).toLocaleString() : '';
}
document.getElementById('numero_inicio').addEventListener('input', calcCantidad);
document.getElementById('numero_fin').addEventListener('input', calcCantidad);

// Estado inicial
tipoEl.disabled = true;
</script>
@endpush
