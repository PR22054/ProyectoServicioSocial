@extends('adminlte::page')

@section('title', 'Panel Administrativo')

@section('content_header')
    <h1>@yield('page_title', 'Dashboard')</h1>
@stop

@section('content')
    @yield('page_content')
@stop

@section('footer')
    <strong>Sistema de Renta</strong> &mdash; Panel Admin
@stop
