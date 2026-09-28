@extends('layouts.app')

@section('title', 'Gestión de Productos')

@section('content')
<div class="page-header">
    <h1 class="page-title">Gestión de <span>Productos</span></h1>
    <a href="{{ route('products.create') }}" class="btn-create">
        <i class="bi bi-plus-circle"></i> Nuevo Producto
    </a>
</div>

{{-- Filtros --}}
<div class="card-custom mb-4">
    <div class="card-header-inner">
        <i class="bi bi-funnel text-muted"></i>
        <span>Filtros</span>
    </div>
    <div style="padding: 1.5rem;">
        <form method="GET" action="{{ route('products.index') }}" class="row">
            <div class="form-group" style="grid-column: span 1;">
                <label class="form-label">Buscar</label>
                <input type="text" name="search" class="form-control" 
                       value="{{ request('search') }}" placeholder="Nombre o SKU...">
            </div>
            
            <div class="form-group" style="grid-column: span 1;">
                <label class="form-label">Categoría</label>
                <select name="category" class="form-control">
                    <option value="">Todas</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" 
                            {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group" style="grid-column: span 1;">
                <label class="form-label">Estado</label>
                <select name="status" class="form-control">
                    <option value="">Todos</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activo</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivo</option>
                    <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Sin Stock</option>
                </select>
            </div>
            
            <div class="form-group" style="grid-column: span 1;">
                <label class="form-label">Ordenar</label>
                <select name="sort" class="form-control">
                    <option value="">Más reciente</option>
                    <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Precio: Menor a Mayor</option>
                    <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Precio: Mayor a Menor</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nombre: A-Z</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nombre: Z-A</option>
                </select>
            </div>
            
            <div class="form-group" style="grid-column: 1 / -1; display: flex; gap: 0.75rem; align-items: flex-end;">
                <button type="submit" class="btn-create" style="margin-bottom: 0;">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                <a href="{{ route('products.index') }}" class="btn-edit" style="margin-bottom: 0;">
                    <i class="bi bi-arrow-counterclockwise"></i> Limpiar
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabla de productos --}}
<div class="card-custom">
    <div class="card-header-inner">
        <i class="bi bi-box-seam text-muted"></i>
        <span>Productos</span>
        <span class="badge-count">{{ $products->total() }}</span>
    </div>
    
    <div style="overflow-x: auto;">
        <table class="table">
            <thead>
                <tr>
                    <th style="width: 60px;">Imagen</th>
                    <th>Producto</th>
                    <th>SKU</th>
                    <th>Stock</th>
                    <th>Precio</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        @if($product['primary_image'])
                            <img src="{{ $product['primary_image']['url'] }}" 
                                 alt="{{ $product['name'] }}" 
                                 style="width: 50px; height: 50px; object-fit: cover; border-radius: 0.375rem;">
                        @else
                            <div style="width: 50px; height: 50px; background: #f3f4f6; border-radius: 0.375rem; display: flex; align-items: center; justify-content: center; color: #9ca3af;">
                                <i class="bi bi-image"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div style="font-weight: 500;">{{ $product['name'] }}</div>
                        <div style="font-size: 0.8rem; color: var(--brand-muted);">
                            {{ $product['category']['name'] ?? 'Sin categoría' }}
                        </div>
                        @if($product['badge'] !== 'none')
                            <span class="product-badge {{ $product['badge_class'] }}" 
                                  style="position: relative; top: auto; right: auto; display: inline-block; margin-top: 0.25rem; font-size: 0.65rem;">
                                {{ $product['badge_label'] }}
                            </span>
                        @endif
                    </td>
                    <td><code style="font-size: 0.8rem;">{{ $product['sku'] }}</code></td>
                    <td>
                        <span style="font-weight: 600; color: {{ $product['stock'] > 10 ? '#16a34a' : ($product['stock'] > 0 ? '#f59e0b' : '#dc2626') }};">
                            {{ $product['stock'] }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight: 700; color: var(--brand-secondary);">
                            {{ $product['price'] }}
                        </div>
                        @if($product['original_price'])
                            <div style="font-size: 0.8rem; color: var(--brand-muted); text-decoration: line-through;">
                                {{ $product['original_price'] }}
                            </div>
                        @endif
                    </td>
                    <td>
                        <span style="display: inline-block; padding: 0.2rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; background: {{ $product['status_color'] }}20; color: {{ $product['status_color'] }};">
                            {{ $product['status_label'] }}
                        </span>
                    </td>
                    <td class="text-end">
                        <div class="actions-cell" style="justify-content: flex-end;">
                            <a href="{{ route('products.show', $product['slug']) }}" class="btn-edit" title="Ver">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('products.edit', $product['id']) }}" class="btn-edit" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('products.destroy', $product['id']) }}" method="POST" 
                                  onsubmit="return confirmDelete(event, '{{ $product['name'] }}')" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" title="Eliminar">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-box"></i>
                            <h3 style="margin-bottom: 0.5rem;">No hay productos</h3>
                            <p style="color: var(--brand-muted);">Comienza creando tu primer producto.</p>
                            <a href="{{ route('products.create') }}" class="btn-create" style="margin-top: 1rem;">
                                <i class="bi bi-plus-circle"></i> Crear Producto
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div style="padding: 1rem 1.5rem; border-top: 1px solid #e5e7eb;">
        {{ $products->links() }}
    </div>
</div>
@endsection