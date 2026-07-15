@extends('frontend.layouts.admin')

@section('page_title', 'Libro de Especies')

@section('page_content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-book mr-2"></i>Libro de Especies
            </h3>
        </div>
        <div class="card-body">
            <p class="text-muted">
                Reporte oficial que <strong>consolida todos los movimientos de especies</strong>:
                compras al Ministerio de Hacienda, traslados a distritos, anulaciones y realizaciones,
                ordenados cronológicamente.
            </p>
            <p class="text-muted mb-0">
                Sirve como el registro maestro del ciclo de vida de cada especie municipal.
                Permitirá filtrar por tipo de especie, denominación y rango de fechas, y se podrá
                exportar en PDF.
            </p>
        </div>
    </div>
@stop
