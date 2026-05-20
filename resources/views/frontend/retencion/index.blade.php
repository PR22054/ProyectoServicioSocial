{{--vista de retencion compartida entre admin y empleado--}}
{{--el layout se inyecta desde RetencionController segun el rol del usuario autenticado--}}
@extends($layout)

@section('page_title', 'Retención')

@section('page_content')

    @if(session('buscar_error'))
        <div class="alert alert-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('buscar_error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ $errors->first() }}
        </div>
    @endif

    <div class="card mx-auto" style="max-width:550px">
        <div class="card-body">

            <form action="{{ route('retencion.buscar') }}" method="POST">
                @csrf

                {{--select cargado con los anos disponibles de la tabla anios--}}
                <div class="form-group">
                    <label>Año</label>
                    <select name="anio_id" class="form-control">
                        <option value="">-- Seleccionar año --</option>
                        @foreach($anios as $anio)
                            <option value="{{ $anio->id }}"
                                {{ old('anio_id', $anioSel) == $anio->id ? 'selected' : '' }}>
                                {{ $anio->anio }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{--campo NIT/DUI: solo acepta 9 digitos, inserta guion automaticamente entre el 8 y 9--}}
                <div class="form-group">
                    <label>NIT / DUI</label>
                    <input type="text" id="nitdui" name="nitdui" class="form-control"
                           placeholder="Ingrese NIT o DUI"
                           maxlength="10"
                           value="{{ old('nitdui', $nitSel) }}"
                           autocomplete="off">
                    <small class="form-text text-muted">Formato: XXXXXXXX-X</small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Buscar
                </button>
            </form>

        </div>
    </div>

    {{--seccion de resultados visible unicamente despues de una busqueda exitosa--}}
    @if($token)
        <div class="card mt-4">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-file-pdf text-danger mr-2"></i>
                <strong>
                    @if($count == 1)
                        1 constancia encontrada
                    @else
                        {{ $count }} constancias encontradas
                    @endif
                </strong>
            </div>
            <div class="card-body p-0">
                <iframe src="{{ route('retencion.pdf.ver', $token) }}"
                        width="100%"
                        style="height:720px; border:none; display:block;">
                </iframe>
            </div>
        </div>
    @endif

@stop

@push('js')
<script>
    //validacion del campo NIT/DUI: solo numeros, maximo 9 digitos, guion auto entre digito 8 y 9
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.getElementById('nitdui');
        if (!input) return;

        input.addEventListener('input', function () {
            //extrae solo los digitos del valor actual
            var digits = this.value.replace(/\D/g, '');
            if (digits.length > 9) digits = digits.substring(0, 9);
            //inserta el guion cuando ya hay 9 digitos completos
            if (digits.length >= 9) {
                this.value = digits.substring(0, 8) + '-' + digits.substring(8);
            } else {
                this.value = digits;
            }
        });

        input.addEventListener('keydown', function (e) {
            //permite teclas de navegacion y edicion: backspace, delete, tab, flechas, inicio, fin
            var navegacion = [8, 9, 46, 35, 36, 37, 39];
            if (navegacion.indexOf(e.keyCode) !== -1) return;
            //bloquea cualquier tecla que no sea un digito del 0 al 9
            if (!/^\d$/.test(e.key)) e.preventDefault();
        });
    });
</script>
@endpush
