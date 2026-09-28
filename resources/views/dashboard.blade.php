@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="page-header">
        <h1 class="page-title">Panel de <span>Control</span></h1>
    </div>

    <div class="row">
        @foreach($dashboardModules ?? [] as $module)
            <div class="card-custom">
                <div class="card-header-inner">
                    <i class="bi {{ $module->icon }} text-muted" style="font-size:1.5rem"></i>
                    <span style="font-size:1rem; font-weight:600;">{{ $module->name }}</span>
                </div>
                <div style="padding: 1.5rem;">
                    <p class="text-muted" style="margin-bottom: 1rem;">Widget del módulo {{ $module->name }}</p>
                    <a href="{{ $module->url }}" class="btn-create" style="font-size:.8rem;">
                        <i class="bi bi-arrow-right"></i> Acceder
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endsection