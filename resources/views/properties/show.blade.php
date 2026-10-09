@extends('layouts.app')

@section('title', $property->category?->name . ' — ' . $property->address . ', ' . $property->city)

@push('styles')
<style>
    /* ═══════════════════════════════════════════════════════
       PUBLIC PROPERTY SHOW
       ═══════════════════════════════════════════════════════ */
    .pub-back {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.85rem;
        color: var(--brand-muted);
        text-decoration: none;
        margin-bottom: 1rem;
        transition: color 0.2s;
    }
    .pub-back:hover { color: var(--brand-secondary); }

    .pub-header { margin-bottom: 1.5rem; }

    .pub-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--brand-primary);
        line-height: 1.25;
        margin-bottom: 0.35rem;
    }

    .pub-subtitle {
        font-size: 0.95rem;
        color: var(--brand-muted);
    }

    /* Galería */
    .pub-gallery {
        display: grid;
        grid-template-columns: 2fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 0.6rem;
        border-radius: 1rem;
        overflow: hidden;
        margin-bottom: 2rem;
        height: 480px;
    }

    .pub-gallery-item {
        position: relative;
        overflow: hidden;
        background: #f3f4f6;
        cursor: pointer;
        border: none;
        padding: 0;
    }

    .pub-gallery-item:first-child {
        grid-row: 1 / 3;
        grid-column: 1 / 2;
    }

    .pub-gallery-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.4s ease;
    }

    .pub-gallery-item:hover img { transform: scale(1.04); }

    .pub-gallery-more {
        position: absolute;
        inset: 0;
        background: rgba(27,27,24,0.65);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 0.25rem;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .pub-gallery-more i { font-size: 1.5rem; }

    .pub-gallery-empty {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        background: #f3f4f6;
        color: #d1d5db;
        font-size: 4rem;
        border-radius: 1rem;
        grid-column: 1 / -1;
        grid-row: 1 / -1;
    }

    /* Layout de 2 columnas */
    .pub-layout {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: 2rem;
        align-items: start;
    }

    /* Bloque principal */
    .pub-block {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 1rem;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
    }

    .pub-block-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--brand-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.55rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .pub-block-title i {
        color: var(--brand-secondary);
    }

    /* Price hero */
    .pub-price-hero {
        display: flex;
        align-items: baseline;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 0.5rem;
    }

    .pub-price-main {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--brand-secondary);
        letter-spacing: -0.02em;
    }

    .pub-price-period {
        font-size: 1rem;
        color: var(--brand-muted);
        font-weight: 500;
    }

    .pub-specs-row {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        padding-top: 0.75rem;
        border-top: 1px solid #f3f4f6;
        margin-top: 0.75rem;
    }

    .pub-spec {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.9rem;
        color: var(--brand-primary);
        font-weight: 600;
    }

    .pub-spec i {
        color: var(--brand-muted);
        font-size: 1rem;
    }

    /* Info list (dl vertical) */
    .pub-info-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        font-size: 0.92rem;
    }

    .pub-info-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 1rem;
        padding-bottom: 0.6rem;
        border-bottom: 1px dashed #f3f4f6;
    }

    .pub-info-row:last-child { border-bottom: none; padding-bottom: 0; }

    .pub-info-label {
        color: var(--brand-muted);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        flex-shrink: 0;
    }

    .pub-info-label i { font-size: 0.95rem; }

    .pub-info-value {
        font-weight: 700;
        color: var(--brand-primary);
        text-align: right;
    }

    /* Chips de amenidades / utilities */
    .pub-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .pub-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.45rem 0.85rem;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 9999px;
        font-size: 0.83rem;
        font-weight: 600;
        color: var(--brand-primary);
    }

    .pub-chip i { color: var(--brand-secondary); font-size: 0.9rem; }

    .pub-chip-pet {
        background: #fff5f2;
        border-color: #fbd8ce;
        color: var(--brand-secondary);
    }

    /* Lista de bullets (requirements) */
    .pub-bullet-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    .pub-bullet-list li {
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
        font-size: 0.9rem;
        color: var(--brand-primary);
        line-height: 1.5;
    }

    .pub-bullet-list li i {
        color: var(--brand-secondary);
        font-size: 1rem;
        flex-shrink: 0;
        margin-top: 0.15rem;
    }

    /* Sidebar sticky */
    .pub-sidebar {
        position: sticky;
        top: 90px;
    }

    .pub-sidebar .pub-block { padding: 1.35rem; }

    /* CTA buttons */
    .pub-cta {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.85rem 1.25rem;
        border-radius: 0.6rem;
        font-weight: 700;
        font-size: 0.95rem;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-bottom: 0.6rem;
        font-family: inherit;
    }

    .pub-cta-primary {
        background: var(--brand-secondary);
        color: white;
    }
    .pub-cta-primary:hover {
        background: #d42b02;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245, 48, 3, 0.3);
    }

    .pub-cta-outline {
        background: white;
        color: var(--brand-primary);
        border: 1.5px solid #d1d5db;
    }
    .pub-cta-outline:hover {
        border-color: var(--brand-primary);
        color: var(--brand-primary);
        text-decoration: none;
    }

    /* Video tour embed */
    .pub-video-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0.75rem;
    }

    .pub-video-grid video {
        width: 100%;
        border-radius: 0.6rem;
        background: #000;
        aspect-ratio: 16/9;
        object-fit: cover;
    }

    /* Disclaimer al fondo */
    .pub-disclaimer {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.15rem 1.35rem;
        font-size: 0.78rem;
        color: var(--brand-muted);
        line-height: 1.6;
        margin-top: 1.5rem;
    }

    .pub-disclaimer strong {
        color: var(--brand-primary);
        display: block;
        margin-bottom: 0.35rem;
        font-size: 0.82rem;
    }

    /* Lightbox */
    .pub-lightbox {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.92);
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .pub-lightbox.open { display: flex; }

    .pub-lightbox img {
        max-width: 92vw;
        max-height: 88vh;
        object-fit: contain;
        border-radius: 0.5rem;
    }

    .pub-lightbox-close,
    .pub-lightbox-nav {
        position: absolute;
        background: rgba(255,255,255,0.15);
        color: white;
        border: none;
        border-radius: 50%;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.4rem;
        transition: background 0.2s;
    }

    .pub-lightbox-close:hover,
    .pub-lightbox-nav:hover { background: rgba(255,255,255,0.3); }

    .pub-lightbox-close { top: 1rem; right: 1rem; }
    .pub-lightbox-nav.prev { left: 1rem; top: 50%; transform: translateY(-50%); }
    .pub-lightbox-nav.next { right: 1rem; top: 50%; transform: translateY(-50%); }

    /* Responsive */
    @media (max-width: 1024px) {
        .pub-layout { grid-template-columns: 1fr; }
        .pub-sidebar { position: static; }
        .pub-gallery { height: 360px; }
    }

    @media (max-width: 640px) {
        .pub-title { font-size: 1.35rem; }
        .pub-price-main { font-size: 1.75rem; }
        .pub-gallery {
            grid-template-columns: 1fr;
            grid-template-rows: 1fr;
            height: 260px;
        }
        .pub-gallery-item:first-child { grid-row: 1; grid-column: 1; }
        .pub-gallery-item:not(:first-child) { display: none; }
        .pub-gallery-more { display: flex; }
        .pub-block { padding: 1.15rem; }
    }
