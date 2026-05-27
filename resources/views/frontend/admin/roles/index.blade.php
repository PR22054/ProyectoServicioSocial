{{--vista para cambiar el rol de cada usuario, solo accesible para admin--}}
{{--si el usuario cambia su propio rol, el sistema le pedira confirmacion antes de guardar--}}
@extends('frontend.layouts.admin')

@section('page_title', 'Roles')

@section('page_content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>Usuario</th>
                        <th>Rol Actual</th>
                        <th>Cambiar Rol</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="align-middle">{{ $user->usuario }}</td>
                        <td class="align-middle">
                            <span class="badge badge-{{ $user->rol === 'admin' ? 'danger' : 'info' }}">
                                {{ $user->rol }}
                            </span>
                        </td>
                        <td class="align-middle">
                            {{--si la fila corresponde al usuario autenticado se agrega confirmacion de cierre de sesion--}}
                            <form action="{{ route('admin.roles.update', $user) }}" method="POST" class="form-inline"
                                @if($user->id === auth()->id()) data-swal-rol @endif>
                                @csrf
                                @method('PATCH')
                                <select name="rol" class="form-control form-control-sm mr-2">
                                    <option value="admin"    {{ $user->rol === 'admin'    ? 'selected' : '' }}>admin</option>
                                    <option value="empleado" {{ $user->rol === 'empleado' ? 'selected' : '' }}>empleado</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Guardar</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@stop

@push('js')
<script>
    //intercepta el submit del formulario de cambio de rol propio y pide confirmacion con SweetAlert
    document.addEventListener('DOMContentLoaded', function () {
        var selfForm = document.querySelector('form[data-swal-rol]');
        if (selfForm) {
            selfForm.addEventListener('submit', function (e) {
                e.preventDefault();
                var form = this;
                Swal.fire({
                    title: '¿Cambiar tu propio rol?',
                    text: 'Se cerrará tu sesión al cambiar el rol. ¿Continuar?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, continuar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#f0a500',
                    cancelButtonColor: '#6c757d',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        }
    });
</script>
@endpush
