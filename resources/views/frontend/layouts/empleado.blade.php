{{--layout base para el portal de empleado, extiende adminlte::page--}}
@extends('adminlte::page')

@section('title', 'Portal Empleado')

{{--CSS para centrar el logo del sidebar en estado normal y contraido--}}
@section('adminlte_css')
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
    //intercepta el enlace de cerrar sesion del menu y pide confirmacion antes de proceder
    document.addEventListener('DOMContentLoaded', function () {
        var logoutLink = document.querySelector('a[href$="logout"]');
        if (logoutLink) {
            logoutLink.addEventListener('click', function (e) {
                if (!confirm('¿Esta seguro que desea cerrar sesion?')) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@stack('js')
@stop
