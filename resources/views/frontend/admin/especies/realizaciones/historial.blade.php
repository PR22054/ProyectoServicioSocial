@extends('frontend.layouts.admin')
@section('page_title', 'Historial de Realizaciones')

@section('page_content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    {{-- filtros y botón --}}
    <div class="d-flex justify-content-between align-items-start mb-3 flex-wrap gap-2">
        <form method="GET" action="{{ route('admin.especies.realizaciones.historial') }}" class="form-inline flex-wrap">
            <select name="distrito_id" class="form-control form-control-sm mr-2 mb-1">
                <option value="">— Todos los distritos —</option>
                @foreach($distritos as $d)
                    <option value="{{ $d->id }}" {{ $distFiltro == $d->id ? 'selected' : '' }}>{{ $d->nombre }}</option>
                @endforeach
            </select>
            <select name="tipo_especie_id" class="form-control form-control-sm mr-2 mb-1">
                <option value="">— Todos los tipos —</option>
                @foreach($tipos as $t)
                    <option value="{{ $t->id }}" {{ $tipoFiltro == $t->id ? 'selected' : '' }}>{{ $t->nombre }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-sm btn-primary mr-2 mb-1">Filtrar</button>
            @if($distFiltro || $tipoFiltro)
                <a href="{{ route('admin.especies.realizaciones.historial') }}" class="btn btn-sm btn-secondary mb-1">Limpiar</a>
            @endif
        </form>
        <a href="{{ route('admin.especies.realizaciones.crear') }}" class="btn btn-primary">
            Registrar realización
        </a>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">Realizaciones ({{ $realizaciones->count() }})</h3>
            @if($realizaciones->count() > 0)
                <span class="badge badge-success" style="font-size:.9rem">
                    Total cobrado: ${{ number_format($realizaciones->sum('monto_cobrado'), 2) }}
                </span>
            @endif
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width:4%">#</th>
                        <th>Fecha</th>
                        <th>Distrito</th>
                        <th>Tipo de especie</th>
                        <th>Denominación</th>
                        <th class="text-center">Rango realizado</th>
                        <th class="text-right">Cantidad</th>
                        <th class="text-right">Monto cobrado</th>
                        <th>Contribuyente</th>
                        <th>Registrado por</th>
                        <th class="text-center" style="width:7%">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($realizaciones as $r)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $r->fecha->format('d/m/Y') }}</td>
                        <td>{{ $r->distrito->nombre ?? '—' }}</td>
                        <td>{{ $r->tipoEspecie->nombre ?? '—' }}</td>
                        <td>${{ number_format($r->denominacion->valor ?? 0, 2) }}</td>
                        <td class="text-center">
                            <span class="badge badge-primary">
                                {{ number_format($r->numero_inicio) }} – {{ number_format($r->numero_fin) }}
                            </span>
                        </td>
                        <td class="text-right">{{ number_format($r->cantidad) }}</td>
                        <td class="text-right font-weight-bold text-success">
                            ${{ number_format($r->monto_cobrado, 2) }}
                        </td>
                        <td>{{ $r->nombre_contribuyente ?? '—' }}</td>
                        <td>{{ $r->usuario->usuario ?? '—' }}</td>
                        <td class="text-center">
                            <form method="POST"
                                  action="{{ route('admin.especies.realizaciones.destroy', $r) }}"
                                  id="del-real-{{ $r->id }}" style="display:inline">
                                @csrf @method('DELETE')
                            </form>
                            <button type="button" class="btn btn-xs btn-danger"
                                    data-swal-delete
                                    data-form="del-real-{{ $r->id }}"
                                    data-msg="¿Eliminar la realización {{ number_format($r->numero_inicio) }}–{{ number_format($r->numero_fin) }} ({{ $r->tipoEspecie->nombre ?? '' }})?">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-3">Sin realizaciones registradas</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@stop
