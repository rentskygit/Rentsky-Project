@extends('layouts.app')

@section('title', 'Gestión de Roles')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Gestión de <span>Roles</span></h1>
        <a href="{{ route('roles.create') }}" class="btn-create">
            <i class="bi bi-plus-lg"></i> Nuevo rol
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-inner">
            <i class="bi bi-shield-lock-fill text-muted"></i>
            <span style="font-size:.875rem; font-weight:600;">Roles del sistema</span>
            <span class="badge-count">{{ $roles->count() }}</span>
        </div>

        @if($roles->isEmpty())
            <div class="empty-state">
                <i class="bi bi-shield"></i>
                <p class="mb-1" style="font-weight:600;">Sin roles</p>
                <p style="font-size:.875rem;">Crea el primer rol para empezar.</p>
            </div>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Módulos</th>
                        <th>Usuarios</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($roles as $role)
                    <tr>
                        <td><strong>{{ $role->name }}</strong><br><small style="color:var(--brand-muted);">{{ $role->slug }}</small></td>
                        <td>{{ $role->description ?? '—' }}</td>
                        <td><span class="badge-count">{{ $role->modules_count }}</span></td>
                        <td><span class="badge-count">{{ $role->users_count }}</span></td>
                        <td>
                            <span style="display:inline-block; padding:0.2rem 0.75rem; border-radius:9999px; font-size:0.75rem; font-weight:600; background: {{ $role->is_active ? '#dcfce7' : '#fee2e2' }}; color: {{ $role->is_active ? '#166534' : '#991b1b' }};">
                                {{ $role->is_active ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td>
                            <div class="actions-cell justify-content-end">
                                <a href="{{ route('roles.edit', $role) }}" class="btn-edit">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <form method="POST" action="{{ route('roles.destroy', $role) }}"
                                      onsubmit="return confirmDelete(event, '{{ addslashes($role->name) }}')">
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