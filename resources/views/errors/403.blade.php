@extends('layouts.app')
@section('title', 'Access denied')

@section('content')
<div style="display:flex;align-items:center;justify-content:center;min-height:50vh;">
    <div class="card-custom" style="max-width:520px;padding:3rem 2rem;text-align:center;">
        <i class="bi bi-shield-lock" style="font-size:3rem;color:var(--brand-secondary);margin-bottom:1rem;display:block;"></i>
        <h1 style="font-family:var(--font-display);font-size:1.5rem;font-weight:700;margin-bottom:0.5rem;">Access denied</h1>
        <p style="color:var(--brand-muted);font-size:0.9rem;margin-bottom:1.5rem;">
            You don't have permission to view this module. If you think this is a mistake, contact your administrator.
        </p>
        <a href="{{ url('/') }}" class="btn-create">
            <i class="bi bi-house"></i> Back to home
        </a>
    </div>
</div>
@endsection