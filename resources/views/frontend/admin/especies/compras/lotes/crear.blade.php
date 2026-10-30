{{--formulario para agregar o editar un lote de una compra: tipo, denominacion, serie y rangos--}}
@extends('frontend.layouts.admin')
@php
    $lote       = $lote ?? null;
    $trasladado = $trasladado ?? false;
    //filas de rangos: las que regresan tras un error, las del lote al editar o una vacia
    $filasRangos = array_values(old('rangos', $lote
        ? $lote->rangos->map(fn($r) => ['numero_inicio' => $r->numero_inicio, 'numero_fin' => $r->numero_fin])->all()
        : [['numero_inicio' => '', 'numero_fin' => '']]));
@endphp
@section('page_title', ($lote ? 'Editar Lote' : 'Agregar Lote') . ' — Factura N° ' . $compra->numero_factura)

@section('page_content')

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ $lote ? 'Editar lote de la' : 'Agregar lote a' }} compra N° {{ $compra->numero_factura }}</h3>
            <div class="card-tools">
                <a href="{{ route('admin.especies.compras.show', $compra) }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i>Volver a la compra
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

            @if($trasladado)
                <div class="alert alert-info py-2">
                    Este lote ya tiene traslados: su tipo, denominación y serie no se pueden cambiar; solo sus rangos,
                    siempre que sigan incluyendo los números ya trasladados.
                </div>
            @endif

            {{--datos del lote--}}
            <form method="POST" id="form-lote"
                  action="{{ $lote ? route('admin.especies.compras.lotes.update', [$compra, $lote]) : route('admin.especies.compras.lotes.store', $compra) }}">
                @csrf
                @if($lote) @method('PATCH') @endif
                {{--un lote trasladado envia sus valores fijos en campos ocultos--}}
                @if($trasladado)
                    <input type="hidden" name="tipo_especie_id" value="{{ $lote->tipo_especie_id }}">
                    <input type="hidden" name="denominacion_id" value="{{ $lote->denominacion_id }}">
                    <input type="hidden" name="serie" value="{{ $lote->serie }}">
                @endif

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Tipo de especie <span class="text-danger">*</span></label>
                            <select name="tipo_especie_id" id="tipo_especie_id" @disabled($trasladado)
                                    class="form-control @error('tipo_especie_id') is-invalid @enderror">
                                <option value="">— Seleccione —</option>
                                @foreach($tipos as $t)
                                    <option value="{{ $t->id }}" {{ old('tipo_especie_id', $lote?->tipo_especie_id) == $t->id ? 'selected' : '' }}>
                                        {{ $t->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Denominación <span class="text-danger">*</span></label>
                            <select name="denominacion_id" id="denominacion_id" @disabled($trasladado)
                                    class="form-control @error('denominacion_id') is-invalid @enderror">
                                <option value="">— Seleccione tipo primero —</option>
                            </select>
                            <small class="text-muted" id="den-loading" style="display:none">
                                <i class="fas fa-spinner fa-spin"></i> Cargando...
                            </small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Serie <small class="text-muted">(opcional)</small></label>
                            <input type="text" name="serie" class="form-control" @disabled($trasladado)
                                   value="{{ old('serie', $lote?->serie) }}"
                                   placeholder='Ej. "A", "B", "C"' maxlength="10">
                            <small class="text-muted">Letra o código que identifica el bloque físico</small>
                        </div>
                    </div>
                </div>

                {{--rangos dinamicos--}}
                <div class="card card-secondary card-outline mt-2">
                    <div class="card-header py-2">
                        <h5 class="card-title mb-0">Rangos de numeración</h5>
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-success" id="btn-add-rango">
                                <i class="fas fa-plus mr-1"></i>Agregar rango
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                        {{--rangos del lote; se pueden agregar varios--}}
                        <table class="table table-sm mb-0" id="tabla-rangos">
                            <thead>
                                <tr>
                                    <th style="width:5%">#</th>
                                    <th>Número inicio</th>
                                    <th>Número fin</th>
                                    <th class="text-right" style="width:20%">Cantidad</th>
                                    <th style="width:8%"></th>
                                </tr>
                            </thead>
                            <tbody id="rangos-body">
                                {{--filas de rangos; la primera no se puede eliminar--}}
                                @foreach($filasRangos as $i => $r)
                                <tr class="rango-row" data-index="{{ $i }}">
                                    <td class="text-center rango-num">{{ $i + 1 }}</td>
                                    <td>
                                        <input type="number" name="rangos[{{ $i }}][numero_inicio]"
                                               class="form-control form-control-sm rango-inicio
                                                      @error('rangos.' . $i . '.numero_inicio') is-invalid @enderror"
                                               value="{{ $r['numero_inicio'] ?? '' }}"
                                               min="1" placeholder="Ej. 257801">
                                    </td>
                                    <td>
                                        <input type="number" name="rangos[{{ $i }}][numero_fin]"
                                               class="form-control form-control-sm rango-fin
                                                      @error('rangos.' . $i . '.numero_fin') is-invalid @enderror"
                                               value="{{ $r['numero_fin'] ?? '' }}"
                                               min="1" placeholder="Ej. 260800">
                                    </td>
                                    <td class="text-right">
                                        <span class="rango-cantidad text-muted">—</span>
                                    </td>
                                    <td class="text-center">
                                        @if($i > 0)
                                            <button type="button" class="btn btn-xs btn-danger btn-eliminar-rango" title="Quitar este rango"><i class="fas fa-times"></i></button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-light">
                                    <td colspan="3" class="text-right font-weight-bold">Total:</td>
                                    <td class="text-right font-weight-bold" id="total-cantidad">0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-12 text-right">
                        <a href="{{ route('admin.especies.compras.show', $compra) }}"
                           class="btn btn-secondary mr-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i>{{ $lote ? 'Guardar cambios' : 'Guardar lote' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@stop

@push('js')
{{--denominaciones por tipo, filas de rangos y totales--}}
<script>
const AJAX_URL = '{{ route('admin.especies.ajax.denominaciones') }}';
const oldTipoId = '{{ old('tipo_especie_id', $lote?->tipo_especie_id) }}';
const oldDenId  = '{{ old('denominacion_id', $lote?->denominacion_id) }}';

//denominaciones AJAX
$('#tipo_especie_id').on('change', function () {
    var tipoId = $(this).val();
    var $den   = $('#denominacion_id');
    var $spin  = $('#den-loading');

    $den.empty().append('<option value="">— Cargando... —</option>');
    if (!tipoId) {
        $den.empty().append('<option value="">— Seleccione tipo primero —</option>');
        return;
    }

    $spin.show();
    $.getJSON(AJAX_URL, { tipo_especie_id: tipoId }, function (data) {
        $den.empty().append('<option value="">— Seleccione —</option>');
        $.each(data, function (i, d) {
            var sel = (d.id == oldDenId && tipoId == oldTipoId) ? 'selected' : '';
            $den.append('<option value="' + d.id + '" ' + sel + '>$' + parseFloat(d.valor).toFixed(2) + '</option>');
        });
        $spin.hide();
    }).fail(function () {
        $den.empty().append('<option value="">Error al cargar</option>');
        $spin.hide();
    });
});

//disparar si hay old() value al cargar la pagina
if (oldTipoId) {
    $('#tipo_especie_id').val(oldTipoId).trigger('change');
}

//rangos dinamicos; las filas precargadas ya muestran su cantidad
var rangoIndex = {{ count($filasRangos) }};
$('#rangos-body .rango-row').each(function () { calcularCantidad($(this)); });

//calcula la cantidad de una fila de rango
function calcularCantidad($row) {
    var inicio = parseInt($row.find('.rango-inicio').val()) || 0;
    var fin    = parseInt($row.find('.rango-fin').val())    || 0;
    var cant   = (inicio > 0 && fin >= inicio) ? (fin - inicio + 1) : 0;
    $row.find('.rango-cantidad').text(cant > 0 ? cant.toLocaleString('es-SV') : '—');
    recalcularTotal();
}

//suma la cantidad de todos los rangos
function recalcularTotal() {
    var total = 0;
    $('#rangos-body .rango-row').each(function () {
        var inicio = parseInt($(this).find('.rango-inicio').val()) || 0;
        var fin    = parseInt($(this).find('.rango-fin').val())    || 0;
        if (inicio > 0 && fin >= inicio) total += (fin - inicio + 1);
    });
    $('#total-cantidad').text(total > 0 ? total.toLocaleString('es-SV') : '0');
}

$(document).on('input', '.rango-inicio, .rango-fin', function () {
    calcularCantidad($(this).closest('tr'));
});

$('#btn-add-rango').on('click', function () {
    var i = rangoIndex++;
    var $row = $('<tr class="rango-row" data-index="' + i + '">' +
        '<td class="text-center rango-num">' + ($('#rangos-body .rango-row').length + 1) + '</td>' +
        '<td><input type="number" name="rangos[' + i + '][numero_inicio]" class="form-control form-control-sm rango-inicio" min="1" placeholder="Ej. 260801"></td>' +
        '<td><input type="number" name="rangos[' + i + '][numero_fin]"    class="form-control form-control-sm rango-fin"    min="1" placeholder="Ej. 261000"></td>' +
        '<td class="text-right"><span class="rango-cantidad text-muted">—</span></td>' +
        '<td class="text-center"><button type="button" class="btn btn-xs btn-danger btn-eliminar-rango" title="Quitar este rango"><i class="fas fa-times"></i></button></td>' +
    '</tr>');
    $('#rangos-body').append($row);
    actualizarNumeros();
});

$(document).on('click', '.btn-eliminar-rango', function () {
    $(this).closest('tr').remove();
    actualizarNumeros();
    recalcularTotal();
});

//renumera las filas al quitar un rango
function actualizarNumeros() {
    $('#rangos-body .rango-row').each(function (i) {
        $(this).find('.rango-num').text(i + 1);
    });
}
</script>
@endpush
