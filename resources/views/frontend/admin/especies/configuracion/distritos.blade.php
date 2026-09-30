@extends('frontend.layouts.admin')
@section('page_title', 'Configuración de Distritos')

@section('page_content')

{{-- COMO LLEVA CADA DISTRITO SU LIBRO DE ESPECIES Y SU REPORTE MENSUAL: ROTULOS, TARIFAS Y CAJAS --}}
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

    {{-- datos del distrito --}}
    <div class="card">
        <div class="card-header"><h3 class="card-title">Encabezados de libros y reportes</h3></div>
        <div class="card-body">
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

    {{-- tipos: titulos y orden --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tipos de especie del distrito</h3>
            <small class="text-muted ml-2">Título en el libro y en el reporte de especies realizadas, y orden en el reporte</small>
        </div>
        <div class="card-body p-0">
            @foreach($config as $c)
                <form method="POST" action="{{ route('admin.especies.configuracion.distritos.tipos', $distrito) }}" id="f-tipo-{{ $c->id }}">
                    @csrf <input type="hidden" name="tipo_especie_id" value="{{ $c->tipo_especie_id }}">
                </form>
                <form method="POST" action="{{ route('admin.especies.configuracion.distritos.tipos.destroy', $c) }}" id="del-tipo-{{ $c->id }}">
                    @csrf @method('DELETE')
                </form>
            @endforeach
            <form method="POST" action="{{ route('admin.especies.configuracion.distritos.tipos', $distrito) }}" id="f-tipo-nuevo">@csrf</form>
            <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width:20%">Tipo</th>
                        <th>Título en el libro</th>
                        <th>Título en el reporte</th>
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
                        <td><input type="number" name="orden" form="f-tipo-{{ $c->id }}" class="form-control form-control-sm" min="0" max="999" value="{{ $c->orden }}"></td>
                        <td class="text-center align-middle">
                            <input type="checkbox" name="nulas_en_realizaciones" value="1" form="f-tipo-{{ $c->id }}" {{ $c->nulas_en_realizaciones ? 'checked' : '' }}>
                        </td>
                        <td class="text-center text-nowrap">
                            <button type="submit" form="f-tipo-{{ $c->id }}" class="btn btn-xs btn-success" title="Guardar"><i class="fas fa-save"></i></button>
                            <button type="button" class="btn btn-xs btn-danger" data-swal-delete data-form="del-tipo-{{ $c->id }}"
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
                        <td><input type="number" name="orden" form="f-tipo-nuevo" class="form-control form-control-sm" min="0" max="999" value="{{ $config->max('orden') + 1 }}"></td>
                        <td class="text-center align-middle"><input type="checkbox" name="nulas_en_realizaciones" value="1" form="f-tipo-nuevo"></td>
                        <td class="text-center"><button type="submit" form="f-tipo-nuevo" class="btn btn-xs btn-primary">Agregar</button></td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>

    {{-- tarifas: rotulo, costo y venta de cada denominacion --}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tarifas del distrito</h3>
            <small class="text-muted ml-2">Filas del reporte de especies realizadas. Precio de venta vacío = no se vende</small>
        </div>
        <div class="card-body p-0">
            @foreach($tarifas as $t)
                <form method="POST" action="{{ route('admin.especies.configuracion.distritos.tarifas', $distrito) }}" id="f-tar-{{ $t->id }}">
                    @csrf <input type="hidden" name="denominacion_id" value="{{ $t->denominacion_id }}">
                </form>
                <form method="POST" action="{{ route('admin.especies.configuracion.distritos.tarifas.destroy', $t) }}" id="del-tar-{{ $t->id }}">
                    @csrf @method('DELETE')
                </form>
            @endforeach
            <form method="POST" action="{{ route('admin.especies.configuracion.distritos.tarifas', $distrito) }}" id="f-tar-nueva">@csrf</form>
            <div class="table-responsive">
            <table class="table table-sm mb-0" style="min-width:1100px">
                <thead>
                    <tr>
                        <th style="width:17%">Denominación</th>
                        <th>Descripción</th>
                        <th style="width:9%">P. costo</th>
                        <th style="width:9%">P. venta</th>
                        <th style="width:6%">Orden</th>
                        <th>Fila de nulas</th>
                        <th style="width:6%">Orden</th>
                        <th class="text-center" style="width:7%" title="La fila de nulas muestra 0.00 en precio de venta">Nulas con venta</th>
                        <th class="text-center" style="width:7%"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tarifas as $t)
                    <tr>
                        <td class="align-middle small">{{ $t->denominacion->tipoEspecie->nombre }} — ${{ number_format($t->denominacion->valor, 2) }}</td>
                        <td><input type="text" name="descripcion" form="f-tar-{{ $t->id }}" class="form-control form-control-sm" maxlength="150" value="{{ $t->descripcion }}"></td>
                        <td><input type="number" name="precio_costo" form="f-tar-{{ $t->id }}" class="form-control form-control-sm" step="0.0001" min="0" value="{{ $t->precio_costo !== null ? (float) $t->precio_costo : '' }}"></td>
                        <td><input type="number" name="precio_venta" form="f-tar-{{ $t->id }}" class="form-control form-control-sm" step="0.0001" min="0" value="{{ $t->precio_venta !== null ? (float) $t->precio_venta : '' }}" placeholder="no se vende"></td>
                        <td><input type="number" name="orden" form="f-tar-{{ $t->id }}" class="form-control form-control-sm" min="0" max="999" value="{{ $t->orden }}"></td>
                        <td><input type="text" name="descripcion_nulas" form="f-tar-{{ $t->id }}" class="form-control form-control-sm" maxlength="150" value="{{ $t->descripcion_nulas }}" placeholder="sin fila: se suman a lo realizado"></td>
                        <td><input type="number" name="orden_nulas" form="f-tar-{{ $t->id }}" class="form-control form-control-sm" min="0" max="999" value="{{ $t->orden_nulas }}"></td>
                        <td class="text-center align-middle"><input type="checkbox" name="nulas_con_venta" value="1" form="f-tar-{{ $t->id }}" {{ $t->nulas_con_venta ? 'checked' : '' }}></td>
                        <td class="text-center text-nowrap">
                            <button type="submit" form="f-tar-{{ $t->id }}" class="btn btn-xs btn-success" title="Guardar"><i class="fas fa-save"></i></button>
                            <button type="button" class="btn btn-xs btn-danger" data-swal-delete data-form="del-tar-{{ $t->id }}"
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
                        <td><input type="text" name="descripcion" form="f-tar-nueva" class="form-control form-control-sm" maxlength="150" placeholder="Ej. DE $0.15"></td>
                        <td><input type="number" name="precio_costo" form="f-tar-nueva" class="form-control form-control-sm" step="0.0001" min="0"></td>
                        <td><input type="number" name="precio_venta" form="f-tar-nueva" class="form-control form-control-sm" step="0.0001" min="0" placeholder="no se vende"></td>
                        <td><input type="number" name="orden" form="f-tar-nueva" class="form-control form-control-sm" min="0" max="999"></td>
                        <td><input type="text" name="descripcion_nulas" form="f-tar-nueva" class="form-control form-control-sm" maxlength="150" placeholder="Ej. NULAS"></td>
                        <td><input type="number" name="orden_nulas" form="f-tar-nueva" class="form-control form-control-sm" min="0" max="999"></td>
                        <td class="text-center align-middle"><input type="checkbox" name="nulas_con_venta" value="1" form="f-tar-nueva"></td>
                        <td class="text-center"><button type="submit" form="f-tar-nueva" class="btn btn-xs btn-primary">Agregar</button></td>
                    </tr>
                </tbody>
            </table>
            </div>
        </div>
    </div>

    {{-- cajas y asignaciones de rangos --}}
    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Cajas del distrito</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead><tr><th>Caja</th><th class="text-center">Orden</th><th class="text-center">Rangos</th><th></th></tr></thead>
                        <tbody>
                            @forelse($cajas as $caja)
                            <tr>
                                <td>{{ $caja->nombre }}</td>
                                <td class="text-center">{{ $caja->orden }}</td>
                                <td class="text-center">{{ $caja->rangos_count }}</td>
                                <td class="text-right">
                                    <form method="POST" action="{{ route('admin.especies.configuracion.distritos.cajas.destroy', $caja) }}" id="del-caja-{{ $caja->id }}">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn btn-xs btn-danger" data-swal-delete data-form="del-caja-{{ $caja->id }}"
                                            data-msg="¿Eliminar la caja «{{ $caja->nombre }}»?"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-2">Sin cajas: el libro no rotula rangos</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <form method="POST" action="{{ route('admin.especies.configuracion.distritos.cajas', $distrito) }}" class="form-row">
                        @csrf
                        <div class="col-6"><input type="text" name="nombre" class="form-control form-control-sm" maxlength="60" placeholder="Ej. CAJA 1" required></div>
                        <div class="col-3"><input type="number" name="orden" class="form-control form-control-sm" min="0" max="999" value="{{ $cajas->max('orden') + 1 }}"></div>
                        <div class="col-3"><button type="submit" class="btn btn-sm btn-primary btn-block">Agregar</button></div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Rangos asignados a cajas</h3>
                    <small class="text-muted ml-2">Desde la fecha, el libro rotula esos números con la caja; no mueve existencias</small>
                </div>
                @if($cajas->isNotEmpty())
                <div class="card-body pb-0">
                    <form method="POST" action="{{ route('admin.especies.configuracion.distritos.asignaciones', $distrito) }}" class="form-row">
                        @csrf
                        <div class="col-md-2 mb-2">
                            <select name="ubicacion_id" class="form-control form-control-sm" required>
                                @foreach($cajas as $caja)<option value="{{ $caja->id }}" {{ old('ubicacion_id') == $caja->id ? 'selected' : '' }}>{{ $caja->nombre }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-3 mb-2">
                            <select name="tipo_especie_id" class="form-control form-control-sm" required>
                                @foreach($tipos as $t)<option value="{{ $t->id }}" {{ old('tipo_especie_id') == $t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>@endforeach
                            </select>
                        </div>
                        <div class="col-md-1 mb-2"><input type="text" name="serie" class="form-control form-control-sm" maxlength="10" placeholder="Serie" value="{{ old('serie') }}"></div>
                        <div class="col-md-2 mb-2"><input type="number" name="numero_inicio" class="form-control form-control-sm" min="1" placeholder="Del" value="{{ old('numero_inicio') }}" required></div>
                        <div class="col-md-2 mb-2"><input type="number" name="numero_fin" class="form-control form-control-sm" min="1" placeholder="Al" value="{{ old('numero_fin') }}" required></div>
                        <div class="col-md-2 mb-2"><input type="date" name="fecha" class="form-control form-control-sm" max="{{ date('Y-m-d') }}" value="{{ old('fecha', date('Y-m-d')) }}" required></div>
                        <div class="col-12 mb-2 text-right"><button type="submit" class="btn btn-sm btn-primary">Asignar</button></div>
                    </form>
                </div>
                @endif
                <div class="card-body p-0">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr><th>Fecha</th><th>Caja</th><th>Tipo</th><th class="text-center">Serie</th><th class="text-right">Del</th><th class="text-right">Al</th><th>Usuario</th><th></th></tr>
                        </thead>
                        <tbody>
                            @forelse($asignaciones as $a)
                            <tr>
                                <td>{{ $a->fecha->format('d/m/Y') }}</td>
                                <td>{{ $a->ubicacion->nombre }}</td>
                                <td>{{ $a->tipoEspecie->nombre }}</td>
                                <td class="text-center">{{ $a->serie ?? '—' }}</td>
                                <td class="text-right">{{ number_format($a->numero_inicio) }}</td>
                                <td class="text-right">{{ number_format($a->numero_fin) }}</td>
                                <td>{{ $a->usuario->usuario ?? '—' }}</td>
                                <td class="text-right">
                                    <form method="POST" action="{{ route('admin.especies.configuracion.distritos.asignaciones.destroy', $a) }}" id="del-asig-{{ $a->id }}">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" class="btn btn-xs btn-danger" data-swal-delete data-form="del-asig-{{ $a->id }}"
                                            data-msg="¿Eliminar la asignación a {{ $a->ubicacion->nombre }}?"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center text-muted py-2">Sin asignaciones</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($asignaciones->hasPages())
                    <div class="card-footer">{{ $asignaciones->links() }}</div>
                @endif
            </div>
        </div>
    </div>

@stop
