@extends('layouts.app')

@section('title', 'Find your home in Boston')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .landing-hero {
        margin: -2rem -2rem 2rem -2rem;
        background: linear-gradient(135deg, #1B1B18 0%, #2d2d2a 100%);
        color: white;
        border-radius: 1rem;
        overflow: hidden;
    }
    .hero-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 4rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2rem;
    }
    .hero-text h1 { font-size: 3rem; font-weight: 800; margin-bottom: 1rem; line-height: 1.2; }
    .hero-text h1 span { color: var(--brand-secondary); }
    .hero-text p { font-size: 1.15rem; color: #a0a09a; margin-bottom: 2rem; max-width: 500px; }
    .hero-badge {
        display: inline-block; background: var(--brand-secondary); color: white;
        padding: 0.3rem 1rem; border-radius: 9999px; font-size: 0.8rem;
        font-weight: 600; margin-bottom: 1rem; text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    .hero-image {
        flex-shrink: 0; width: 260px; height: 260px;
        background: rgba(255,255,255,0.05); border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 7rem; color: var(--brand-secondary);
        border: 2px solid rgba(245, 48, 3, 0.2);
    }

    /* ═══════════════════════════════════════════════════════
       SECCIÓN MAPA + LISTA
       ═══════════════════════════════════════════════════════ */
    .section-header { margin: 3rem 0 1.5rem; }
    .section-title {
        font-size: 1.75rem; font-weight: 700; color: var(--brand-primary);
        display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;
    }
    .section-title span { color: var(--brand-secondary); }
    .section-subtitle { color: var(--brand-muted); margin-top: 0.25rem; }

    .properties-layout {
        display: grid;
        grid-template-columns: 1.3fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
        min-height: 620px;
    }

    .map-wrapper {
        background: white; border-radius: 1rem; overflow: hidden;
        border: 1px solid #e5e7eb; position: relative;
    }
    #propertyMap { width: 100%; height: 100%; min-height: 620px; }

    .list-wrapper {
        background: white; border-radius: 1rem;
        border: 1px solid #e5e7eb; display: flex; flex-direction: column;
        max-height: 620px;
    }
    .list-header {
        padding: 1rem 1.25rem; border-bottom: 1px solid #f3f4f6;
        background: #f9fafb;
    }
    .list-header label {
        font-size: 0.8rem; font-weight: 700; color: var(--brand-muted);
        text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.5rem;
    }
    #addressSelect {
        width: 100%; padding: 0.6rem 2.5rem 0.6rem 0.875rem;
        border: 1px solid #d1d5db; border-radius: 0.5rem;
        font-size: 0.9rem; background: white; cursor: pointer;
        appearance: none; -webkit-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23706f6c'%3e%3cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.9rem center;
        background-size: 14px 14px;
    }
    #addressSelect:focus {
        outline: none; border-color: var(--brand-secondary);
        box-shadow: 0 0 0 3px rgba(245, 48, 3, 0.1);
    }

    .properties-list {
        flex: 1; overflow-y: auto; padding: 0.75rem;
    }

    .property-item {
        display: flex; gap: 0.75rem; padding: 0.75rem;
        border-radius: 0.5rem; cursor: pointer;
        transition: all 0.2s ease; border: 1px solid transparent;
        margin-bottom: 0.5rem;
    }
    .property-item:hover { background: #f9fafb; border-color: #e5e7eb; }
    .property-item.active { background: #fff5f2; border-color: var(--brand-secondary); }
    .property-item-img {
        width: 90px; height: 90px; flex-shrink: 0;
        border-radius: 0.5rem; overflow: hidden; background: #f3f4f6;
        display: flex; align-items: center; justify-content: center;
    }
    .property-item-img img { width: 100%; height: 100%; object-fit: cover; }
    .property-item-img i { color: #d1d5db; font-size: 1.75rem; }
    .property-item-body { flex: 1; min-width: 0; }
    .property-item-title {
        font-weight: 700; font-size: 0.9rem; color: var(--brand-primary);
        margin-bottom: 0.15rem; white-space: nowrap; overflow: hidden;
        text-overflow: ellipsis;
    }
    .property-item-addr {
        font-size: 0.8rem; color: var(--brand-muted);
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .property-item-meta {
        display: flex; gap: 0.5rem; margin-top: 0.4rem; flex-wrap: wrap;
    }
    .property-item-tag {
        font-size: 0.7rem; padding: 0.15rem 0.5rem; border-radius: 9999px;
        background: #f3f4f6; color: var(--brand-muted); font-weight: 600;
    }
    .property-item-tag.pet { background: #dcfce7; color: #166534; }
    .property-item-tag.rent { background: #fff5f2; color: var(--brand-secondary); }
    .empty-list { padding: 3rem 1rem; text-align: center; color: var(--brand-muted); }
    .empty-list i { font-size: 2.5rem; color: #d1d5db; display: block; margin-bottom: 0.75rem; }

    /* ═══════════════════════════════════════════════════════
       CATEGORÍAS
       ═══════════════════════════════════════════════════════ */
    .categories-grid {
        display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 1rem; margin-bottom: 3rem;
    }
    .category-card {
        background: white; border: 1px solid #e5e7eb; border-radius: 0.75rem;
        padding: 1.5rem 1rem; text-align: center; cursor: pointer;
        transition: all 0.3s ease; text-decoration: none; color: var(--brand-primary);
        display: block;
    }
    .category-card:hover {
        transform: translateY(-4px); box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        border-color: var(--brand-secondary);
    }
    .category-card .category-icon {
        font-size: 2.5rem; display: block; margin-bottom: 0.5rem;
        color: var(--brand-secondary);
    }
    .category-card .category-name { font-weight: 600; font-size: 0.95rem; }
    .category-card .category-count { font-size: 0.8rem; color: var(--brand-muted); }

    /* ═══════════════════════════════════════════════════════
       MODAL RICO
       ═══════════════════════════════════════════════════════ */
    .prop-modal-backdrop {
        position: fixed; inset: 0; background: rgba(0,0,0,0.6);
        z-index: 9999; display: none; align-items: center; justify-content: center;
        padding: 1.5rem;
    }
    .prop-modal-backdrop.open { display: flex; }

    .prop-modal {
        background: white; border-radius: 1rem; max-width: 1080px;
        width: 100%; max-height: 90vh; overflow: hidden;
        display: flex; flex-direction: column;
    }
    .prop-modal-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1rem 1.5rem; border-bottom: 1px solid #f3f4f6;
        flex-shrink: 0;
    }
    .prop-modal-header h2 { font-size: 1.05rem; font-weight: 700; margin: 0; }
    .prop-modal-close {
        background: none; border: none; font-size: 1.5rem; cursor: pointer;
        color: var(--brand-muted); width: 32px; height: 32px;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        transition: all 0.15s;
    }
    .prop-modal-close:hover { background: #f3f4f6; color: var(--brand-primary); }

    .prop-modal-body {
        display: grid;
        grid-template-columns: 1.1fr 1fr;
        gap: 0;
        overflow: hidden;
        padding: 0;
        flex: 1;
        min-height: 0;
    }

    .prop-modal-left {
        padding: 1.5rem;
        overflow-y: auto;
        border-right: 1px solid #f3f4f6;
        background: #fafafa;
    }
    .prop-modal-right {
        padding: 1.5rem;
        overflow-y: auto;
        background: white;
    }

    .prop-modal-gallery-main {
        width: 100%;
        aspect-ratio: 4/3;
        border-radius: 0.75rem;
        overflow: hidden;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .prop-modal-gallery-main img { width: 100%; height: 100%; object-fit: cover; }

    .prop-modal-thumbs {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.4rem;
        margin-top: 0.5rem;
    }
    .prop-modal-thumb {
        aspect-ratio: 1;
        border-radius: 0.4rem;
        overflow: hidden;
        border: 2px solid #e5e7eb;
        background: none;
        padding: 0;
        cursor: pointer;
        transition: border-color 0.15s;
    }
    .prop-modal-thumb.active { border-color: var(--brand-secondary); }
    .prop-modal-thumb:hover { border-color: var(--brand-secondary); }
    .prop-modal-thumb img { width: 100%; height: 100%; object-fit: cover; }

    .prop-modal-videos {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.6rem;
        margin-top: 0.75rem;
    }
    .prop-modal-videos video {
        width: 100%;
        border-radius: 0.5rem;
        background: black;
    }

    /* Modal - precio hero */
    .prop-m-price-hero {
        display: flex;
        align-items: baseline;
        gap: 0.5rem;
        margin-bottom: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f3f4f6;
    }
    .prop-m-price {
        font-size: 1.9rem;
        font-weight: 800;
        color: var(--brand-secondary);
        letter-spacing: -0.02em;
        line-height: 1;
    }
    .prop-m-price-period {
        font-size: 0.9rem;
        color: var(--brand-muted);
        font-weight: 500;
    }

    /* Specs inline */
    .prop-m-specs {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        padding-bottom: 1rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid #f3f4f6;
    }
    .prop-m-spec {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--brand-primary);
    }
    .prop-m-spec i { color: var(--brand-muted); }

    /* Available */
    .prop-m-available {
        background: #fff5f2;
        border: 1px solid #fbd8ce;
        border-radius: 0.5rem;
        padding: 0.65rem 0.85rem;
        font-size: 0.88rem;
        color: var(--brand-primary);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.25rem;
    }
    .prop-m-available i { color: var(--brand-secondary); font-size: 1rem; }

    /* Secciones del modal */
    .prop-m-section { margin-bottom: 1.25rem; }
    .prop-m-section-title {
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--brand-primary);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.6rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }
    .prop-m-section-title i { color: var(--brand-secondary); font-size: 0.9rem; }
    .prop-m-text {
        font-size: 0.88rem;
        color: var(--brand-muted);
        line-height: 1.65;
        white-space: pre-line;
        margin: 0;
    }

    /* Info rows */
    .prop-m-info-list {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        font-size: 0.85rem;
    }
    .prop-m-info-row {
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px dashed #f3f4f6;
    }
    .prop-m-info-row:last-child { border-bottom: none; padding-bottom: 0; }
    .prop-m-info-label { color: var(--brand-muted); }
    .prop-m-info-value { font-weight: 700; color: var(--brand-primary); text-align: right; }

    /* Chips */
    .prop-m-chips { display: flex; flex-wrap: wrap; gap: 0.4rem; }
    .prop-m-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.35rem 0.7rem;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 9999px;
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--brand-primary);
    }
    .prop-m-chip i { color: var(--brand-secondary); font-size: 0.85rem; }
    .prop-m-chip.pet {
        background: #fff5f2;
        border-color: #fbd8ce;
        color: var(--brand-secondary);
    }

    /* Screening list */
    .prop-m-screening {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    .prop-m-screening li {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        font-size: 0.83rem;
        color: var(--brand-primary);
        line-height: 1.55;
    }
    .prop-m-screening li i {
        color: var(--brand-secondary);
        font-size: 0.95rem;
        flex-shrink: 0;
        margin-top: 0.15rem;
    }

    /* Restrictions */
    .prop-m-restrictions {
        background: #fef3c7;
        border: 1px solid #fde68a;
        border-radius: 0.5rem;
        padding: 0.75rem 0.9rem;
        font-size: 0.83rem;
        color: #78350f;
        line-height: 1.6;
        white-space: pre-line;
    }

    /* CTA del modal */
    .prop-m-cta {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.8rem;
        background: var(--brand-secondary);
        color: white;
        border-radius: 0.5rem;
        font-weight: 700;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s;
        margin-top: 0.5rem;
    }
    .prop-m-cta:hover {
        background: #d42b02;
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
    }

    /* ═══════════════════════════════════════════════════════
       RESPONSIVE
       ═══════════════════════════════════════════════════════ */
    @media (max-width: 1024px) {
        .properties-layout { grid-template-columns: 1fr; }
        .list-wrapper { max-height: 500px; }
        #propertyMap { min-height: 400px; }
    }
    @media (max-width: 900px) {
        .prop-modal-body { grid-template-columns: 1fr; }
        .prop-modal-left { border-right: none; border-bottom: 1px solid #f3f4f6; max-height: 320px; }
        .prop-modal-right { max-height: 60vh; }
    }
    @media (max-width: 768px) {
        .hero-content { flex-direction: column; text-align: center; padding: 2.5rem 1.5rem; }
        .hero-text h1 { font-size: 2rem; }
        .hero-image { width: 160px; height: 160px; font-size: 4rem; }
        .landing-hero { margin: -1rem -1rem 2rem -1rem; border-radius: 0.5rem; }
        .prop-modal-thumbs { grid-template-columns: repeat(4, 1fr); }
    }
</style>
@endpush

@section('content')

{{-- ── HERO ── --}}
<section class="landing-hero">
    <div class="hero-content">
        <div class="hero-text">
            <span class="hero-badge">🏙️ Boston, MA</span>
            <h1>Find your <span>next home</span> in Boston</h1>
            <p>Explore apartments, studios and suites available. Filter by area, browse the map and find your ideal space.</p>
        </div>
        <div class="hero-image">🏠</div>
    </div>
</section>

{{-- ── MAPA + LISTA ── --}}
<div class="section-header">
    <h2 class="section-title">📍 Explore <span>properties</span> on the map</h2>
    <p class="section-subtitle">Select an address to locate it, or click a property to see the details.</p>
</div>

<div class="properties-layout">
    <div class="map-wrapper">
        <div id="propertyMap"></div>
    </div>

    <div class="list-wrapper">
        <div class="list-header">
            <label for="addressSelect">Jump to an address</label>
            <select id="addressSelect">
                <option value="">— Select a property —</option>
                @foreach($properties as $p)
                    <option value="{{ $p['id'] }}">
                        {{ $p['address'] }}{{ $p['city'] ? ', ' . $p['city'] : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="properties-list" id="propertiesList">
            @if($properties->isEmpty())
                <div class="empty-list">
                    <i class="bi bi-house-slash"></i>
                    <p>No properties published yet.</p>
                </div>
            @else
                @foreach($properties as $p)
                    <div class="property-item"
                         data-id="{{ $p['id'] }}"
                         data-lat="{{ $p['lat'] }}"
                         data-lng="{{ $p['lng'] }}">
                        <div class="property-item-img">
                            @if($p['primary_image'])
                                <img src="{{ $p['primary_image'] }}" alt="{{ $p['address'] }}">
                            @else
                                <i class="bi bi-house"></i>
                            @endif
                        </div>
                        <div class="property-item-body">
                            <div class="property-item-title">{{ $p['title'] }}</div>
                            <div class="property-item-addr">{{ $p['address'] }}{{ $p['city'] ? ', ' . $p['city'] : '' }}</div>
                            <div class="property-item-meta">
                                @if($p['rent_formatted'])
                                    <span class="property-item-tag rent">{{ $p['rent_formatted'] }}/mo</span>
                                @endif
                                @if($p['unit_type'])
                                    <span class="property-item-tag">{{ $p['unit_type'] }}</span>
                                @endif
                                @if($p['pet_friendly'])
                                    <span class="property-item-tag pet">🐾 Pet friendly</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>

{{-- ── CATEGORÍAS ── --}}
@if($categories->isNotEmpty())
    <div class="section-header" style="margin-top: 3rem;">
        <h2 class="section-title">🏘️ Browse by <span>type</span></h2>
        <p class="section-subtitle">Filter properties by category.</p>
    </div>

    <div class="categories-grid">
        @foreach($categories as $cat)
            <a href="{{ route('public.properties.index', ['category' => $cat->id]) }}" class="category-card">
                <i class="bi {{ $cat->icon ?? 'bi-house' }} category-icon"></i>
                <div class="category-name">{{ $cat->name }}</div>
                <div class="category-count">{{ $cat->properties_count }} properties</div>
            </a>
        @endforeach
    </div>
@endif

{{-- ── MODAL DETALLE ── --}}
<div class="prop-modal-backdrop" id="propModal">
    <div class="prop-modal">
        <div class="prop-modal-header">
            <h2 id="modalTitle">Property</h2>
            <button type="button" class="prop-modal-close" id="modalClose">&times;</button>
        </div>
        <div class="prop-modal-body">
            <div class="prop-modal-left">
                <div class="prop-modal-gallery-main" id="modalMainImage"></div>
                <div class="prop-modal-thumbs" id="modalThumbs"></div>
                <div id="modalVideos"></div>
            </div>
            <div class="prop-modal-right" id="modalInfo"></div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    'use strict';

    const properties = @json($properties);

    // const BOSTON = [42.3601, -71.0589];

    const geocoded = properties.filter(p => p.lat && p.lng);
    const initialCenter = geocoded.length > 0
        ? [
            geocoded.reduce((s, p) => s + p.lat, 0) / geocoded.length,
            geocoded.reduce((s, p) => s + p.lng, 0) / geocoded.length,
          ]
        : BOSTON;

    const map = L.map('propertyMap').setView(initialCenter, 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        maxZoom: 19,
    }).addTo(map);

    const icon = L.divIcon({
        className: 'custom-marker',
        html: '<div style="background:#f53003;width:32px;height:32px;border-radius:50% 50% 50% 0;transform:rotate(-45deg);border:3px solid white;box-shadow:0 2px 8px rgba(0,0,0,0.3);"><div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;transform:rotate(45deg);color:white;font-size:14px;">🏠</div></div>',
        iconSize: [32, 32],
        iconAnchor: [16, 32],
    });

    const markers = {};

    properties.forEach(p => {
        if (!p.lat || !p.lng) return;

        const marker = L.marker([p.lat, p.lng], { icon }).addTo(map);

        const priceLine = p.rent_formatted
            ? `<div style="color:#f53003;font-weight:800;font-size:0.95rem;margin:0.25rem 0;">${p.rent_formatted} <span style="font-weight:500;color:#706f6c;font-size:0.8rem;">/mo</span></div>`
            : '';

        marker.bindPopup(`
            <div style="font-family:inherit; min-width:200px;">
                <strong style="font-size:0.9rem;">${p.title}</strong><br>
                <span style="font-size:0.8rem;color:#706f6c;">${p.address}</span>
                ${priceLine}
                <button onclick="window.openPropertyModal(${p.id})"
                        style="margin-top:0.4rem;background:#f53003;color:white;border:none;padding:0.4rem 0.85rem;border-radius:0.4rem;font-size:0.8rem;font-weight:600;cursor:pointer;">
                    View details
                </button>
            </div>
        `);

        marker.on('click', () => highlightCard(p.id));
        markers[p.id] = marker;
    });

    if (geocoded.length > 1) {
        const bounds = L.latLngBounds(geocoded.map(p => [p.lat, p.lng]));
        map.fitBounds(bounds, { padding: [40, 40], maxZoom: 14 });
    }

    const select = document.getElementById('addressSelect');
    select?.addEventListener('change', function () {
        const id = parseInt(this.value);
        if (!id) return;

        const p = properties.find(x => x.id === id);
        if (!p) return;

        if (p.lat && p.lng) {
            map.flyTo([p.lat, p.lng], 16, { duration: 0.8 });
            markers[p.id]?.openPopup();
        }
        highlightCard(id);
    });

    function highlightCard(id) {
        document.querySelectorAll('.property-item').forEach(el => {
            el.classList.toggle('active', parseInt(el.dataset.id) === id);
        });
    }

    document.querySelectorAll('.property-item').forEach(card => {
        card.addEventListener('click', function () {
            const id = parseInt(this.dataset.id);
            const p = properties.find(x => x.id === id);
            if (!p) return;

            if (p.lat && p.lng) {
                map.flyTo([p.lat, p.lng], 16, { duration: 0.8 });
                markers[p.id]?.openPopup();
            }
            highlightCard(id);
            openPropertyModal(id);
        });
    });

    const modal       = document.getElementById('propModal');
    const modalTitle  = document.getElementById('modalTitle');
    const modalMain   = document.getElementById('modalMainImage');
    const modalThumbs = document.getElementById('modalThumbs');
    const modalInfo   = document.getElementById('modalInfo');
    const modalVideos = document.getElementById('modalVideos');
    const modalClose  = document.getElementById('modalClose');

    function openPropertyModal(id) {
        const p = properties.find(x => x.id === id);
        if (!p) return;

        modalTitle.textContent = (p.title || 'Property') + ' — ' + p.address;

        const images = (p.images && p.images.length)
            ? p.images
            : (p.primary_image ? [p.primary_image] : []);

        if (images.length) {
            modalMain.innerHTML = `<img src="${images[0]}" alt="">`;
            modalThumbs.innerHTML = images.map((url, i) =>
                `<button type="button" class="prop-modal-thumb ${i===0?'active':''}" data-url="${url}">
                    <img src="${url}">
                </button>`
            ).join('');
            modalThumbs.querySelectorAll('.prop-modal-thumb').forEach(btn => {
                btn.addEventListener('click', function () {
                    modalMain.innerHTML = `<img src="${this.dataset.url}" alt="">`;
                    modalThumbs.querySelectorAll('.prop-modal-thumb').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        } else {
            modalMain.innerHTML = `<i class="bi bi-image" style="font-size:4rem;color:#d1d5db;"></i>`;
            modalThumbs.innerHTML = '';
        }

        modalVideos.innerHTML = (p.videos && p.videos.length)
            ? `<div class="prop-m-section" style="margin-top:1rem;">
                 <div class="prop-m-section-title"><i class="bi bi-camera-video"></i> Video tour</div>
                 <div class="prop-modal-videos">
                   ${p.videos.map(v => `<video src="${v}" controls preload="metadata"></video>`).join('')}
                 </div>
               </div>`
            : '';

        let html = '';

        html += `<div class="prop-m-price-hero">`;
        if (p.rent_formatted) {
            html += `<span class="prop-m-price">${p.rent_formatted}</span>
                     <span class="prop-m-price-period">/ month</span>`;
        } else {
            html += `<span class="prop-m-price">Contact for pricing</span>`;
        }
        html += `</div>`;

        const specs = [];
        if (p.title)       specs.push(`<div class="prop-m-spec"><i class="bi bi-door-closed"></i>${p.title}</div>`);
        if (p.bathrooms)   specs.push(`<div class="prop-m-spec"><i class="bi bi-droplet"></i>${p.bathrooms} ba</div>`);
        if (p.square_feet) specs.push(`<div class="prop-m-spec"><i class="bi bi-rulers"></i>${parseInt(p.square_feet).toLocaleString()} sqft</div>`);
        if (p.unit_type)   specs.push(`<div class="prop-m-spec"><i class="bi bi-house"></i>${p.unit_type}</div>`);
        if (specs.length) {
            html += `<div class="prop-m-specs">${specs.join('')}</div>`;
        }

        if (p.available_date) {
            html += `<div class="prop-m-available">
                <i class="bi bi-calendar-check"></i>
                Available ${p.available_date}
            </div>`;
        }

        if (p.description) {
            html += `<div class="prop-m-section">
                <div class="prop-m-section-title"><i class="bi bi-text-paragraph"></i> About</div>
                <p class="prop-m-text">${p.description.replace(/\n/g,'<br>')}</p>
            </div>`;
        }

        const leaseRows = [];
        if (p.lease_term)        leaseRows.push(['Lease term', p.lease_term]);
        if (p.rent_formatted)    leaseRows.push(['Monthly rent', p.rent_formatted]);
        if (p.deposit_formatted) leaseRows.push(['Security deposit', p.deposit_formatted]);
        if (leaseRows.length) {
            html += `<div class="prop-m-section">
                <div class="prop-m-section-title"><i class="bi bi-cash-stack"></i> Lease & Costs</div>
                <div class="prop-m-info-list">
                    ${leaseRows.map(([k,v]) => `<div class="prop-m-info-row">
                        <span class="prop-m-info-label">${k}</span>
                        <span class="prop-m-info-value">${v}</span>
                    </div>`).join('')}
                </div>
            </div>`;
        }

        const features = [];
        if (p.parking_type)   features.push(['Parking', p.parking_type + (p.parking_spot ? ' — Spot ' + p.parking_spot : '')]);
        if (p.cooling_system) features.push(['Cooling', p.cooling_system]);
        if (p.heating_system) features.push(['Heating', p.heating_system]);
        if (p.laundry)        features.push(['Laundry', p.laundry]);
        if (features.length) {
            html += `<div class="prop-m-section">
                <div class="prop-m-section-title"><i class="bi bi-stars"></i> Features</div>
                <div class="prop-m-info-list">
                    ${features.map(([k,v]) => `<div class="prop-m-info-row">
                        <span class="prop-m-info-label">${k}</span>
                        <span class="prop-m-info-value">${v}</span>
                    </div>`).join('')}
                </div>
            </div>`;
        }

        if (p.pet_friendly) {
            const petChips = [];
            if (p.allow_cats)       petChips.push(`<span class="prop-m-chip pet"><i class="bi bi-check-circle-fill"></i> Cats welcome</span>`);
            if (p.allow_small_dogs) petChips.push(`<span class="prop-m-chip pet"><i class="bi bi-check-circle-fill"></i> Small dogs${p.small_dog_max_weight ? ' (≤ ' + p.small_dog_max_weight + ' lbs)' : ''}</span>`);
            if (p.allow_large_dogs) petChips.push(`<span class="prop-m-chip pet"><i class="bi bi-check-circle-fill"></i> Large dogs welcome</span>`);
            if (petChips.length) {
                html += `<div class="prop-m-section">
                    <div class="prop-m-section-title"><i class="bi bi-heart"></i> Pet policy</div>
                    <div class="prop-m-chips">${petChips.join('')}</div>
                    ${p.max_pets ? `<p class="prop-m-text" style="margin-top:.5rem;font-size:.8rem;">Up to <strong>${p.max_pets}</strong> pet${p.max_pets > 1 ? 's' : ''} allowed.</p>` : ''}
                </div>`;
            }
        }

        if (p.amenities && p.amenities.length) {
            html += `<div class="prop-m-section">
                <div class="prop-m-section-title"><i class="bi bi-list-check"></i> Amenities</div>
                <div class="prop-m-chips">
                    ${p.amenities.map(a => `<span class="prop-m-chip">${a.icon ? `<i class="bi ${a.icon}"></i>` : ''}${a.name}</span>`).join('')}
                </div>
            </div>`;
        }

        if (p.utilities && p.utilities.length) {
            html += `<div class="prop-m-section">
                <div class="prop-m-section-title"><i class="bi bi-plug"></i> Utilities included</div>
                <div class="prop-m-chips">
                    ${p.utilities.map(u => `<span class="prop-m-chip">${u.icon ? `<i class="bi ${u.icon}"></i>` : ''}${u.name}</span>`).join('')}
                </div>
            </div>`;
        }

        const screening = [];
        if (p.income_to_rent_ratio) {
            screening.push(`Combined gross monthly income of at least <strong>${p.income_to_rent_ratio}× the monthly rent</strong>.`);
        }
        if (p.allow_eviction_older_than_5_years) {
            screening.push(`Past evictions considered on a case-by-case basis if they occurred <strong>more than 5 years ago</strong>.`);
        } else {
            screening.push(`Recent evictions (within the last 5 years) may affect your application.`);
        }
        screening.push(`Standard credit and background screening applies to all applicants equally.`);

        html += `<div class="prop-m-section">
            <div class="prop-m-section-title"><i class="bi bi-clipboard-check"></i> What you'll need to qualify</div>
            <ul class="prop-m-screening">
                ${screening.map(s => `<li><i class="bi bi-check-circle"></i><span>${s}</span></li>`).join('')}
            </ul>
        </div>`;

        if (p.restrictions) {
            html += `<div class="prop-m-section">
                <div class="prop-m-section-title"><i class="bi bi-exclamation-circle"></i> House rules</div>
                <div class="prop-m-restrictions">${p.restrictions.replace(/\n/g,'<br>')}</div>
            </div>`;
        }

        html += `<a href="${p.url}" class="prop-m-cta">
            <i class="bi bi-arrow-right-circle"></i> View full details
        </a>`;

        modalInfo.innerHTML = html;

        modal.classList.add('open');
        document.body.style.overflow = 'hidden';
    }

    function closePropertyModal() {
        modal.classList.remove('open');
        document.body.style.overflow = '';
    }

    window.openPropertyModal = openPropertyModal;

    modalClose?.addEventListener('click', closePropertyModal);
    modal?.addEventListener('click', e => { if (e.target === modal) closePropertyModal(); });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && modal.classList.contains('open')) closePropertyModal();
    });

})();
</script>
@endpush