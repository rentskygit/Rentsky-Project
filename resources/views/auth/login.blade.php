@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('content')
<div class="auth-card">
    <div class="auth-brand">
        <h1>JCM<span>Realty Group</span></h1>
        <p>Log in to continue</p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label class="form-label" for="email">Email</label>
            <input
                type="email"
                id="email"
                name="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                placeholder="email@example.com"
                required
                autofocus
            >
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input
                type="password"
                id="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                placeholder="••••••••"
                required
            >
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group" style="display: flex; align-items: center; gap: 0.5rem;">
            <input type="checkbox" id="remember" name="remember" value="1" {{ old('remember') ? 'checked' : '' }}>
            <label for="remember" style="margin: 0; font-size: 0.9rem; cursor: pointer;">Remember me</label>
        </div>

        <button type="submit" class="btn-auth">
            <i class="bi bi-box-arrow-in-right"></i> Log in
        </button>

        <p class="auth-footer">
            Don't have an account? <a href="{{ route('register') }}">Sign up here</a>
        </p>
    </form>
</div>
@endsection