@extends('frontend.layouts.admin')
@section('page_title', 'Registrar Traslado')

@section('page_content')

    <div class="card">
        <div class="card-header"><h3 class="card-title">Nuevo traslado</h3></div>
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

            <form method="POST" action="{{ route('admin.especies.bodega.traslado.store') }}">
                @csrf

                <div class="form-group">
                    <label>Distrito destino <span class="text-danger">*</span></label>
                    <select name="distrito_id" class="form-control @error('distrito_id') is-invalid @enderror" required>
                        <option value="">— Seleccione —</option>
                        @foreach($distritos as $d)
                            <option value="{{ $d->id }}" {{ old('distrito_id') == $d->id ? 'selected' : '' }}>
                                {{ $d->nombre }}{{ $d->codigo ? ' (' . $d->codigo . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('distrito_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Fecha <span class="text-danger">*</span></label>
                    <input type="date" name="fecha"
                           class="form-control @error('fecha') is-invalid @enderror"
                           value="{{ old('fecha', date('Y-m-d')) }}" required>
                    @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label>Observaciones <small class="text-muted">(opcional)</small></label>
                    <textarea name="observaciones" class="form-control" rows="2" maxlength="500">{{ old('observaciones') }}</textarea>
                </div>

                <div class="d-flex justify-content-between">
                    <a href="{{ route('admin.especies.bodega.traslado.historial') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Guardar — agregar detalles
                    </button>
                </div>
            </form>
        </div>
    </div>

@stop
