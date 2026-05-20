{{--vista de retencion compartida entre admin y empleado--}}
{{--el layout se inyecta desde RetencionController segun el rol del usuario autenticado--}}
@extends($layout)

@section('page_title', 'Retención')

@section('page_content')

    {{--select cargado con los anos disponibles de la tabla anios--}}
    <div class="card mx-auto" style="max-width:550px">
        <div class="card-body">

            <div class="form-group">
                <label>Año</label>
                <select class="form-control">
                    <option value="">-- Seleccionar año --</option>
                    @foreach($anios as $anio)
                        <option value="{{ $anio->id }}">{{ $anio->anio }}</option>
                    @endforeach
                </select>
            </div>

            {{--campo NIT/DUI: solo acepta 9 digitos, inserta guion automaticamente entre el 8 y 9--}}
            <div class="form-group">
                <label>NIT / DUI</label>
                <input type="text" id="nitdui" class="form-control"
                       placeholder="Ingrese NIT o DUI"
                       maxlength="10"
                       autocomplete="off">
                <small class="form-text text-muted">Formato: XXXXXXXX-X</small>
            </div>

            {{--boton buscar sin funcionalidad aun, se implementara en una etapa posterior--}}
            <button type="button" class="btn btn-primary">
                <i class="fas fa-search"></i> Buscar
            </button>

        </div>
    </div>

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
