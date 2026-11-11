@extends('layouts.app')
@section('title', 'Modules')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Module <span>Management</span></h1>
        <a href="{{ route('modules.create') }}" class="btn-create">
            <i class="bi bi-plus-lg"></i> New module
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-inner">
            <i class="bi bi-grid-3x3-gap-fill text-muted" style="font-size:.95rem"></i>
            <span style="font-size:.875rem; font-weight:600;">System modules</span>
            <span class="badge-count">{{ $modules->count() }}</span>
        </div>

        @if($modules->isEmpty())
            <div class="empty-state">
                <i class="bi bi-grid"></i>
                <p class="mb-1" style="font-weight:600; color:var(--brand-dark);">No modules yet</p>
                <p style="font-size:.875rem">Create the first module to get started.</p>
            </div>
        @else
            <table class="table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Module</th>
                        <th>Icon</th>
                        <th>Route</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
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
                            <span class="status-badge {{ $module->is_dashboard ? 'info' : 'neutral' }}">
                                {{ $module->is_dashboard ? 'Dashboard' : 'Navigation' }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge {{ $module->is_active ? 'active' : 'inactive' }}">
                                {{ $module->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="actions-cell justify-content-end">
                                <a href="{{ route('modules.edit', $module) }}" class="btn-icon-action" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('modules.destroy', $module) }}"
                                      onsubmit="return confirmDelete(event, '{{ addslashes($module->name) }}')" style="display:inline;">
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