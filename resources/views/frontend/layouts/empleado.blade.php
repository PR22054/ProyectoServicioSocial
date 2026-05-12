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
