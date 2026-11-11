@extends('layouts.app')
@section('title', 'Roles')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Role <span>Management</span></h1>
        <a href="{{ route('roles.create') }}" class="btn-create">
            <i class="bi bi-plus-lg"></i> New role
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-inner">
            <i class="bi bi-shield-lock-fill text-muted"></i>
            <span style="font-size:.875rem; font-weight:600;">System roles</span>
            <span class="badge-count">{{ $roles->count() }}</span>
        </div>

        @if($roles->isEmpty())
            <div class="empty-state">
                <i class="bi bi-shield"></i>
                <p class="mb-1" style="font-weight:600;">No roles yet</p>
                <p style="font-size:.875rem;">Create the first role to get started.</p>
            </div>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Modules</th>
                        <th>Users</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
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
                            <span class="status-badge {{ $role->is_active ? 'active' : 'inactive' }}">
                                {{ $role->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="actions-cell justify-content-end">
                                <a href="{{ route('roles.edit', $role) }}" class="btn-icon-action" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('roles.destroy', $role) }}"
                                      onsubmit="return confirmDelete(event, '{{ addslashes($role->name) }}')" style="display:inline;">
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