</style>
@endpush

@section('content')

{{-- Back link --}}
<a href="{{ route('public.properties.index') }}" class="pub-back">
    <i class="bi bi-arrow-left"></i> Back to listings
</a>

{{-- Header --}}
<div class="pub-header">
    <h1 class="pub-title">{{ $property->category?->name ?? 'Property' }}</h1>
    <p class="pub-subtitle">
        <i class="bi bi-geo-alt"></i>
        {{ $property->address }}@if($property->city), {{ $property->city }}@endif@if($property->state), {{ $property->state }}@endif
        {{ $property->zip_code ? ' ' . $property->zip_code : '' }}
    </p>
</div>

{{-- ═══════════════ GALLERY ═══════════════ --}}
@if($property->images->isNotEmpty())
    @php
        $galleryImages = $property->images->pluck('image_url')->values();
        $totalImages   = $galleryImages->count();
    @endphp
    <div class="pub-gallery">
        @foreach($galleryImages->take(3) as $i => $url)
            <button type="button"
                    class="pub-gallery-item"
                    data-index="{{ $i }}"
                    onclick="openLightbox({{ $i }})">
                <img src="{{ $url }}" alt="{{ $property->address }} — image {{ $i + 1 }}">
                @if($i === 2 && $totalImages > 3)
                    <div class="pub-gallery-more">
                        <i class="bi bi-images"></i>
                        +{{ $totalImages - 3 }} more
                    </div>
                @endif
            </button>
        @endforeach
    </div>
@else
    <div class="pub-gallery" style="height:260px;grid-template-columns:1fr;grid-template-rows:1fr;">
        <div class="pub-gallery-empty"><i class="bi bi-image"></i></div>
    </div>
@endif

{{-- Lightbox --}}
@if($property->images->isNotEmpty())
<div class="pub-lightbox" id="pubLightbox" onclick="if(event.target===this) closeLightbox()">
    <button type="button" class="pub-lightbox-close" onclick="closeLightbox()">&times;</button>
    <button type="button" class="pub-lightbox-nav prev" onclick="navLightbox(-1)">‹</button>
    <img id="pubLightboxImg" src="" alt="">
    <button type="button" class="pub-lightbox-nav next" onclick="navLightbox(1)">›</button>
</div>
@endif

{{-- ═══════════════ LAYOUT ═══════════════ --}}
<div class="pub-layout">

    {{-- ─────────── LEFT COLUMN ─────────── --}}
    <div>

        {{-- Key specs --}}
        <div class="pub-block">
            <div class="pub-price-hero">
                @if($property->rent)
                    <span class="pub-price-main">{{ $property->formatted_rent }}</span>
                    <span class="pub-price-period">/ month</span>
                @else
                    <span class="pub-price-main">Contact for pricing</span>
                @endif
            </div>

            <div class="pub-specs-row">
                @if($property->category)
                    <div class="pub-spec">
                        <i class="bi bi-door-closed"></i>
                        {{ $property->category->name }}
                    </div>
                @endif
                @if($property->bathrooms)
                    <div class="pub-spec">
                        <i class="bi bi-droplet"></i>
                        {{ $property->bathrooms }} ba
                    </div>
                @endif
                @if($property->square_feet)
                    <div class="pub-spec">
                        <i class="bi bi-rulers"></i>
                        {{ number_format($property->square_feet) }} sqft
                    </div>
                @endif
                @if($property->unit_type)
                    <div class="pub-spec">
                        <i class="bi bi-house"></i>
                        {{ $property->unit_type }}
                    </div>
                @endif
            </div>

            @if($property->available_date)
                <div style="margin-top:1rem;padding-top:0.85rem;border-top:1px solid #f3f4f6;display:flex;align-items:center;gap:0.5rem;font-size:0.9rem;">
                    <i class="bi bi-calendar-check" style="color:var(--brand-secondary);font-size:1.1rem;"></i>
                    <span style="font-weight:700;color:var(--brand-primary);">
                        Available {{ $property->available_date->format('m/d/Y') }}
                    </span>
                </div>
            @endif
        </div>

        {{-- Description --}}
        @if($property->description)
            <div class="pub-block">
                <h2 class="pub-block-title"><i class="bi bi-text-paragraph"></i> About this property</h2>
                <p style="font-size:0.95rem;color:var(--brand-primary);line-height:1.7;white-space:pre-line;">{{ $property->description }}</p>
            </div>
        @endif

        {{-- Lease & Costs --}}
        @if($property->leaseTerm || $property->deposit || $property->rent)
            <div class="pub-block">
                <h2 class="pub-block-title"><i class="bi bi-cash-stack"></i> Lease & Costs</h2>

                <div class="pub-info-list">
                    @if($property->leaseTerm)
                        <div class="pub-info-row">
                            <span class="pub-info-label"><i class="bi bi-file-earmark-text"></i> Lease term</span>
                            <span class="pub-info-value">{{ $property->leaseTerm->name }}</span>
                        </div>
                    @endif

                    @if($property->rent)
                        <div class="pub-info-row">
                            <span class="pub-info-label"><i class="bi bi-tag"></i> Monthly rent</span>
                            <span class="pub-info-value">{{ $property->formatted_rent }}</span>
                        </div>
                    @endif

                    @if($property->deposit)
                        <div class="pub-info-row">
                            <span class="pub-info-label"><i class="bi bi-shield-check"></i> Security deposit</span>
                            <span class="pub-info-value">{{ $property->formatted_deposit }}</span>
                        </div>
                    @endif

                    <div class="pub-info-row">
                        <span class="pub-info-label"><i class="bi bi-info-circle"></i> First month rent</span>
                        <span class="pub-info-value">{{ $property->formatted_rent ?? '—' }} due at signing</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Features --}}
        @php
            $featureRows = [];
            if ($property->parkingType) {
                $featureRows[] = ['icon' => 'bi-car-front', 'label' => 'Parking', 'value' => $property->parkingType->name . ($property->parking_spot ? ' — Spot ' . $property->parking_spot : '')];
            }
            if ($property->coolingSystem) {
                $featureRows[] = ['icon' => 'bi-snow', 'label' => 'Cooling', 'value' => $property->coolingSystem->name];
            }
            if ($property->heatingSystem) {
                $featureRows[] = ['icon' => 'bi-thermometer-half', 'label' => 'Heating', 'value' => $property->heatingSystem->name];
            }
            if ($property->laundry) {
                $featureRows[] = ['icon' => 'bi-basket', 'label' => 'Laundry', 'value' => $property->laundry->name];
            }
        @endphp

        @if(!empty($featureRows) || $property->amenities->isNotEmpty())
            <div class="pub-block">
                <h2 class="pub-block-title"><i class="bi bi-stars"></i> Features & Amenities</h2>

                @if(!empty($featureRows))
                    <div class="pub-info-list" style="margin-bottom:1.25rem;">
                        @foreach($featureRows as $row)
                            <div class="pub-info-row">
                                <span class="pub-info-label">
                                    <i class="bi {{ $row['icon'] }}"></i> {{ $row['label'] }}
                                </span>
                                <span class="pub-info-value">{{ $row['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if($property->amenities->isNotEmpty())
                    <div class="pub-chips">
                        @foreach($property->amenities as $am)
                            <span class="pub-chip">
                                @if($am->icon)<i class="bi {{ $am->icon }}"></i>@endif
                                {{ $am->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        {{-- Utilities --}}
        @if($property->utilities->isNotEmpty())
            <div class="pub-block">
                <h2 class="pub-block-title"><i class="bi bi-plug"></i> Utilities included</h2>
                <div class="pub-chips">
                    @foreach($property->utilities as $util)
                        <span class="pub-chip">
                            @if($util->icon)<i class="bi {{ $util->icon }}"></i>@endif
                            {{ $util->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Pet Policy --}}
        @if($property->pet_friendly || $property->allow_cats || $property->allow_small_dogs || $property->allow_large_dogs)
            <div class="pub-block">
                <h2 class="pub-block-title"><i class="bi bi-heart"></i> Pet policy</h2>

                @if(!$property->pet_friendly)
                    <p style="font-size:0.92rem;color:var(--brand-muted);margin:0;">This property is not listed as pet-friendly at this time.</p>
                @else
                    <div class="pub-chips" style="margin-bottom:0.85rem;">
                        @if($property->allow_cats)
                            <span class="pub-chip pub-chip-pet"><i class="bi bi-check-circle-fill"></i> Cats welcome</span>
                        @endif
                        @if($property->allow_small_dogs)
                            <span class="pub-chip pub-chip-pet">
                                <i class="bi bi-check-circle-fill"></i>
                                Small dogs welcome
                                @if($property->small_dog_max_weight)
                                    (up to {{ $property->small_dog_max_weight }} lbs)
                                @endif
                            </span>
                        @endif
                        @if($property->allow_large_dogs)
                            <span class="pub-chip pub-chip-pet"><i class="bi bi-check-circle-fill"></i> Large dogs welcome</span>
                        @endif
                    </div>

                    @if($property->max_pets)
                        <p style="font-size:0.88rem;color:var(--brand-muted);margin:0;">
                            <i class="bi bi-info-circle"></i>
                            Up to <strong>{{ $property->max_pets }}</strong> {{ \Illuminate\Support\Str::plural('pet', $property->max_pets) }} allowed per unit.
                        </p>
                    @endif
                @endif
            </div>
        @endif

        {{-- Screening criteria (friendly) --}}
        @if($property->income_to_rent_ratio || $property->allow_eviction_older_than_5_years)
            <div class="pub-block">
                <h2 class="pub-block-title"><i class="bi bi-clipboard-check"></i> What you'll need to qualify</h2>

                <ul class="pub-bullet-list">
                    @if($property->income_to_rent_ratio)
                        <li>
                            <i class="bi bi-check-circle"></i>
                            <span>
                                Combined gross monthly income of at least
                                <strong>{{ $property->income_to_rent_ratio }}× the monthly rent</strong>.
                            </span>
                        </li>
                    @endif

                    @if($property->allow_eviction_older_than_5_years)
                        <li>
                            <i class="bi bi-check-circle"></i>
                            <span>
                                Past evictions are considered on a case-by-case basis if they occurred
                                <strong>more than 5 years ago</strong>.
                            </span>
                        </li>
                    @else
                        <li>
                            <i class="bi bi-check-circle"></i>
                            <span>
                                Recent evictions (within the last 5 years) may affect your application.
                            </span>
                        </li>
                    @endif

                    <li>
                        <i class="bi bi-check-circle"></i>
                        <span>
                            Standard credit and background screening applies to all applicants equally.
                            An application fee may apply.
                        </span>
                    </li>

                    <li>
                        <i class="bi bi-check-circle"></i>
                        <span>
                            All criteria are applied consistently to every applicant, in compliance with
                            fair housing laws.
                        </span>
                    </li>
                </ul>
            </div>
        @endif

        {{-- Restrictions / house rules --}}
        @if($property->restrictions)
            <div class="pub-block">
                <h2 class="pub-block-title"><i class="bi bi-exclamation-circle"></i> House rules</h2>
                <p style="font-size:0.92rem;color:var(--brand-primary);line-height:1.7;white-space:pre-line;margin:0;">{{ $property->restrictions }}</p>
            </div>
        @endif

        {{-- Videos tour --}}
        @if($property->videos->isNotEmpty())
            <div class="pub-block">
                <h2 class="pub-block-title"><i class="bi bi-camera-video"></i> Video tour</h2>
                <div class="pub-video-grid">
                    @foreach($property->videos as $vid)
                        <video src="{{ $vid->video_url }}" controls preload="metadata"></video>
                    @endforeach
                </div>
            </div>
        @endif

    </div>

    {{-- ─────────── SIDEBAR ─────────── --}}
    <aside class="pub-sidebar">

        {{-- CTA block --}}
        <div class="pub-block">
            <div style="text-align:center;padding-bottom:1rem;margin-bottom:1rem;border-bottom:1px solid #f3f4f6;">
                @if($property->rent)
                    <div style="font-size:2rem;font-weight:800;color:var(--brand-secondary);line-height:1;">
                        {{ $property->formatted_rent }}
                    </div>
                    <div style="font-size:0.85rem;color:var(--brand-muted);margin-top:0.25rem;">per month</div>
                @else
                    <div style="font-size:1.3rem;font-weight:800;color:var(--brand-primary);">
                        Contact for pricing
                    </div>
                @endif
            </div>

            <a href="#contact" class="pub-cta pub-cta-primary">
                <i class="bi bi-calendar-event"></i> Schedule a showing
            </a>

            @if($property->virtual_tour_url)
                <a href="{{ $property->virtual_tour_url }}"
                   target="_blank"
                   rel="noopener"
                   class="pub-cta pub-cta-outline">
                    <i class="bi bi-box-arrow-up-right"></i> Virtual tour
                </a>
            @endif
        </div>

        {{-- Quick facts --}}
        <div class="pub-block">
            <h3 class="pub-block-title"><i class="bi bi-list-check"></i> Quick facts</h3>
            <div class="pub-info-list">
                @if($property->available_date)
                    <div class="pub-info-row">
                        <span class="pub-info-label"><i class="bi bi-calendar"></i> Available</span>
                        <span class="pub-info-value">{{ $property->available_date->format('m/d/Y') }}</span>
                    </div>
                @endif

                @if($property->leaseTerm)
                    <div class="pub-info-row">
                        <span class="pub-info-label"><i class="bi bi-file-text"></i> Lease</span>
                        <span class="pub-info-value">{{ $property->leaseTerm->name }}</span>
                    </div>
                @endif

                @if($property->deposit)
                    <div class="pub-info-row">
                        <span class="pub-info-label"><i class="bi bi-shield-check"></i> Deposit</span>
                        <span class="pub-info-value">{{ $property->formatted_deposit }}</span>
                    </div>
                @endif

                @if($property->square_feet)
                    <div class="pub-info-row">
                        <span class="pub-info-label"><i class="bi bi-rulers"></i> Size</span>
                        <span class="pub-info-value">{{ number_format($property->square_feet) }} sqft</span>
                    </div>
                @endif

                @if($property->bathrooms)
                    <div class="pub-info-row">
                        <span class="pub-info-label"><i class="bi bi-droplet"></i> Bathrooms</span>
                        <span class="pub-info-value">{{ $property->bathrooms }}</span>
                    </div>
                @endif

                @if($property->unit_type)
                    <div class="pub-info-row">
                        <span class="pub-info-label"><i class="bi bi-house"></i> Unit</span>
                        <span class="pub-info-value">{{ $property->unit_type }}</span>
                    </div>
                @endif

                @if($property->parkingType)
                    <div class="pub-info-row">
                        <span class="pub-info-label"><i class="bi bi-car-front"></i> Parking</span>
                        <span class="pub-info-value">{{ $property->parkingType->name }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Contact block (placeholder — se conectará con el scheduler después) --}}
        <div class="pub-block" id="contact">
            <h3 class="pub-block-title"><i class="bi bi-chat-dots"></i> Get in touch</h3>
            <p style="font-size:0.88rem;color:var(--brand-muted);line-height:1.6;margin:0;">
                Interested in this property? Reach out to schedule a tour or ask any questions.
                Our team responds within 24 hours.
            </p>
            <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid #f3f4f6;display:flex;flex-direction:column;gap:0.5rem;font-size:0.88rem;">
                <div style="display:flex;align-items:center;gap:0.5rem;">
                    <i class="bi bi-envelope" style="color:var(--brand-secondary);"></i>
                    <span style="color:var(--brand-primary);">Contact form coming soon</span>
                </div>
            </div>
        </div>

    </aside>

</div>

{{-- ─────────── DISCLAIMER ─────────── --}}
<div class="pub-disclaimer">
    <strong><i class="bi bi-info-circle"></i> Fair Housing Notice</strong>
    We are committed to compliance with all federal, state, and local fair housing laws.
    We do not discriminate on the basis of race, color, religion, sex, gender identity,
    sexual orientation, national origin, familial status, disability, or any other
    protected characteristic. All application criteria are applied uniformly to every
    applicant. Pricing and availability are subject to change without notice.
</div>

@endsection

@push('scripts')
@if($property->images->isNotEmpty())
<script>
(function () {
    const images   = @json($property->images->pluck('image_url')->values());
    const lightbox = document.getElementById('pubLightbox');
    const imgEl    = document.getElementById('pubLightboxImg');
    let current    = 0;

    window.openLightbox = function (index) {
        current = index;
        imgEl.src = images[current];
        lightbox.classList.add('open');
        document.body.style.overflow = 'hidden';
    };

    window.closeLightbox = function () {
        lightbox.classList.remove('open');
        document.body.style.overflow = '';
    };

    window.navLightbox = function (dir) {
        current = (current + dir + images.length) % images.length;
        imgEl.src = images[current];
    };

    document.addEventListener('keydown', function (e) {
        if (!lightbox.classList.contains('open')) return;
        if (e.key === 'Escape')      closeLightbox();
        if (e.key === 'ArrowLeft')   navLightbox(-1);
        if (e.key === 'ArrowRight')  navLightbox(1);
    });
})();
</script>
@endif
@endpush