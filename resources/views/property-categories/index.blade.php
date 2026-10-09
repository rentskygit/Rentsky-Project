@extends('layouts.app')
@section('title','Categorías de Propiedades')

@section('content')
<div class="page-header">
    <h1 class="page-title">Categorías de <span>Propiedades</span></h1>
    <a href="{{ route('property-categories.create') }}" class="btn-create">
        <i class="bi bi-plus-lg"></i> Nueva categoría
    </a>
</div>

<div class="card-custom">
    <div class="card-header-inner">
        <i class="bi bi-tags-fill text-muted"></i>
        <span style="font-size:.875rem;font-weight:600;">Títulos disponibles</span>
        <span class="badge-count">{{ $categories->count() }}</span>
    </div>

    @if($categories->isEmpty())
        <div class="empty-state"><i class="bi bi-tag"></i><p>Sin categorías. Crea la primera.</p></div>
    @else
        <table class="table">
            <thead><tr>
                <th>Orden</th><th>Nombre</th><th>Icono</th><th>Propiedades</th><th>Estado</th><th class="text-end">Acciones</th>
            </tr></thead>
            <tbody>
            @foreach($categories as $c)
                <tr>
                    <td>{{ $c->order }}</td>
                    <td><strong>{{ $c->name }}</strong></td>
                    <td><i class="bi {{ $c->icon }}"></i></td>
                    <td><span class="badge-count">{{ $c->properties_count }}</span></td>
                    <td>
                        <span class="badge {{ $c->is_active ? 'bg-success':'bg-danger' }}">
                            {{ $c->is_active ? 'Activo':'Inactivo' }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="actions-cell justify-content-end">
                            <a href="{{ route('property-categories.edit', $c) }}" class="btn-edit"><i class="bi bi-pencil"></i> Editar</a>
                            <form method="POST" action="{{ route('property-categories.destroy', $c) }}"
                                  onsubmit="return confirmDelete(event,'{{ addslashes($c->name) }}')">
                                @csrf @method('DELETE')
                                <button class="btn-delete"><i class="bi bi-trash3"></i> Eliminar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</div>
@endsection