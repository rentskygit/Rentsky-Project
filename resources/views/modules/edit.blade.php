@extends('layouts.app')

@section('title', 'Editar Módulo')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Editar <span>Módulo</span></h1>
        <a href="{{ route('modules.index') }}" class="btn-back">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-inner">
            <i class="bi bi-pencil-square text-muted" style="font-size:.95rem"></i>
            <span style="font-size:.875rem; font-weight:600;">Editando: {{ $module->name }}</span>
        </div>

        <div style="padding: 1.5rem;">
            <form method="POST" action="{{ route('modules.update', $module) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Nombre del módulo</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $module->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="icon">Icono (Bootstrap)</label>
                            <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                   id="icon" name="icon" value="{{ old('icon', $module->icon) }}" required>
                            <small class="text-muted">Ej: bi-people, bi-gear, bi-shield-lock</small>
                            @error('icon')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="route">Nombre de ruta</label>
                            <input type="text" class="form-control @error('route') is-invalid @enderror" 
                                   id="route" name="route" value="{{ old('route', $module->route) }}">
                            <small class="text-muted">Ej: users.index, roles.index</small>
                            @error('route')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="url">URL directa</label>
                            <input type="text" class="form-control @error('url') is-invalid @enderror" 
                                   id="url" name="url" value="{{ old('url', $module->url) }}">
                            <small class="text-muted">Usar ruta o URL, no ambos</small>
                            @error('url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="route_pattern">Patrón para activo</label>
                            <input type="text" class="form-control @error('route_pattern') is-invalid @enderror" 
                                   id="route_pattern" name="route_pattern" value="{{ old('route_pattern', $module->route_pattern) }}">
                            <small class="text-muted">Ej: users.*, roles.*</small>
                            @error('route_pattern')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="order">Orden</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" 
                                   id="order" name="order" value="{{ old('order', $module->order) }}" required>
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_active" name="is_active" value="1" 
                                       {{ old('is_active', $module->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    Módulo activo
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_dashboard" name="is_dashboard" value="1"
                                       {{ old('is_dashboard', $module->is_dashboard) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_dashboard">
                                    Módulo de Dashboard
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn-submit">
                        <i class="bi bi-check-lg"></i> Actualizar
                    </button>
                    <a href="{{ route('modules.index') }}" class="btn-back">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
@endsection