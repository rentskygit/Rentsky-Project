@extends('layouts.app')
@section('title', 'Property Categories')

@section('content')
<div class="page-header">
    <h1 class="page-title">Property <span>Categories</span></h1>
    <a href="{{ route('property-categories.create') }}" class="btn-create">
        <i class="bi bi-plus-lg"></i> New category
    </a>
</div>

<div class="card-custom">
    <div class="card-header-inner">
        <i class="bi bi-tags-fill text-muted"></i>
        <span style="font-size:.875rem;font-weight:600;">Available categories</span>
        <span class="badge-count">{{ $categories->count() }}</span>
    </div>

    @if($categories->isEmpty())
        <div class="empty-state"><i class="bi bi-tag"></i><p>No categories yet. Create the first one.</p></div>
    @else
        <table class="table">
            <thead><tr>
                <th>Order</th><th>Name</th><th>Icon</th><th>Properties</th><th>Status</th><th class="text-end">Actions</th>
            </tr></thead>
            <tbody>
            @foreach($categories as $c)
                <tr>
                    <td>{{ $c->order }}</td>
                    <td><strong>{{ $c->name }}</strong></td>
                    <td><i class="bi {{ $c->icon }}"></i></td>
                    <td><span class="badge-count">{{ $c->properties_count }}</span></td>
                    <td>
                        <span class="status-badge {{ $c->is_active ? 'active':'inactive' }}">
                            {{ $c->is_active ? 'Active':'Inactive' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="actions-cell justify-content-end">
                            <a href="{{ route('property-categories.edit', $c) }}" class="btn-icon-action" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form method="POST" action="{{ route('property-categories.destroy', $c) }}"
                                  onsubmit="return confirmDelete(event,'{{ addslashes($c->name) }}')" style="display:inline;">
                                @csrf @method('DELETE')
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