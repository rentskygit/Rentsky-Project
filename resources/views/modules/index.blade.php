@extends('layouts.app')

@section('title', 'Gestión de Módulos')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Gestión de <span>Módulos</span></h1>
        <a href="{{ route('modules.create') }}" class="btn-create">
            <i class="bi bi-plus-lg"></i>
            Nuevo módulo
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-inner">
            <i class="bi bi-grid-3x3-gap-fill text-muted" style="font-size:.95rem"></i>
            <span style="font-size:.875rem; font-weight:600;">Módulos del sistema</span>
            <span class="badge-count">{{ $modules->count() }}</span>
        </div>

        @if($modules->isEmpty())
            <div class="empty-state">
                <i class="bi bi-grid"></i>
                <p class="mb-1" style="font-weight:600; color:var(--brand-dark);">Sin módulos</p>
                <p style="font-size:.875rem">Crea el primer módulo para comenzar.</p>
            </div>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Orden</th>
                        <th>Módulo</th>
                        <th>Icono</th>
                        <th>Ruta</th>
                        <th>Tipo</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($modules as $module)
                    <tr>
                        <td>{{ $module->order }}</td>
                        <td>
                            <div class="user-cell">
                                <span style="font-weight:600;">{{ $module->name }}</span>
                            </div>
                        </td>
                        <td><i class="bi {{ $module->icon }}"></i></td>
                        <td>
                            <span style="font-size:.8rem; color:var(--brand-muted);">
                                {{ $module->route ?? $module->url ?? '#' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $module->is_dashboard ? 'bg-info' : 'bg-primary' }}">
                                {{ $module->is_dashboard ? 'Dashboard' : 'Navegación' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $module->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $module->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <div class="actions-cell justify-content-end">
                                <a href="{{ route('modules.edit', $module) }}" class="btn-edit">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>

                                <form method="POST" action="{{ route('modules.destroy', $module) }}"
                                      onsubmit="return confirmDelete(event, '{{ addslashes($module->name) }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-delete">
                                        <i class="bi bi-trash3"></i> Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection