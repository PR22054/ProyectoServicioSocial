{{--vista de retencion compartida entre admin y empleado--}}
{{--el layout se inyecta desde RetencionController segun el rol del usuario autenticado--}}
@extends($layout)

@section('page_title', 'Retención')

@section('page_content')

    <div class="card" style="max-width:550px">
        <div class="card-body">

            {{--select cargado con los anos disponibles de la tabla anios--}}
            <div class="form-group">
                <label>Año</label>
                <select class="form-control">
                    <option value="">-- Seleccionar año --</option>
                    @foreach($anios as $anio)
                        <option value="{{ $anio->id }}">{{ $anio->anio }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>NIT / DUI</label>
                <input type="text" class="form-control" placeholder="Ingrese NIT o DUI">
            </div>

            {{--boton buscar sin funcionalidad aun, se implementara en una etapa posterior--}}
            <button type="button" class="btn btn-primary">
                <i class="fas fa-search"></i> Buscar
            </button>

        </div>
    </div>

@stop
