@extends('layouts.app')

@section('title', 'Edit Module')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Edit <span>Module</span></h1>
        <a href="{{ route('modules.index') }}" class="btn-create" style="background:#6b7280;border-color:#6b7280;">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-inner">
            <i class="bi bi-pencil-square text-muted" style="font-size:.95rem"></i>
            <span style="font-size:.875rem; font-weight:600;">Editing: {{ $module->name }}</span>
        </div>

        <div style="padding: 1.5rem;">
            <form method="POST" action="{{ route('modules.update', $module) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="form-group">
                        <label class="form-label" for="name">Module name</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $module->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="icon">Icon (Bootstrap)</label>
                        <input type="text" class="form-control @error('icon') is-invalid @enderror"
                               id="icon" name="icon" value="{{ old('icon', $module->icon) }}" required>
                        <small class="text-muted">E.g. bi-people, bi-gear, bi-shield-lock</small>
                        @error('icon')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label class="form-label" for="route">Route name</label>
                        <input type="text" class="form-control @error('route') is-invalid @enderror"
                               id="route" name="route" value="{{ old('route', $module->route) }}">
                        <small class="text-muted">E.g. users.index, roles.index</small>
                        @error('route')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="url">Direct URL</label>
                        <input type="text" class="form-control @error('url') is-invalid @enderror"
                               id="url" name="url" value="{{ old('url', $module->url) }}">
                        <small class="text-muted">Use either route or URL, not both</small>
                        @error('url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label class="form-label" for="route_pattern">Active pattern</label>
                        <input type="text" class="form-control @error('route_pattern') is-invalid @enderror"
                               id="route_pattern" name="route_pattern" value="{{ old('route_pattern', $module->route_pattern) }}">
                        <small class="text-muted">E.g. users.*, roles.*</small>
                        @error('route_pattern')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="order">Order</label>
                        <input type="number" class="form-control @error('order') is-invalid @enderror"
                               id="order" name="order" value="{{ old('order', $module->order) }}" required>
                        @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:0.5rem;">
                            <input type="checkbox" id="is_active" name="is_active" value="1"
                                   {{ old('is_active', $module->is_active) ? 'checked' : '' }}>
                            <strong>Active module</strong>
                        </label>
                    </div>

                    <div class="form-group">
                        <label style="display:flex;align-items:center;gap:0.5rem;">
                            <input type="checkbox" id="is_dashboard" name="is_dashboard" value="1"
                                   {{ old('is_dashboard', $module->is_dashboard) ? 'checked' : '' }}>
                            <strong>Dashboard module</strong>
                        </label>
                    </div>
                </div>

                <div style="display:flex;gap:1rem;margin-top:1rem;">
                    <button type="submit" class="btn-create">
                        <i class="bi bi-check-lg"></i> Update
                    </button>
                    <a href="{{ route('modules.index') }}" class="btn-create" style="background:#6b7280;border-color:#6b7280;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection