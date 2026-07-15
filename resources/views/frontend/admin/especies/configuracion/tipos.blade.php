@extends('frontend.layouts.admin')

@section('page_title', 'Tipos de Especie')

@section('page_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-tags mr-2"></i>Tipos de Especie
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Catálogo de los tipos de especie municipal que maneja la Alcaldía. Aquí se podrá
                <strong>listar, agregar y activar/desactivar</strong> tipos como Fondo Vialidad,
                Tiquetes de Mercado, Cartas de Venta Continua, entre otros.
            </p>
            <p class="text-muted mb-0">
                Los tipos inactivos no estarán disponibles para nuevas compras ni realizaciones.
            </p>
        </div>
    </div>
@stop
