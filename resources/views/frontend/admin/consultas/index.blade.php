{{--vista del reporte de consultas: formulario de periodo y visualizacion del PDF generado--}}
@extends('frontend.layouts.admin')

@section('page_title', 'Reporte de consultas')

@section('page_content')

    {{--formulario de seleccion de periodo--}}
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Seleccionar periodo</h3>
        </div>
        <div class="card-body">

            @if($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if(session('buscar_error'))
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    {{ session('buscar_error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.consultas.generar') }}">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="fecha_inicio">Fecha de inicio</label>
                            <input type="date" id="fecha_inicio" name="fecha_inicio"
                                   class="form-control @error('fecha_inicio') is-invalid @enderror"
                                   value="{{ old('fecha_inicio', $fechaInicio) }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="fecha_fin">Fecha de finalización</label>
                            <input type="date" id="fecha_fin" name="fecha_fin"
                                   class="form-control @error('fecha_fin') is-invalid @enderror"
                                   value="{{ old('fecha_fin', $fechaFin) }}">
                        </div>
                    </div>
                    <div class="col-md-4 mt-3 mt-md-0">
                        <button type="submit" class="btn btn-primary">Generar Reporte</button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    {{--iframe con el PDF generado, visible solo cuando hay token en sesion--}}
    @if($token)
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-file-pdf mr-1 text-danger"></i>
                    Reporte: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }}
                    &mdash;
                    {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}
                </h3>
            </div>
            <div class="card-body p-0">
                <iframe src="{{ route('admin.consultas.pdf.ver', $token) }}"
                        style="width:100%; height:680px; border:none;">
                </iframe>
            </div>
        </div>
    @endif

@stop
