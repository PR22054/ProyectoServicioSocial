{{--vista principal del CRUD de usuarios: lista, editar y eliminar--}}
{{--si el admin intenta eliminarse a si mismo se muestra un mensaje de confirmacion diferente--}}
@extends('frontend.layouts.admin')

@section('page_title', 'Usuarios')

@section('page_content')

    @if(session('success'))
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert">&times;</button>
            {{ session('success') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <a href="{{ route('admin.usuarios.create') }}" class="btn btn-success btn-sm">
                <i class="fas fa-plus"></i> Nuevo Usuario
            </a>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>#</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th style="width:110px">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="align-middle">{{ $user->id }}</td>
                        <td class="align-middle">{{ $user->usuario }}</td>
                        <td class="align-middle">
                            <span class="badge badge-{{ $user->rol === 'admin' ? 'danger' : 'info' }}">
                                {{ $user->rol }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.usuarios.edit', $user) }}"
                               class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('admin.usuarios.destroy', $user) }}"
                                  method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                {{--mensaje de confirmacion diferente si el usuario es el mismo que esta autenticado--}}
                                @if(auth()->id() === $user->id)
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Eliminar tu propio usuario? Esto cerrara tu sesion.')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('¿Eliminar al usuario {{ $user->usuario }}?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">Sin usuarios registrados.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@stop
