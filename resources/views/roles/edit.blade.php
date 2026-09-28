@extends('layouts.app')

@section('title', 'Editar Rol')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Editar <span>Rol</span></h1>
        <a href="{{ route('roles.index') }}" class="btn-create" style="background:#6b7280;">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-inner">
            <i class="bi bi-shield-gear text-muted"></i>
            <span>Editando: {{ $role->name }}</span>
        </div>
        <div style="padding:1.5rem;">
            <form method="POST" action="{{ route('roles.update', $role) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label" for="name">Nombre</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $role->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Descripción</label>
                    <input type="text" id="description" name="description" class="form-control"
                           value="{{ old('description', $role->description) }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Módulos permitidos</label>
                    @include('roles._modules-tree', [
                        'modules' => $modules,
                        'selected' => old('modules', $role->modules->pluck('id')->toArray())
                    ])
                </div>

                <div class="form-group">
                    <label style="display:flex; align-items:center; gap:0.5rem;">
                        <input type="checkbox" name="is_active" value="1" {{ $role->is_active ? 'checked' : '' }}>
                        Rol activo
                    </label>
                </div>

                <div style="display:flex; gap:1rem; margin-top:1.5rem;">
                    <button type="submit" class="btn-create">
                        <i class="bi bi-check-lg"></i> Actualizar
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn-create" style="background:#6b7280;">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection