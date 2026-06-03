{{--vista del historial de busquedas de NIT/DUI realizadas en retencion--}}
@extends('frontend.layouts.admin')

@section('page_title', 'Consultas')

@section('page_content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Historial de búsquedas NIT/DUI</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered table-striped table-hover mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>NIT / DUI</th>
                        <th>Nombre</th>
                        <th>Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($consultas as $c)
                        <tr>
                            <td>{{ $consultas->firstItem() + $loop->index }}</td>
                            <td>{{ $c->nitdui }}</td>
                            <td>{{ $c->nombre ?? '—' }}</td>
                            <td>{{ $c->buscado_en->format('d/m/Y H:i:s') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No hay consultas registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($consultas->hasPages())
            <div class="card-footer">
                {{ $consultas->links() }}
            </div>
        @endif
    </div>

@stop
