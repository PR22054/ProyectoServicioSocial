{{--layout base para el portal de empleado, extiende adminlte::page--}}
@extends('adminlte::page')

@section('title', 'Portal Empleado')

@section('content_header')
    <h1>@yield('page_title', 'Inicio')</h1>
@stop

@section('content')
    @yield('page_content')
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
@stop
