@extends('layouts.app')

@section('title', 'Create Module')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Create <span>Module</span></h1>
        <a href="{{ route('modules.index') }}" class="btn-create" style="background:#6b7280;border-color:#6b7280;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-inner">
            <i class="bi bi-plus-square text-muted" style="font-size:.95rem"></i>
            <span style="font-size:.875rem; font-weight:600;">New module</span>
        </div>

        <div style="padding: 1.5rem;">
            <form method="POST" action="{{ route('modules.store') }}">
                @csrf

                <div class="row">
                    <div class="form-group">
                        <label class="form-label" for="name">Module name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="icon">Icon (Bootstrap)</label>
                        <input type="text" class="form-control @error('icon') is-invalid @enderror"
                               id="icon" name="icon" value="{{ old('icon', 'bi-box') }}" required>
                        <small class="text-muted">E.g. bi-people, bi-gear, bi-shield-lock</small>
                        @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label class="form-label" for="route">Route name</label>
                        <input type="text" class="form-control @error('route') is-invalid @enderror"
                               id="route" name="route" value="{{ old('route') }}">
                        <small class="text-muted">E.g. users.index, roles.index</small>
                        @error('route')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="url">Direct URL</label>
                        <input type="text" class="form-control @error('url') is-invalid @enderror"
                               id="url" name="url" value="{{ old('url') }}">
                        <small class="text-muted">Use either route or URL, not both</small>
                        @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label class="form-label" for="route_pattern">Active pattern</label>
                        <input type="text" class="form-control @error('route_pattern') is-invalid @enderror"
                               id="route_pattern" name="route_pattern" value="{{ old('route_pattern') }}">
                        <small class="text-muted">E.g. users.*, roles.*</small>
                        @error('route_pattern')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="order">Order</label>
                        <input type="number" class="form-control @error('order') is-invalid @enderror"
                               id="order" name="order" value="{{ old('order', 0) }}" required>
                        @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:0.5rem;">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <strong>Active module</strong>
                        </label>
                    </div>

                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:0.5rem;">
                            <input type="checkbox" id="is_dashboard" name="is_dashboard" value="1">
                            <strong>Dashboard module</strong>
                        </label>
                    </div>
                </div>

                <div style="display:flex;gap:1rem;margin-top:1rem;">
                    <button type="submit" class="btn-create">
                        <i class="bi bi-check-lg"></i> Create
                    </button>
                    <a href="{{ route('modules.index') }}" class="btn-create" style="background:#6b7280;border-color:#6b7280;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection