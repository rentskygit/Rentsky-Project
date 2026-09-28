@extends('layouts.app')

@section('title', 'Crear Usuario')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Crear <span>Usuario</span></h1>
        <a href="{{ route('users.index') }}" class="btn-create" style="background: #6b7280;">
            <i class="bi bi-arrow-left"></i>
            Volver
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-inner">
            <i class="bi bi-person-plus text-muted"></i>
            <span>Nuevo Usuario</span>
        </div>
        <div style="padding: 1.5rem;">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="name">Nombre</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" 
                           value="{{ old('name') }}" placeholder="Nombre completo" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                           value="{{ old('email') }}" placeholder="correo@ejemplo.com" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Contraseña</label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Mínimo 8 caracteres" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirmar Contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" 
                           placeholder="Repite la contraseña" required>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn-create">
                        <i class="bi bi-check-lg"></i> Guardar
                    </button>
                    <a href="{{ route('users.index') }}" class="btn-create" style="background: #6b7280;">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection