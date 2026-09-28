@extends('layouts.app')

@section('title', 'Crear Rol')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Crear <span>Rol</span></h1>
        <a href="{{ route('roles.index') }}" class="btn-create" style="background:#6b7280;">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-inner">
            <i class="bi bi-shield-plus text-muted"></i>
            <span>Nuevo rol</span>
        </div>
        <div style="padding:1.5rem;">
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="name">Nombre</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="description">Descripción</label>
                    <input type="text" id="description" name="description" class="form-control"
                           value="{{ old('description') }}">
                </div>

                <div class="form-group">
                    <label class="form-label">Módulos permitidos</label>
                    @include('roles._modules-tree', ['modules' => $modules, 'selected' => old('modules', [])])
                </div>

                <div class="form-group">
                    <label style="display:flex; align-items:center; gap:0.5rem;">
                        <input type="checkbox" name="is_active" value="1" checked>
                        Rol activo
                    </label>
                </div>

                <div style="display:flex; gap:1rem; margin-top:1.5rem;">
                    <button type="submit" class="btn-create">
                        <i class="bi bi-check-lg"></i> Crear
                    </button>
                    <a href="{{ route('roles.index') }}" class="btn-create" style="background:#6b7280;">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection