@extends('layouts.app')
@section('title', 'Our Apartments')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    .apt-hero {
        padding: 3rem 0 2rem;
        border-bottom: 1px solid var(--border);
        margin-bottom: 2rem;
    }
    .apt-title {
        font-family: var(--font-display);
        font-size: 2.5rem;
        font-weight: 700;
        letter-spacing: -0.03em;
        margin-bottom: 0.5rem;
        color: var(--brand-primary);
        line-height: 1.1;
    }
    .apt-title em { font-style: italic; color: var(--brand-secondary); }
    .apt-subtitle { font-size: 1rem; color: var(--brand-muted); margin-bottom: 2rem; }

    .apt-toolbar {
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }

    .apt-search { position: relative; flex: 1; min-width: 240px; max-width: 480px; }
    .apt-search input {
        width: 100%;
        padding: 0.85rem 2.75rem 0.85rem 2.75rem;
        border: 1px solid var(--border-strong);
        border-radius: 8px;
        font-size: 0.92rem;
        font-family: inherit;
        background: white;
        transition: all 0.15s;
        color: var(--brand-primary);
    }
    .apt-search input:focus {
        outline: none;
        border-color: var(--brand-secondary);
        box-shadow: 0 0 0 3px rgba(55, 79, 167, 0.1);
    }
    .apt-search .search-icon {
        position: absolute;
        left: 0.9rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--brand-muted);
        font-size: 1rem;
        pointer-events: none;
    }
    .apt-search .search-clear {
        position: absolute;
        right: 0.6rem;
        top: 50%;
        transform: translateY(-50%);
        background: var(--surface);
        border: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        color: var(--brand-muted);
        font-size: 0.75rem;
    }
    .apt-search .search-clear:hover { background: var(--surface-alt); color: var(--brand-primary); }
    .apt-search .search-clear.visible { display: flex; }

    .apt-view-toggle {
        display: inline-flex;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 3px;
    }
    .apt-view-toggle button {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.5rem 1rem;
        background: none;
        border: none;
        border-radius: 6px;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--brand-muted);
        cursor: pointer;
        transition: all 0.15s;
        font-family: inherit;
    }
    .apt-view-toggle button:hover { color: var(--brand-primary); }
    .apt-view-toggle button.active {
        background: white;
        color: var(--brand-primary);
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    }
    .apt-view-toggle button i { font-size: 0.9rem; }

    .apt-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-bottom: 2rem;
    }
    .apt-filter {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.5rem 1rem;
        border: 1px solid var(--border-strong);
        border-radius: 9999px;
        font-size: 0.83rem;
        font-weight: 500;
        color: var(--brand-primary);
        background: white;
        text-decoration: none;
        transition: all 0.15s;
        cursor: pointer;
    }
    .apt-filter:hover {
        border-color: var(--brand-primary);
        text-decoration: none;
        color: var(--brand-primary);
    }
    .apt-filter.active {
        background: var(--brand-primary);
        border-color: var(--brand-primary);
        color: white;
    }
    .apt-filter .count { opacity: 0.6; font-size: 0.75rem; }

    .apt-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }
    .apt-card {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        text-decoration: none;
        color: inherit;
        display: flex;
        flex-direction: column;
        transition: all 0.25s;
        border: 1px solid var(--border);
    }
    .apt-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.08);
        border-color: transparent;
        color: inherit;
        text-decoration: none;
    }
    .apt-card-image {
        position: relative;
        width: 100%;
        aspect-ratio: 4/3;
        background: #f3f4f6;
        overflow: hidden;
    }
    .apt-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }
    .apt-card:hover .apt-card-image img { transform: scale(1.05); }
    .apt-card-image .placeholder-icon {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #d1d5db;
        font-size: 3rem;
    }
    .apt-card-status {
        position: absolute;
        top: 1rem;
        left: 1rem;
        padding: 0.3rem 0.75rem;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
        border-radius: 9999px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #16a34a;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    .apt-card-status::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #16a34a;
    }

    .apt-card-body {
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        flex: 1;
    }
    .apt-card-title {
        font-family: var(--font-display);
        font-size: 1.15rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: var(--brand-primary);
        margin: 0;
        line-height: 1.25;
    }
    .apt-card-location { font-size: 0.8rem; color: var(--brand-muted); margin-top: -0.25rem; }
    .apt-card-price {
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--brand-primary);
        letter-spacing: -0.02em;
        margin-top: 0.25rem;
    }
    .apt-card-price span {
        font-size: 0.8rem;
        font-weight: 500;
        color: var(--brand-muted);
        margin-left: 0.15rem;
    }
    .apt-card-meta {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        font-size: 0.82rem;
        color: var(--brand-muted);
        padding: 0.7rem 0;
        border-top: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
    }
    .apt-card-meta-item { display: flex; align-items: center; gap: 0.35rem; }
    .apt-card-meta-item i { font-size: 0.9rem; }
    .apt-card-tags { display: flex; flex-wrap: wrap; gap: 0.3rem; margin-top: 0.35rem; }
    .apt-card-tag {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        background: var(--surface);
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 500;
        color: var(--brand-primary);
    }

    .apt-map-section { display: none; margin-bottom: 3rem; }
    .apt-map-section.visible { display: block; }
    .apt-map-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr;
        gap: 1.5rem;
        min-height: 600px;
    }
    .apt-map-wrapper {
        background: white;
        border-radius: 10px;
        overflow: hidden;
        border: 1px solid var(--border);
        position: relative;
    }
    #apartmentMap { width: 100%; height: 100%; min-height: 600px; }
    .apt-map-list {
        background: white;
        border-radius: 10px;
        border: 1px solid var(--border);
        display: flex;
        flex-direction: column;
        max-height: 600px;
        overflow: hidden;
    }
    .apt-map-list-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
        background: var(--surface);
    }
    .apt-map-list-header label {
        font-size: 0.72rem;
        font-weight: 700;
        color: var(--brand-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: block;
        margin-bottom: 0.5rem;
    }
    #mapAddressSelect {
        width: 100%;
        padding: 0.6rem 2.5rem 0.6rem 0.85rem;
        border: 1px solid var(--border-strong);
        border-radius: 6px;
        font-size: 0.85rem;
        background: white;
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        font-family: inherit;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%236b7280'%3e%3cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.85rem center;
        background-size: 12px 12px;
    }
    #mapAddressSelect:focus {
        outline: none;
        border-color: var(--brand-secondary);
        box-shadow: 0 0 0 3px rgba(55, 79, 167, 0.1);
    }
    .apt-map-list-items { flex: 1; overflow-y: auto; padding: 0.5rem; }
    .apt-map-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.75rem;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.15s;
        border: 1px solid transparent;
    }
    .apt-map-item:hover { background: var(--surface); border-color: var(--border); }
    .apt-map-item.active { background: #f5f7fb; border-color: var(--brand-secondary); }
    .apt-map-item-img {
        width: 72px;
        height: 72px;
        flex-shrink: 0;
        border-radius: 6px;
        overflow: hidden;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .apt-map-item-img img { width: 100%; height: 100%; object-fit: cover; }
    .apt-map-item-img i { color: #d1d5db; font-size: 1.5rem; }
    .apt-map-item-body { flex: 1; min-width: 0; display: flex; flex-direction: column; justify-content: center; }
    .apt-map-item-title {
        font-weight: 700;
        font-size: 0.88rem;
        color: var(--brand-primary);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0.15rem;
    }
    .apt-map-item-addr {
        font-size: 0.78rem;
        color: var(--brand-muted);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: 0.35rem;
    }
    .apt-map-item-price {
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--brand-secondary);
    }
    .apt-map-empty {
        padding: 2rem 1rem;
        text-align: center;
        color: var(--brand-muted);
        font-size: 0.85rem;
    }

    .empty-apartments {
        text-align: center;
        padding: 5rem 2rem;
        color: var(--brand-muted);
    }
    .empty-apartments i { font-size: 3rem; color: #d1d5db; display: block; margin-bottom: 1rem; }
    .empty-apartments h3 {
        font-family: var(--font-display);
        font-size: 1.2rem;
        color: var(--brand-primary);
        margin-bottom: 0.5rem;
    }

    .apt-pagination { display: flex; justify-content: center; padding: 2rem 0; }

    .custom-map-marker {
        background: var(--brand-secondary);
        width: 32px;
        height: 32px;
        border-radius: 50% 50% 50% 0;
        transform: rotate(-45deg);
        border: 3px solid white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .custom-map-marker::after {
        content: '';
        width: 10px;
        height: 10px;
        background: white;
        border-radius: 50%;
        transform: rotate(45deg);
    }

    @media (max-width: 900px) {
        .apt-map-grid { grid-template-columns: 1fr; }
        #apartmentMap { min-height: 400px; }
        .apt-map-list { max-height: 500px; }
    }
    @media (max-width: 767px) {
        .apt-title { font-size: 1.85rem; }
        .apt-grid { grid-template-columns: 1fr; gap: 1rem; }
        .apt-hero { padding: 2rem 0 1.5rem; }
        .apt-search input { font-size: 0.88rem; padding: 0.7rem 1rem 0.7rem 2.5rem; }
    }
</style>
@endpush

@section('content')

<div class="apt-hero">
    <h1 class="apt-title">Our <em>apartments</em></h1>
    <p class="apt-subtitle">Browse our available rentals in Boston and beyond.</p>

    <div class="apt-toolbar">
        <form method="GET" action="{{ route('apartments.index') }}" class="apt-search" id="aptSearchForm">
            <i class="bi bi-search search-icon"></i>
            <input type="text" name="search" id="aptSearchInput"
                   value="{{ request('search') }}"
                   placeholder="Search by address, city, or ZIP..."
                   autocomplete="off">
            <button type="button" class="search-clear {{ request('search') ? 'visible' : '' }}" id="aptSearchClear" aria-label="Clear search">
                <i class="bi bi-x-lg"></i>
            </button>
            @if(request('category'))
                <input type="hidden" name="category" value="{{ request('category') }}">
            @endif
        </form>

        <div class="apt-view-toggle">
            <button type="button" class="active" id="viewGridBtn" onclick="switchView('grid')">
                <i class="bi bi-grid-3x3-gap"></i> Grid
            </button>
            <button type="button" id="viewMapBtn" onclick="switchView('map')">
                <i class="bi bi-map"></i> Map
            </button>
        </div>
    </div>

    @if($categories->isNotEmpty())
        <div class="apt-filters">
            <a href="{{ route('apartments.index', ['search' => request('search')]) }}"
               class="apt-filter {{ !request('category') ? 'active' : '' }}">
                All
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('apartments.index', ['category' => $cat->id, 'search' => request('search')]) }}"
                   class="apt-filter {{ request('category') == $cat->id ? 'active' : '' }}">
                    {{ $cat->name }}
                    <span class="count">({{ $cat->properties_count }})</span>
                </a>
            @endforeach
        </div>
    @endif
</div>

@if($apartments->isEmpty())
    <div class="empty-apartments">
        <i class="bi bi-house-slash"></i>
        <h3>No apartments found</h3>
        <p>Try adjusting your search or filters.</p>
    </div>
@else

    <div id="gridView">
        <div class="apt-grid">
            @foreach($apartments as $apartment)
                <a href="{{ route('apartments.show', $apartment->slug) }}" class="apt-card">
                    <div class="apt-card-image">
                        @if($apartment->primaryImage)
                            <img src="{{ $apartment->primaryImage->image_url }}"
                                 alt="{{ $apartment->address }}" loading="lazy">
                        @else
                            <div class="placeholder-icon"><i class="bi bi-image"></i></div>
                        @endif
                        @if($apartment->status === 'active')
                            <span class="apt-card-status">Available</span>
                        @endif
                    </div>
                    <div class="apt-card-body">
                        <h3 class="apt-card-title">
                            {{ $apartment->address }}
                            @if($apartment->unit_type) {{ $apartment->unit_type }} @endif
                        </h3>
                        @if($apartment->city)
                            <p class="apt-card-location">{{ $apartment->city }}@if($apartment->state), {{ $apartment->state }}@endif</p>
                        @endif

                        @if($apartment->rent)
                            <div class="apt-card-price">
                                ${{ number_format($apartment->rent, 0) }}<span>/mo</span>
                            </div>
                        @else
                            <div class="apt-card-price" style="font-size:1rem;color:var(--brand-muted);">Contact for pricing</div>
                        @endif

                        <div class="apt-card-meta">
                            @if($apartment->category)
                                <div class="apt-card-meta-item">
                                    <i class="bi bi-door-closed"></i>
                                    <span>{{ $apartment->category->name }}</span>
                                </div>
                            @endif
                            @if($apartment->bathrooms)
                                <div class="apt-card-meta-item">
                                    <i class="bi bi-droplet"></i>
                                    <span>{{ $apartment->bathrooms }} ba</span>
                                </div>
                            @endif
                            @if($apartment->available_date)
                                <div class="apt-card-meta-item">
                                    <i class="bi bi-calendar-check"></i>
                                    <span>Available {{ $apartment->available_date->format('m/d') }}</span>
                                </div>
                            @endif
                        </div>

                        @if($apartment->amenities->isNotEmpty())
                            <div class="apt-card-tags">
                                @foreach($apartment->amenities->take(4) as $am)
                                    <span class="apt-card-tag">{{ $am->name }}</span>
                                @endforeach
                                @if($apartment->amenities->count() > 4)
                                    <span class="apt-card-tag">+{{ $apartment->amenities->count() - 4 }}</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <div class="apt-pagination">
            {{ $apartments->links() }}
        </div>
    </div>

    <div id="mapView" class="apt-map-section">
        <div class="apt-map-grid">
            <div class="apt-map-wrapper">
                <div id="apartmentMap"></div>
            </div>
            <div class="apt-map-list">
                <div class="apt-map-list-header">
                    <label for="mapAddressSelect">Jump to a property</label>
                    <select id="mapAddressSelect">
                        <option value="">— Select a property —</option>
                        @foreach($apartments as $apartment)
                            @if($apartment->latitude && $apartment->longitude)
                                <option value="{{ $apartment->id }}">
                                    {{ $apartment->address }}{{ $apartment->city ? ', ' . $apartment->city : '' }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="apt-map-list-items">
                    @foreach($apartments as $apartment)
                        @if($apartment->latitude && $apartment->longitude)
                            <div class="apt-map-item"
                                 data-map-id="{{ $apartment->id }}"
                                 data-lat="{{ $apartment->latitude }}"
                                 data-lng="{{ $apartment->longitude }}"
                                 data-url="{{ route('apartments.show', $apartment->slug) }}">
                                <div class="apt-map-item-img">
                                    @if($apartment->primaryImage)
                                        <img src="{{ $apartment->primaryImage->image_url }}" alt="">
                                    @else
                                        <i class="bi bi-house"></i>
                                    @endif
                                </div>
                                <div class="apt-map-item-body">
                                    <div class="apt-map-item-title">
                                        {{ $apartment->address }}
                                        @if($apartment->unit_type) {{ $apartment->unit_type }} @endif
                                    </div>
                                    <div class="apt-map-item-addr">{{ $apartment->city }}@if($apartment->state), {{ $apartment->state }}@endif</div>
                                    @if($apartment->rent)
                                        <div class="apt-map-item-price">${{ number_format($apartment->rent, 0) }}/mo</div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>

@endif

@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
(function () {
    const searchInput = document.getElementById('aptSearchInput');
    const clearBtn = document.getElementById('aptSearchClear');
    const form = document.getElementById('aptSearchForm');
    if (searchInput && clearBtn && form) {
        function toggleClear() {
            clearBtn.classList.toggle('visible', searchInput.value.length > 0);
        }
        searchInput.addEventListener('input', toggleClear);
        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            toggleClear();
            searchInput.focus();
        });
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                form.submit();
            }
        });
        toggleClear();
    }

    let mapInitialized = false;
    let map;
    const markers = {};

    window.switchView = function (view) {
        const gridBtn = document.getElementById('viewGridBtn');
        const mapBtn = document.getElementById('viewMapBtn');
        const gridView = document.getElementById('gridView');
        const mapView = document.getElementById('mapView');

        if (!gridView || !mapView) return;

        if (view === 'map') {
            gridBtn.classList.remove('active');
            mapBtn.classList.add('active');
            gridView.style.display = 'none';
            mapView.classList.add('visible');

            if (!mapInitialized) {
                initMap();
                mapInitialized = true;
            } else if (map) {
                setTimeout(() => map.invalidateSize(), 100);
            }
        } else {
            mapBtn.classList.remove('active');
            gridBtn.classList.add('active');
            gridView.style.display = '';
            mapView.classList.remove('visible');
        }
    };

    function initMap() {
        const items = document.querySelectorAll('[data-map-id]');
        if (!items.length) return;

        const BOSTON = [42.3601, -71.0589];
        let center = BOSTON;
        const coords = [];

        items.forEach(function (el) {
            const lat = parseFloat(el.dataset.lat);
            const lng = parseFloat(el.dataset.lng);
            if (!isNaN(lat) && !isNaN(lng)) {
                coords.push([lat, lng]);
            }
        });

        if (coords.length > 0) {
            center = [
                coords.reduce((s, c) => s + c[0], 0) / coords.length,
                coords.reduce((s, c) => s + c[1], 0) / coords.length,
            ];
        }

        map = L.map('apartmentMap').setView(center, 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19,
        }).addTo(map);

        const icon = L.divIcon({
            className: 'custom-map-icon',
            html: '<div class="custom-map-marker"></div>',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
        });

        items.forEach(function (el) {
            const lat = parseFloat(el.dataset.lat);
            const lng = parseFloat(el.dataset.lng);
            const id = el.dataset.mapId;
            const url = el.dataset.url;

            if (isNaN(lat) || isNaN(lng)) return;

            const title = el.querySelector('.apt-map-item-title')?.textContent.trim() || '';
            const price = el.querySelector('.apt-map-item-price')?.textContent.trim() || '';

            const marker = L.marker([lat, lng], { icon }).addTo(map);
            marker.bindPopup(
                '<div style="font-family:inherit;min-width:180px;">' +
                '<strong style="font-size:0.9rem;">' + title + '</strong><br>' +
                (price ? '<span style="color:#374fa7;font-weight:700;">' + price + '</span><br>' : '') +
                '<a href="' + url + '" style="margin-top:0.4rem;display:inline-block;background:#374fa7;color:white;padding:0.35rem 0.85rem;border-radius:4px;font-size:0.8rem;font-weight:600;text-decoration:none;">View details</a>' +
                '</div>'
            );

            marker.on('click', function () {
                highlightItem(id);
            });

            markers[id] = marker;
        });

        if (coords.length > 1) {
            const bounds = L.latLngBounds(coords);
            map.fitBounds(bounds, { padding: [40, 40], maxZoom: 14 });
        }

        items.forEach(function (el) {
            el.addEventListener('click', function () {
                const id = el.dataset.mapId;
                const lat = parseFloat(el.dataset.lat);
                const lng = parseFloat(el.dataset.lng);
                if (!isNaN(lat) && !isNaN(lng)) {
                    map.flyTo([lat, lng], 16, { duration: 0.7 });
                    if (markers[id]) markers[id].openPopup();
                }
                highlightItem(id);
            });
        });

        const select = document.getElementById('mapAddressSelect');
        if (select) {
            select.addEventListener('change', function () {
                const id = this.value;
                if (!id) return;
                const el = document.querySelector('[data-map-id="' + id + '"]');
                if (!el) return;
                const lat = parseFloat(el.dataset.lat);
                const lng = parseFloat(el.dataset.lng);
                if (!isNaN(lat) && !isNaN(lng)) {
                    map.flyTo([lat, lng], 16, { duration: 0.7 });
                    if (markers[id]) markers[id].openPopup();
                }
                highlightItem(id);
            });
        }
    }

    function highlightItem(id) {
        document.querySelectorAll('[data-map-id]').forEach(function (el) {
            el.classList.toggle('active', el.dataset.mapId === id);
        });
    }
})();
</script>
@endpush