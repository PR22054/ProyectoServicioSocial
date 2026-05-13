{{--dashboard de bienvenida para el rol admin--}}
@extends('frontend.layouts.admin')

@section('page_title', 'Dashboard Admin')

@section('page_content')
    <div class="card">
        <div class="card-body">
            Bienvenido, <strong>{{ auth()->user()->usuario }}</strong>.
        </div>
    </div>
@stop
