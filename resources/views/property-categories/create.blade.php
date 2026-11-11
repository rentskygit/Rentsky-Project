@extends('layouts.app')
@section('title', 'New Category')

@section('content')
<div class="page-header">
    <h1 class="page-title">New <span>Category</span></h1>
    <a href="{{ route('property-categories.index') }}" class="btn-create" style="background:#6b7280;border-color:#6b7280;">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card-custom">
    <div class="card-header-inner"><i class="bi bi-tag text-muted"></i><span>Category information</span></div>
    <div style="padding:1.5rem;">
        <form method="POST" action="{{ route('property-categories.store') }}">
            @csrf
            @include('property-categories._form')
            <div style="display:flex;gap:1rem;margin-top:1rem;">
                <button type="submit" class="btn-create"><i class="bi bi-check-lg"></i> Create</button>
                <a href="{{ route('property-categories.index') }}" class="btn-create" style="background:#6b7280;border-color:#6b7280;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection