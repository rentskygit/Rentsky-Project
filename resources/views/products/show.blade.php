@extends('layouts.app')

@section('title', $product['name'])

@section('content')
    <div class="page-header">
        <h1 class="page-title">{{ $product['name'] }}</h1>
        <div style="display:flex; gap:0.75rem;">
            <a href="{{ route('products.edit', $product['id']) }}" class="btn-create">
                <i class="bi bi-pencil"></i> Editar
            </a>
            <a href="{{ route('products.index') }}" class="btn-create" style="background:#6b7280;">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:2rem; align-items:start;">
        {{-- Galería --}}
        <div>
            @if(!empty($product['images']))
                <div class="product-gallery-main">
                    <img id="mainImage"
                         src="{{ $product['primary_image']['url'] ?? $product['images'][0]['url'] }}"
                         alt="{{ $product['name'] }}">
                </div>
                @if(count($product['images']) > 1)
                    <div class="product-gallery-thumbs">
                        @foreach($product['images'] as $image)
                            <button type="button"
                                    class="product-thumb {{ $image['is_primary'] ? 'active' : '' }}"
                                    onclick="document.getElementById('mainImage').src = '{{ $image['url'] }}'">
                                <img src="{{ $image['url'] }}" alt="Miniatura">
                            </button>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="product-gallery-main product-gallery-empty">
                    <i class="bi bi-image"></i>
                    <p>Sin imágenes</p>
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div>
            <div class="product-meta">
                @if($product['badge'] !== 'none')
                    <span class="product-badge {{ $product['badge_class'] }}">{{ $product['badge_label'] }}</span>
                @endif
                @if($product['featured'])
                    <span class="product-badge featured">Destacado</span>
                @endif
            </div>

            <div class="product-price-row">
                <span class="product-price-current">{{ $product['price'] }}</span>
                @if($product['original_price'])
                    <span class="product-price-original">{{ $product['original_price'] }}</span>
                    <span class="product-price-discount">-{{ $product['discount_percentage'] }}%</span>
                @endif
            </div>

            <div class="product-info-list">
                <div><strong>SKU:</strong> <code>{{ $product['sku'] }}</code></div>
                <div><strong>Categoría:</strong> {{ $product['category']['name'] ?? 'Sin categoría' }}</div>
                <div><strong>Stock:</strong>
                    <span class="stock-badge" style="background:{{ $product['in_stock'] ? '#dcfce7' : '#fee2e2' }}; color:{{ $product['in_stock'] ? '#166534' : '#991b1b' }};">
                        {{ $product['stock'] }} unidades ({{ $product['stock_status'] }})
                    </span>
                </div>
                <div><strong>Estado:</strong>
                    <span style="color:{{ $product['status_color'] }}; font-weight:600;">
                        {{ $product['status_label'] }}
                    </span>
                </div>
                <div><strong>Vistas:</strong> {{ $product['views'] }}</div>
                <div><strong>Creado:</strong> {{ $product['created_at'] }} ({{ $product['created_at_diff'] }})</div>
            </div>

            @if($product['description'])
                <div class="product-description">
                    <h3>Descripción</h3>
                    <p>{!! $product['description_html'] !!}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Productos relacionados --}}
    @if(!$relatedProducts->isEmpty())
        <div style="margin-top:3rem;">
            <h2 style="font-size:1.25rem; font-weight:700; margin-bottom:1rem;">Productos relacionados</h2>
            <div class="related-grid">
                @foreach($relatedProducts as $related)
                    <a href="{{ $related['url'] }}" class="related-card">
                        @if($related['primary_image'])
                            <img src="{{ $related['primary_image']['url'] }}" alt="{{ $related['name'] }}">
                        @else
                            <div class="related-card-placeholder"><i class="bi bi-image"></i></div>
                        @endif
                        <div class="related-card-body">
                            <div class="related-card-name">{{ $related['name'] }}</div>
                            <div class="related-card-price">{{ $related['price'] }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
@endsection

@push('styles')
<style>
    .product-gallery-main {
        width: 100%;
        aspect-ratio: 1;
        border-radius: 1rem;
        overflow: hidden;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .product-gallery-main img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-gallery-empty {
        color: var(--brand-muted);
        flex-direction: column;
        gap: 0.5rem;
    }

    .product-gallery-empty i {
        font-size: 3rem;
        color: #d1d5db;
    }

    .product-gallery-thumbs {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.5rem;
        margin-top: 0.75rem;
    }

    .product-thumb {
        aspect-ratio: 1;
        border-radius: 0.5rem;
        overflow: hidden;
        border: 2px solid #e5e7eb;
        background: none;
        padding: 0;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .product-thumb:hover,
    .product-thumb.active {
        border-color: var(--brand-secondary);
    }

    .product-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-meta {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .product-badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .product-badge.sale { background: #fef3c7; color: #92400e; }
    .product-badge.liquidation { background: #fee2e2; color: #991b1b; }
    .product-badge.new { background: #dbeafe; color: #1e40af; }
    .product-badge.featured { background: #fce7f3; color: #9d174d; }

    .product-price-row {
        display: flex;
        align-items: baseline;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .product-price-current {
        font-size: 2rem;
        font-weight: 800;
        color: var(--brand-secondary);
    }

    .product-price-original {
        font-size: 1.1rem;
        color: var(--brand-muted);
        text-decoration: line-through;
    }

    .product-price-discount {
        background: var(--brand-secondary);
        color: white;
        padding: 0.15rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.85rem;
        font-weight: 700;
    }

    .product-info-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        padding: 1.25rem;
        background: #f9fafb;
        border-radius: 0.75rem;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
    }

    .stock-badge {
        display: inline-block;
        padding: 0.15rem 0.6rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .product-description {
        padding: 1.25rem;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
    }

    .product-description h3 {
        font-size: 1rem;
        font-weight: 700;
        margin-bottom: 0.75rem;
    }

    .product-description p {
        color: var(--brand-muted);
        line-height: 1.7;
        font-size: 0.95rem;
    }

    .related-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }

    .related-card {
        display: block;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        overflow: hidden;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .related-card:hover {
        border-color: var(--brand-secondary);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(245, 48, 3, 0.1);
    }

    .related-card img,
    .related-card-placeholder {
        width: 100%;
        aspect-ratio: 1;
        object-fit: cover;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9fafb;
        color: #d1d5db;
        font-size: 2rem;
    }

    .related-card-body {
        padding: 0.75rem;
    }

    .related-card-name {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--brand-primary);
        margin-bottom: 0.25rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .related-card-price {
        color: var(--brand-secondary);
        font-weight: 700;
        font-size: 0.95rem;
    }

    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }

        .product-gallery-thumbs {
            grid-template-columns: repeat(4, 1fr);
        }
    }
</style>
@endpush