{{--layout base para el portal de empleado, extiende adminlte::page--}}
@extends('adminlte::page')

@section('title', 'Renta municipal')

{{--CSS para centrar el logo del sidebar en estado normal y contraido--}}
@section('adminlte_css')
<link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
<style>
    .brand-link .brand-image { display: none !important; }
    .brand-link .brand-text { width: 100%; text-align: center; }
</style>
@stop

{{--titulo de la pagina centrado--}}
@section('content_header')
    <h1 class="text-center">@yield('page_title', 'Inicio')</h1>
@stop

{{--todo el contenido se centra horizontalmente dentro de la columna principal--}}
@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-lg-10 col-xl-9">
            @yield('page_content')
        </div>
    </div>
@stop

@section('footer')
    <strong>Sistema de Renta</strong> &mdash; Portal Empleado
@stop

@section('adminlte_js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        //intercepta el enlace de cerrar sesion y pide confirmacion con SweetAlert
        var logoutLink = document.querySelector('a[href$="logout"]');
        if (logoutLink) {
            logoutLink.addEventListener('click', function (e) {
                e.preventDefault();
                var href = this.href;
                Swal.fire({
                    title: '¿Cerrar sesión?',
                    text: '¿Está seguro que desea cerrar sesión?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, cerrar sesión',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#6c757d',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        window.location.href = href;
                    }
                });
            });
        }

        //manejador generico para botones de eliminacion marcados con data-swal-delete
        document.querySelectorAll('[data-swal-delete]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var formId = this.dataset.form;
                var msg    = this.dataset.msg || '¿Confirmar eliminación?';
                var icon   = this.dataset.icon || 'warning';
                Swal.fire({
                    title: msg,
                    icon: icon,
                    showCancelButton: true,
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                }).then(function (result) {
                    if (result.isConfirmed) {
                        document.getElementById(formId).submit();
                    }
                });
            });
        });
    });
</script>
@stack('js')
@stop
