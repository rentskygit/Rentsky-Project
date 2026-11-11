@extends('layouts.app')
@section('title', 'Properties')

@section('content')
<div class="page-header">
    <h1 class="page-title">Property <span>Management</span></h1>
    <a href="{{ route('properties.create') }}" class="btn-create">
        <i class="bi bi-plus-lg"></i> New property
    </a>
</div>

<div class="card-custom" style="margin-bottom:1.5rem;">
    <div class="card-header-inner">
        <i class="bi bi-funnel text-muted"></i>
        <span>Filters</span>
    </div>
    <div style="padding:1.25rem;">
        <form method="GET" action="{{ route('properties.index') }}" class="row">
            <div class="form-group">
                <label class="form-label">Search</label>
                <input type="text" name="search" class="form-control"
                       value="{{ request('search') }}" placeholder="Address or ZIP">
            </div>
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category" class="form-control">
                    <option value="">All</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-control">
                    <option value="">All</option>
                    <option value="active"   @selected(request('status')==='active')>Active</option>
                    <option value="inactive" @selected(request('status')==='inactive')>Inactive</option>
                </select>
            </div>
            <div class="form-group" style="display:flex;gap:.5rem;align-items:flex-end;">
                <button class="btn-create"><i class="bi bi-search"></i> Filter</button>
                <a href="{{ route('properties.index') }}" class="btn-edit">Clear</a>
            </div>
        </form>
    </div>
</div>

<div class="card-custom">
    <div class="card-header-inner">
        <i class="bi bi-house-door-fill text-muted"></i>
        <span style="font-size:.875rem;font-weight:600;">All properties</span>
        <span class="badge-count">{{ $properties->total() }}</span>
    </div>

    @if($properties->isEmpty())
        <div class="empty-state"><i class="bi bi-house"></i><p>No properties found.</p></div>
    @else
        <table class="table">
            <thead><tr>
                <th>Photo</th>
                <th>Category</th>
                <th>Address</th>
                <th>ZIP</th>
                <th>Unit</th>
                <th>Views</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr></thead>
            <tbody>
            @foreach($properties as $p)
                <tr>
                    <td>
                        @if($p->primaryImage)
                            <img src="{{ $p->primaryImage->image_url }}" style="width:50px;height:50px;object-fit:cover;border-radius:.4rem;">
                        @else
                            <div style="width:50px;height:50px;background:#f3f4f6;border-radius:.4rem;display:flex;align-items:center;justify-content:center;color:#9ca3af;">
                                <i class="bi bi-image"></i>
                            </div>
                        @endif
                    </td>
                    <td><strong>{{ $p->category->name ?? '—' }}</strong></td>
                    <td>{{ $p->address }}</td>
                    <td><code>{{ $p->zip_code }}</code></td>
                    <td>{{ $p->unit_type_label }}</td>
                    <td><span class="badge-count">{{ $p->views_count }}</span></td>
                    <td>
                        <span class="status-badge {{ $p->status === 'active' ? 'active' : 'inactive' }}">
                            {{ $p->status === 'active' ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="actions-cell justify-content-end">
                            <a href="{{ route('properties.show', $p) }}" class="btn-icon-action" title="View">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('properties.edit', $p) }}" class="btn-icon-action" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <!-- <form method="POST" action="{{ route('properties.destroy', $p) }}"
                                  onsubmit="return confirmDelete(event,'{{ addslashes($p->address) }}')" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon-action danger" title="Delete">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form> -->
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="padding:1rem 1.25rem;">{{ $properties->links() }}</div>
    @endif
</div>
@endsection