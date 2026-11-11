@extends('layouts.app')
@section('title', 'Create Role')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Create <span>Role</span></h1>
        <a href="{{ route('roles.index') }}" class="btn-create" style="background:#6b7280;border-color:#6b7280;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    @if($errors->any())
        <div style="background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:0.75rem 1rem;border-radius:4px;margin-bottom:1.5rem;font-size:0.85rem;">
            <ul style="margin:0;padding-left:1.25rem;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('roles.store') }}">
        @csrf

        <div class="card-custom" style="margin-bottom:1.5rem;">
            <div class="card-header-inner">
                <i class="bi bi-shield-plus text-muted"></i>
                <span>Role information</span>
            </div>
            <div style="padding:1.5rem;">
                <div class="form-group">
                    <label class="form-label" for="name">Name</label>
                    <input type="text" id="name" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Description</label>
                    <input type="text" id="description" name="description"
                           class="form-control @error('description') is-invalid @enderror"
                           value="{{ old('description') }}">
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label style="display:flex;align-items:center;gap:0.5rem;">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <strong>Active role</strong>
                    </label>
                </div>
            </div>
        </div>

        <div class="card-custom" style="margin-bottom:1.5rem;">
            <div class="card-header-inner">
                <i class="bi bi-grid-3x3-gap text-muted"></i>
                <span>Module permissions</span>
            </div>
            <div style="padding:1.5rem;">
                <p style="font-size:0.85rem;color:var(--brand-muted);margin-bottom:1.25rem;line-height:1.6;">
                    Select the modules this role should have access to.
                    Checking a submodule will automatically check its parent.
                </p>

                @if($modules->isEmpty())
                    <p style="color:var(--brand-muted);">No modules available.</p>
                @else
                    @include('roles._modules-tree', [
                        'modules'  => $modules,
                        'selected' => old('modules', []),
                    ])
                @endif
            </div>
        </div>

        <div style="display:flex;gap:1rem;">
            <button type="submit" class="btn-create">
                <i class="bi bi-check-lg"></i> Create role
            </button>
            <a href="{{ route('roles.index') }}" class="btn-create" style="background:#6b7280;border-color:#6b7280;">
                Cancel
            </a>
        </div>
    </form>
@endsection