@extends('layouts.app')
@section('title', 'Edit Category')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit <span>Category</span></h1>
    <a href="{{ route('property-categories.index') }}" class="btn-create" style="background:#6b7280;border-color:#6b7280;">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card-custom">
    <div class="card-header-inner"><i class="bi bi-tag text-muted"></i><span>{{ $category->name }}</span></div>
    <div style="padding:1.5rem;">
        <form method="POST" action="{{ route('property-categories.update', $category) }}">
            @csrf
            @method('PUT')
            @include('property-categories._form', ['category' => $category])
            <div style="display:flex;gap:1rem;margin-top:1rem;">
                <button type="submit" class="btn-create"><i class="bi bi-check-lg"></i> Update</button>
                <a href="{{ route('property-categories.index') }}" class="btn-create" style="background:#6b7280;border-color:#6b7280;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection