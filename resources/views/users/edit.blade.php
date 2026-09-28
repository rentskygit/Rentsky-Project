@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Editar <span>Usuario</span></h1>
        <a href="{{ route('users.index') }}" class="btn-create" style="background:#6b7280;">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')

        {{-- Datos básicos --}}
        <div class="card-custom" style="margin-bottom:1.5rem;">
            <div class="card-header-inner">
                <i class="bi bi-person-gear text-muted"></i>
                <span>Datos básicos</span>
            </div>
            <div style="padding:1.5rem;">
                <div class="form-group">
                    <label class="form-label" for="name">Nombre</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label style="display:flex; align-items:center; gap:0.5rem;">
                        <input type="checkbox" name="is_super_admin" value="1" {{ $user->is_super_admin ? 'checked' : '' }}>
                        <strong>Super Administrador</strong> (acceso total, ignora permisos)
                    </label>
                </div>
            </div>
        </div>

        {{-- Roles --}}
        <div class="card-custom" style="margin-bottom:1.5rem;">
            <div class="card-header-inner">
                <i class="bi bi-shield-lock text-muted"></i>
                <span>Roles asignados</span>
            </div>
            <div style="padding:1.5rem;">
                @if($roles->isEmpty())
                    <p style="color:var(--brand-muted);">No hay roles creados. <a href="{{ route('roles.create') }}">Crea uno primero</a>.</p>
                @else
                    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap:0.75rem;">
                        @foreach($roles as $role)
                            <label style="display:flex; align-items:center; gap:0.5rem; padding:0.6rem; border:1px solid #e5e7eb; border-radius:0.5rem; cursor:pointer;">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}"
                                       {{ in_array($role->id, $selectedRoles) ? 'checked' : '' }}>
                                <div>
                                    <div style="font-weight:600; font-size:0.9rem;">{{ $role->name }}</div>
                                    <div style="font-size:0.75rem; color:var(--brand-muted);">{{ $role->description ?? 'Sin descripción' }}</div>
                                </div>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Permisos directos (override) --}}
        <div class="card-custom" style="margin-bottom:1.5rem;">
            <div class="card-header-inner">
                <i class="bi bi-grid-3x3-gap text-muted"></i>
                <span>Permisos directos de módulos (override sobre roles)</span>
            </div>
            <div style="padding:1.5rem;">
                <p style="font-size:0.85rem; color:var(--brand-muted); margin-bottom:1rem;">
                    Marca los módulos que este usuario verá <strong>adicionalmente</strong> a lo que le dan sus roles.
                </p>
                @include('roles._modules-tree', ['modules' => $modules, 'selected' => $selectedModules])
            </div>
        </div>

        <div style="display:flex; gap:1rem;">
            <button type="submit" class="btn-create">
                <i class="bi bi-check-lg"></i> Guardar cambios
            </button>
            <a href="{{ route('users.index') }}" class="btn-create" style="background:#6b7280;">
                Cancelar
            </a>
        </div>
    </form>
@endsection