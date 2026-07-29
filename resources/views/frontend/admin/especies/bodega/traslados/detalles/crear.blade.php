@extends('frontend.layouts.admin')
@section('page_title', 'Agregar Detalle — Traslado #' . $traslado->id)

@section('page_content')

    <div class="card mb-3">
        <div class="card-body py-2 d-flex justify-content-between align-items-center">
            <span>
                <strong>Traslado #{{ $traslado->id }}</strong> —
                {{ $traslado->distrito->nombre ?? '—' }} —
                {{ $traslado->fecha->format('d/m/Y') }}
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
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.especies.bodega.traslado.detalle.store', $traslado) }}">
                @csrf

                <div class="form-group">
                    <label>Tipo de especie <span class="text-danger">*</span></label>
                    <select id="tipo_especie_id" class="form-control">
                        <option value="">— Seleccione un tipo —</option>
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
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

                {{-- info de rangos disponibles --}}
                <div id="rangosInfo" class="alert alert-info py-2" style="display:none">
                    <strong>Rangos del lote:</strong> <span id="rangosList"></span>
                    <br><strong>Stock disponible:</strong> <span id="stockDisp"></span>
                    <span id="rangosUsadosRow" style="display:none">
                        <br><strong>Ya trasladados:</strong> <span id="rangosUsadosList"></span>
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
const ajaxUrl = '{{ route("admin.especies.ajax.lotes-stock") }}';

document.getElementById('tipo_especie_id').addEventListener('change', function () {
    const tipoId  = this.value;
    const loteEl  = document.getElementById('lote_id');
    const infoEl  = document.getElementById('rangosInfo');

    loteEl.innerHTML = '<option value="">Cargando...</option>';
    loteEl.disabled  = true;
    infoEl.style.display = 'none';

    if (!tipoId) {
        loteEl.innerHTML = '<option value="">— Seleccione un tipo primero —</option>';
        return;
    }

    fetch(ajaxUrl + '?tipo_especie_id=' + tipoId)
        .then(r => r.json())
        .then(lotes => {
            if (lotes.length === 0) {
                loteEl.innerHTML = '<option value="">Sin stock disponible para este tipo</option>';
            } else {
                loteEl.innerHTML = '<option value="">— Seleccione un lote —</option>';
                lotes.forEach(l => {
                    const opt       = document.createElement('option');
                    opt.value       = l.id;
                    opt.textContent = l.label;
                    opt.dataset.disponible   = l.disponible;
                    opt.dataset.rangos       = JSON.stringify(l.rangos);
                    opt.dataset.rangosUsados = JSON.stringify(l.rangos_usados);
                    loteEl.appendChild(opt);
                });
                loteEl.disabled = false;
            }
        });
});

document.getElementById('lote_id').addEventListener('change', function () {
    const selected = this.options[this.selectedIndex];
    const infoEl   = document.getElementById('rangosInfo');

    if (!this.value) {
        infoEl.style.display = 'none';
        return;
    }

    const rangos       = JSON.parse(selected.dataset.rangos || '[]');
    const rangosUsados = JSON.parse(selected.dataset.rangosUsados || '[]');
    const disp         = selected.dataset.disponible;

    document.getElementById('rangosList').textContent =
        rangos.map(r => r.inicio.toLocaleString() + ' – ' + r.fin.toLocaleString()).join(' | ');
    document.getElementById('stockDisp').textContent = Number(disp).toLocaleString();

    const usadosRow = document.getElementById('rangosUsadosRow');
    if (rangosUsados.length > 0) {
        document.getElementById('rangosUsadosList').textContent =
            rangosUsados.map(r => r.inicio.toLocaleString() + ' – ' + r.fin.toLocaleString()).join(' | ');
        usadosRow.style.display = '';
    } else {
        usadosRow.style.display = 'none';
    }

    infoEl.style.display = '';
});

function calcCantidad() {
    const ini = parseInt(document.getElementById('numero_inicio').value);
    const fin = parseInt(document.getElementById('numero_fin').value);
    const prev = document.getElementById('cantidad_preview');
    if (!isNaN(ini) && !isNaN(fin) && fin >= ini) {
        prev.value = (fin - ini + 1).toLocaleString();
    } else {
        prev.value = '';
    }
}

document.getElementById('numero_inicio').addEventListener('input', calcCantidad);
document.getElementById('numero_fin').addEventListener('input', calcCantidad);
</script>
@endpush
