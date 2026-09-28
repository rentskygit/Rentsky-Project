@extends('layouts.app')

@section('title', 'Gestión de Usuarios')

@section('content')
    {{-- ── Page header ── --}}
    <div class="page-header">
        <h1 class="page-title">Gestión de <span>Usuarios</span></h1>
        <a href="{{ route('users.create') }}" class="btn-create">
            <i class="bi bi-plus-lg"></i>
            Nuevo usuario
        </a>
    </div>

    {{-- ── Table card ── --}}
    <div class="card-custom">
        <div class="card-header-inner">
            <i class="bi bi-people-fill text-muted" style="font-size:.95rem"></i>
            <span style="font-size:.875rem; font-weight:600;">Todos los usuarios</span>
            <span class="badge-count">{{ $users->count() }}</span>
        </div>

        @if($users->isEmpty())
            <div class="empty-state">
                <i class="bi bi-person-slash"></i>
                <p class="mb-1" style="font-weight:600; color:var(--brand-dark);">Sin usuarios aún</p>
                <p style="font-size:.875rem; color:var(--brand-muted);">Crea el primero para empezar.</p>
            </div>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Creado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <span class="avatar">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </span>
                                <span class="user-name">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="user-email">{{ $user->email }}</span>
                        </td>
                        <td>
                            @if($user->is_super_admin)
                                <span style="display:inline-block; padding:0.15rem 0.6rem; border-radius:9999px; font-size:0.7rem; font-weight:700; background:#fef3c7; color:#92400e;">SUPER ADMIN</span>
                            @endif
                            @forelse($user->roles as $role)
                                <span style="display:inline-block; padding:0.15rem 0.6rem; border-radius:9999px; font-size:0.7rem; font-weight:600; background:#e0e7ff; color:#3730a3; margin-right:0.25rem;">
                                    {{ $role->name }}
                                </span>
                            @empty
                                @unless($user->is_super_admin)
                                    <span style="color:var(--brand-muted); font-size:0.8rem;">Sin roles</span>
                                @endunless
                            @endforelse
                        </td>
                        <td>
                            <span style="font-size:.8rem; color:var(--brand-muted);">
                                {{ $user->created_at->format('d M Y') }}
                            </span>
                        </td>
                        <td>
                            <div class="actions-cell justify-content-end">
                                <a href="{{ route('users.edit', $user) }}" class="btn-edit">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>

                                <form method="POST" action="{{ route('users.destroy', $user) }}"
                                      onsubmit="return confirmDelete(event, '{{ addslashes($user->name) }}')">
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