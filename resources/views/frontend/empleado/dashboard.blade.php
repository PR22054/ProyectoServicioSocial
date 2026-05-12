@extends('frontend.layouts.empleado')

@section('page_title', 'Inicio')

@section('page_content')
    <div class="card">
        <div class="card-body">
            Bienvenido, <strong>{{ auth()->user()->usuario }}</strong>.
        </div>
    </div>
@stop
