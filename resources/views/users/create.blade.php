@extends('layouts.app')
@section('title', 'Create User')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Create <span>User</span></h1>
        <a href="{{ route('users.index') }}" class="btn-create" style="background:#6b7280;border-color:#6b7280;">
            <i class="bi bi-arrow-left"></i>
            Back
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-inner">
            <i class="bi bi-person-plus text-muted"></i>
            <span>New User</span>
        </div>
        <div style="padding: 1.5rem;">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label" for="name">Name</label>
                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name') }}" placeholder="Full name" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" placeholder="name@example.com" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                           placeholder="Minimum 8 characters" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                           placeholder="Repeat the password" required>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn-create">
                        <i class="bi bi-check-lg"></i> Save
                    </button>
                    <a href="{{ route('users.index') }}" class="btn-create" style="background:#6b7280;border-color:#6b7280;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection