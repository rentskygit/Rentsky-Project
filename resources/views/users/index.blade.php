@extends('layouts.app')
@section('title', 'Users')

@section('content')
    <div class="page-header">
        <h1 class="page-title">User <span>Management</span></h1>
        <a href="{{ route('users.create') }}" class="btn-create">
            <i class="bi bi-plus-lg"></i> New user
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-inner">
            <i class="bi bi-people-fill text-muted" style="font-size:.95rem"></i>
            <span style="font-size:.875rem; font-weight:600;">All users</span>
            <span class="badge-count">{{ $users->count() }}</span>
        </div>

        @if($users->isEmpty())
            <div class="empty-state">
                <i class="bi bi-person-slash"></i>
                <p class="mb-1" style="font-weight:600; color:var(--brand-dark);">No users yet</p>
                <p style="font-size:.875rem; color:var(--brand-muted);">Create the first one to get started.</p>
            </div>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Roles</th>
                        <th>Created</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <span class="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                <span class="user-name">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="user-email">{{ $user->email }}</span>
                        </td>
                        <td>
                            @if($user->is_super_admin)
                                <span class="status-badge superadmin">SUPER ADMIN</span>
                            @endif
                            @forelse($user->roles as $role)
                                <span class="status-badge role" style="margin-right:0.25rem;">{{ $role->name }}</span>
                            @empty
                                @unless($user->is_super_admin)
                                    <span style="color:var(--brand-muted); font-size:0.8rem;">No roles</span>
                                @endunless
                            @endforelse
                        </td>
                        <td>
                            <span style="font-size:.8rem; color:var(--brand-muted);">
                                {{ $user->created_at->format('M d, Y') }}
                            </span>
                        </td>
                        <td>
                            <div class="actions-cell justify-content-end">
                                <a href="{{ route('users.edit', $user) }}" class="btn-icon-action" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('users.destroy', $user) }}"
                                      onsubmit="return confirmDelete(event, '{{ addslashes($user->name) }}')" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon-action danger" title="Delete">
                                        <i class="bi bi-trash3"></i>
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