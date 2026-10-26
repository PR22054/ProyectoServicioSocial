@extends('frontend.layouts.admin')
@section('page_title', 'Configuración de Distritos')

@section('page_content')

{{--COMO LLEVA CADA DISTRITO SU LIBRO DE ESPECIES Y SUS REPORTES: ROTULOS Y TARIFAS--}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger py-2">
            @foreach(array_unique($errors->all()) as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    <div class="card">
        <div class="card-body py-2">
            {{--selector del distrito a configurar--}}
            <form method="GET" class="form-inline">
                <label class="mr-2">Distrito</label>
                <select name="distrito_id" class="form-control" onchange="this.form.submit()">
                    @foreach($distritos as $d)
                        <option value="{{ $d->id }}" {{ $d->id === $distrito->id ? 'selected' : '' }}>{{ $d->nombre }} ({{ $d->codigo }})</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{--datos del distrito--}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Encabezados de libros y reportes</h3></div>
        <div class="card-body">
            {{--datos del distrito para los encabezados--}}
            <form method="POST" action="{{ route('admin.especies.configuracion.distritos.update', $distrito) }}">
                @csrf @method('PATCH')
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group mb-md-0">
                            <label>Nombre en los reportes</label>
                            <input type="text" name="nombre_oficial" class="form-control" maxlength="150"
                                   value="{{ old('nombre_oficial', $distrito->nombre_oficial) }}" placeholder="{{ mb_strtoupper($distrito->nombre) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-md-0">
                            <label>Lugar de la firma del libro</label>
                            <input type="text" name="lugar_firma" class="form-control" maxlength="150"
                                   value="{{ old('lugar_firma', $distrito->lugar_firma) }}" placeholder="{{ $distrito->nombre_reporte }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="custom-control custom-switch" title="Si se apaga, el saldo lista los documentos en el orden en que entraron">
                            <input type="checkbox" class="custom-control-input" id="saldo_por_valor" name="saldo_por_valor" value="1"
                                   {{ $distrito->saldo_por_valor ? 'checked' : '' }}>
                            <label class="custom-control-label" for="saldo_por_valor">Saldo ordenado por valor</label>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary btn-block">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{--tipos: titulos y orden--}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tipos de especie del distrito</h3>
            <small class="text-muted ml-2">Título en el libro y en el control de saldos, y orden en el control de saldos</small>
        </div>
        <div class="card-body p-0">
            {{--formularios de cada fila de tipos (la tabla usa form= para enviarlos)--}}
            @foreach($config as $c)
                <form method="POST" action="{{ route('admin.especies.configuracion.distritos.tipos', $distrito) }}" id="f-tipo-{{ $c->id }}">
                    @csrf <input type="hidden" name="tipo_especie_id" value="{{ $c->tipo_especie_id }}">
                </form>
                <form method="POST" action="{{ route('admin.especies.configuracion.distritos.tipos.destroy', $c) }}" id="del-tipo-{{ $c->id }}">
                    @csrf @method('DELETE')
                </form>
            @endforeach
            <form method="POST" action="{{ route('admin.especies.configuracion.distritos.tipos', $distrito) }}" id="f-tipo-nuevo">@csrf</form>
            {{--tipos de especie del distrito--}}
            <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width:20%">Tipo</th>
                        <th>Título en el libro</th>
                        <th>Título en control de saldos</th>
                        <th>Título en realizaciones</th>
                        <th style="width:7%">Orden</th>
                        <th class="text-center" style="width:13%" title="El libro suma las nulas al total de realizaciones">Nulas en realizaciones</th>
                        <th class="text-center" style="width:9%"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($config as $c)
                    <tr>
                        <td class="align-middle">{{ $c->tipoEspecie->nombre }}</td>
                        <td><input type="text" name="titulo_libro" form="f-tipo-{{ $c->id }}" class="form-control form-control-sm" maxlength="150" value="{{ $c->titulo_libro }}"></td>
                        <td><input type="text" name="titulo_reporte" form="f-tipo-{{ $c->id }}" class="form-control form-control-sm" maxlength="150" value="{{ $c->titulo_reporte }}"></td>
                        <td><input type="text" name="titulo_realizaciones" form="f-tipo-{{ $c->id }}" class="form-control form-control-sm" maxlength="150" value="{{ $c->titulo_realizaciones }}" placeholder="el del control de saldos"></td>
                        <td><input type="number" name="orden" form="f-tipo-{{ $c->id }}" class="form-control form-control-sm" min="0" max="999" value="{{ $c->orden }}"></td>
                        <td class="text-center align-middle">
                            <input type="checkbox" name="nulas_en_realizaciones" value="1" form="f-tipo-{{ $c->id }}" {{ $c->nulas_en_realizaciones ? 'checked' : '' }}>
                        </td>
                        <td class="text-center text-nowrap">
                            <button type="submit" form="f-tipo-{{ $c->id }}" class="btn btn-xs btn-success" data-toggle="tooltip" title="Guardar"><i class="fas fa-save"></i></button>
                            <button type="button" class="btn btn-xs btn-danger" data-swal-delete data-toggle="tooltip" title="Quitar este tipo del distrito" data-form="del-tipo-{{ $c->id }}"
                                    data-msg="¿Quitar los rótulos de «{{ $c->tipoEspecie->nombre }}» en este distrito?"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                    <tr class="table-light">
                        <td>
                            <select name="tipo_especie_id" form="f-tipo-nuevo" class="form-control form-control-sm">
                                <option value="">— Agregar tipo —</option>
                                @foreach($tipos->whereNotIn('id', $config->pluck('tipo_especie_id')) as $t)
                                    <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="text" name="titulo_libro" form="f-tipo-nuevo" class="form-control form-control-sm" maxlength="150" placeholder="Ej. FONDO DE VIALIDAD"></td>
                        <td><input type="text" name="titulo_reporte" form="f-tipo-nuevo" class="form-control form-control-sm" maxlength="150"></td>
                        <td><input type="text" name="titulo_realizaciones" form="f-tipo-nuevo" class="form-control form-control-sm" maxlength="150" placeholder="el del control de saldos"></td>
                        <td><input type="number" name="orden" form="f-tipo-nuevo" class="form-control form-control-sm" min="0" max="999" value="{{ $config->max('orden') + 1 }}"></td>
                        <td class="text-center align-middle"><input type="checkbox" name="nulas_en_realizaciones" value="1" form="f-tipo-nuevo"></td>
                        <td class="text-center"><button type="submit" form="f-tipo-nuevo" class="btn btn-xs btn-primary">Agregar</button></td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>

    {{--tarifas: rotulo, costo y venta de cada denominacion--}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tarifas del distrito</h3>
            <small class="text-muted ml-2">Filas del control de saldos y del reporte de realizaciones; el costo y el precio de venta también calculan cobros y descargos. Precio de venta vacío = no se vende</small>
        </div>
        <div class="card-body p-0">
            {{--formularios de cada fila de tarifas--}}
            @foreach($tarifas as $t)
                <form method="POST" action="{{ route('admin.especies.configuracion.distritos.tarifas', $distrito) }}" id="f-tar-{{ $t->id }}" data-tarifa>
                    @csrf <input type="hidden" name="denominacion_id" value="{{ $t->denominacion_id }}">
                    <input type="hidden" name="aplicar_desde">
                </form>
                <form method="POST" action="{{ route('admin.especies.configuracion.distritos.tarifas.destroy', $t) }}" id="del-tar-{{ $t->id }}">
                    @csrf @method('DELETE')
                </form>
            @endforeach
            <form method="POST" action="{{ route('admin.especies.configuracion.distritos.tarifas', $distrito) }}" id="f-tar-nueva">@csrf</form>
            {{--tarifas del distrito--}}
            <div class="table-responsive">
            <table class="table table-sm mb-0" style="min-width:1500px">
                <thead>
                    <tr>
                        <th rowspan="2" class="align-middle" style="width:12%">Denominación</th>
                        <th rowspan="2" class="align-middle" style="width:6%">P. costo</th>
                        <th rowspan="2" class="align-middle" style="width:6%">P. venta</th>
                        <th colspan="5" class="text-center border-left">Control de saldos</th>
                        <th colspan="4" class="text-center border-left">Realizaciones</th>
                        <th rowspan="2" style="width:5%"></th>
                    </tr>
                    <tr>
                        <th class="border-left">Descripción</th>
                        <th style="width:4%">Orden</th>
                        <th title="Fila que el control de saldos muestra siempre en cero">Fila de nulas</th>
                        <th style="width:4%">Orden</th>
                        <th class="text-center" style="width:5%" title="La fila de nulas muestra 0.00 en precio de venta">Con venta</th>
                        <th class="border-left" title="Vacío: la del control de saldos">Descripción</th>
                        <th style="width:4%">Orden</th>
                        <th title="Vacío: las nulas se suman a la fila de la denominación">Fila de nulas</th>
                        <th style="width:4%">Orden</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tarifas as $t)
                    <tr>
                        <td class="align-middle small">{{ $t->denominacion->tipoEspecie->nombre }} — ${{ number_format($t->denominacion->valor, 2) }}</td>
                        <td><input type="number" name="precio_costo" form="f-tar-{{ $t->id }}" class="form-control form-control-sm" step="0.0001" min="0" value="{{ $t->precio_costo !== null ? (float) $t->precio_costo : '' }}"></td>
                        <td><input type="number" name="precio_venta" form="f-tar-{{ $t->id }}" class="form-control form-control-sm" step="0.0001" min="0" value="{{ $t->precio_venta !== null ? (float) $t->precio_venta : '' }}" data-original="{{ $t->precio_venta !== null ? (float) $t->precio_venta : '' }}" placeholder="no se vende"></td>
                        <td class="border-left"><input type="text" name="descripcion" form="f-tar-{{ $t->id }}" class="form-control form-control-sm" maxlength="150" value="{{ $t->descripcion }}"></td>
                        <td><input type="number" name="orden" form="f-tar-{{ $t->id }}" class="form-control form-control-sm" min="0" max="999" value="{{ $t->orden }}"></td>
                        <td><input type="text" name="descripcion_nulas" form="f-tar-{{ $t->id }}" class="form-control form-control-sm" maxlength="150" value="{{ $t->descripcion_nulas }}" placeholder="sin fila de nulas"></td>
                        <td><input type="number" name="orden_nulas" form="f-tar-{{ $t->id }}" class="form-control form-control-sm" min="0" max="999" value="{{ $t->orden_nulas }}"></td>
                        <td class="text-center align-middle"><input type="checkbox" name="nulas_con_venta" value="1" form="f-tar-{{ $t->id }}" {{ $t->nulas_con_venta ? 'checked' : '' }}></td>
                        <td class="border-left"><input type="text" name="descripcion_realizaciones" form="f-tar-{{ $t->id }}" class="form-control form-control-sm" maxlength="150" value="{{ $t->descripcion_realizaciones }}"></td>
                        <td><input type="number" name="orden_realizaciones" form="f-tar-{{ $t->id }}" class="form-control form-control-sm" min="0" max="999" value="{{ $t->orden_realizaciones }}"></td>
                        <td><input type="text" name="nulas_realizaciones" form="f-tar-{{ $t->id }}" class="form-control form-control-sm" maxlength="150" value="{{ $t->nulas_realizaciones }}" placeholder="sumadas a la fila"></td>
                        <td><input type="number" name="orden_nulas_realizaciones" form="f-tar-{{ $t->id }}" class="form-control form-control-sm" min="0" max="999" value="{{ $t->orden_nulas_realizaciones }}"></td>
                        <td class="text-center text-nowrap">
                            <button type="submit" form="f-tar-{{ $t->id }}" class="btn btn-xs btn-success" data-toggle="tooltip" title="Guardar"><i class="fas fa-save"></i></button>
                            <button type="button" class="btn btn-xs btn-danger" data-swal-delete data-toggle="tooltip" title="Quitar esta tarifa del distrito" data-form="del-tar-{{ $t->id }}"
                                    data-msg="¿Quitar la tarifa de {{ $t->denominacion->tipoEspecie->nombre }} ${{ number_format($t->denominacion->valor, 2) }}?"><i class="fas fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                    <tr class="table-light">
                        <td>
                            <select name="denominacion_id" form="f-tar-nueva" class="form-control form-control-sm">
                                <option value="">— Agregar denominación —</option>
                                @foreach($denominaciones->whereNotIn('id', $tarifas->pluck('denominacion_id')) as $d)
                                    <option value="{{ $d->id }}">{{ $d->tipoEspecie->nombre }} — ${{ number_format($d->valor, 2) }}</option>
                                @endforeach
                            </select>
                        </td>
                        <td><input type="number" name="precio_costo" form="f-tar-nueva" class="form-control form-control-sm" step="0.0001" min="0"></td>
                        <td><input type="number" name="precio_venta" form="f-tar-nueva" class="form-control form-control-sm" step="0.0001" min="0" placeholder="no se vende"></td>
                        <td class="border-left"><input type="text" name="descripcion" form="f-tar-nueva" class="form-control form-control-sm" maxlength="150" placeholder="Ej. DE $0.15"></td>
                        <td><input type="number" name="orden" form="f-tar-nueva" class="form-control form-control-sm" min="0" max="999"></td>
                        <td><input type="text" name="descripcion_nulas" form="f-tar-nueva" class="form-control form-control-sm" maxlength="150" placeholder="Ej. NULAS"></td>
                        <td><input type="number" name="orden_nulas" form="f-tar-nueva" class="form-control form-control-sm" min="0" max="999"></td>
                        <td class="text-center align-middle"><input type="checkbox" name="nulas_con_venta" value="1" form="f-tar-nueva"></td>
                        <td class="border-left"><input type="text" name="descripcion_realizaciones" form="f-tar-nueva" class="form-control form-control-sm" maxlength="150"></td>
                        <td><input type="number" name="orden_realizaciones" form="f-tar-nueva" class="form-control form-control-sm" min="0" max="999"></td>
                        <td><input type="text" name="nulas_realizaciones" form="f-tar-nueva" class="form-control form-control-sm" maxlength="150" placeholder="Ej. NULAS"></td>
                        <td><input type="number" name="orden_nulas_realizaciones" form="f-tar-nueva" class="form-control form-control-sm" min="0" max="999"></td>
                        <td class="text-center"><button type="submit" form="f-tar-nueva" class="btn btn-xs btn-primary">Agregar</button></td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>

@stop

@push('js')
{{--aviso al cambiar el precio de venta de una tarifa--}}
<script>
//si cambia el precio de venta de una tarifa, pregunta desde que fecha recalcular el monto cobrado de sus realizaciones
document.querySelectorAll('form[data-tarifa]').forEach(function (form) {
    form.addEventListener('submit', function (e) {
        var precio = document.querySelector('input[name="precio_venta"][form="' + form.id + '"]');
        if (!precio || precio.value === precio.dataset.original) return;
        e.preventDefault();
        var hoy = new Date();
        var inicioMes = hoy.getFullYear() + '-' + String(hoy.getMonth() + 1).padStart(2, '0') + '-01';
        Swal.fire({
            title: 'Cambió el precio de venta',
            html: 'De <b>' + (precio.dataset.original === '' ? 'no se vende' : '$' + precio.dataset.original) + '</b> a <b>'
                + (precio.value === '' ? 'no se vende' : '$' + precio.value) + '</b>.<br>'
                + '¿Desde qué fecha recalculo el monto cobrado de las realizaciones de esta denominación en el distrito?'
                + '<br><small class="text-muted">Las realizaciones anteriores a esa fecha no cambian.</small>',
            input: 'date',
            inputValue: inicioMes,
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: 'Guardar y recalcular',
            denyButtonText: 'Solo guardar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#28a745',
            denyButtonColor: '#3085d6',
            cancelButtonColor: '#6c757d',
            inputValidator: function (valor) { if (!valor) return 'Indique la fecha.'; },
        }).then(function (r) {
            if (r.isDismissed) return;
            form.querySelector('input[name="aplicar_desde"]').value = r.isConfirmed ? r.value : '';
            form.submit();
        });
    });
});
</script>
@endpush
