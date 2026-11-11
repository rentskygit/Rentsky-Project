@extends('layouts.app')
@section('title', $property->category?->name . ' — ' . $property->address)

@php
    $lightboxUrls = $property->images
        ->map(fn($img) => $img->image_url)
        ->filter()
        ->values()
        ->all();
@endphp

@push('styles')
<style>
    .ps-back {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.85rem;
        color: var(--brand-muted);
        text-decoration: none;
        margin-bottom: 1rem;
        transition: color 0.2s;
    }
    .ps-back:hover { color: var(--brand-secondary); }

    .ps-header { margin-bottom: 1.5rem; }

    .ps-title {
        font-family: var(--font-display);
        font-size: 1.75rem;
        font-weight: 700;
        letter-spacing: -0.025em;
        color: var(--brand-primary);
        line-height: 1.2;
        margin-bottom: 0.35rem;
    }

    .ps-subtitle {
        font-size: 0.95rem;
        color: var(--brand-muted);
    }

    .ps-layout {
        display: grid;
        grid-template-columns: 1.3fr 1fr;
        gap: 2rem;
        align-items: start;
    }

    .ps-gallery-main {
        width: 100%;
        aspect-ratio: 4/3;
        border-radius: 10px;
        overflow: hidden;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border);
        cursor: pointer;
        position: relative;
    }
    .ps-gallery-main img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s ease; }
    .ps-gallery-main:hover img { transform: scale(1.02); }
    .ps-gallery-empty {
        flex-direction: column;
        gap: 0.5rem;
        color: #d1d5db;
        font-size: 3rem;
        cursor: default;
    }
    .ps-gallery-empty:hover img { transform: none; }

    .ps-thumbs {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 0.5rem;
        margin-top: 0.6rem;
    }
    .ps-thumb {
        aspect-ratio: 1;
        border-radius: 6px;
        overflow: hidden;
        border: 2px solid var(--border);
        background: none;
        padding: 0;
        cursor: pointer;
        transition: all 0.15s;
    }
    .ps-thumb.active,
    .ps-thumb:hover { border-color: var(--brand-secondary); }
    .ps-thumb img { width: 100%; height: 100%; object-fit: cover; }

    .ps-block {
        background: white;
        border: 1px solid var(--border);
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.25rem;
    }
    .ps-block:last-child { margin-bottom: 0; }

    .ps-block-title {
        font-family: var(--font-display);
        font-size: 1rem;
        font-weight: 700;
        color: var(--brand-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.55rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border);
    }
    .ps-block-title i { color: var(--brand-secondary); }

    .ps-meta-badges { display: flex; gap: 0.4rem; flex-wrap: wrap; margin-bottom: 1rem; }
    .ps-badge {
        display: inline-block;
        padding: 0.2rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }
    .ps-badge.featured { background: #fce7f3; color: #9d174d; }
    .ps-badge.active   { background: #dcfce7; color: #166534; }
    .ps-badge.inactive { background: #fee2e2; color: #991b1b; }

    .ps-price-row {
        display: flex;
        align-items: baseline;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 1.25rem;
    }
    .ps-price-current {
        font-family: var(--font-display);
        font-size: 1.85rem;
        font-weight: 700;
        color: var(--brand-secondary);
        letter-spacing: -0.02em;
    }
    .ps-price-original {
        font-size: 1rem;
        color: var(--brand-muted);
        text-decoration: line-through;
    }

    .ps-info-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        font-size: 0.88rem;
    }
    .ps-info-row {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 1rem;
        padding-bottom: 0.6rem;
        border-bottom: 1px dashed var(--border);
    }
    .ps-info-row:last-child { border-bottom: none; padding-bottom: 0; }
    .ps-info-label { color: var(--brand-muted); display: inline-flex; align-items: center; gap: 0.4rem; }
    .ps-info-label i { font-size: 0.95rem; }
    .ps-info-value { font-weight: 600; color: var(--brand-primary); text-align: right; }

    .ps-description {
        font-size: 0.92rem;
        color: var(--brand-primary);
        line-height: 1.7;
        white-space: pre-line;
    }

    .ps-restrictions {
        background: #fef3c7;
        border: 1px solid #fde68a;
        border-radius: 8px;
        padding: 0.9rem 1.1rem;
        font-size: 0.88rem;
        color: #78350f;
        line-height: 1.6;
        white-space: pre-line;
    }

    .ps-chips { display: flex; flex-wrap: wrap; gap: 0.4rem; }
    .ps-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.35rem 0.8rem;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 9999px;
        font-size: 0.78rem;
        font-weight: 500;
        color: var(--brand-primary);
    }
    .ps-chip i { color: var(--brand-secondary); font-size: 0.85rem; }

    .ps-videos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 0.75rem;
    }
    .ps-videos-grid video {
        width: 100%;
        border-radius: 8px;
        background: black;
        aspect-ratio: 16/9;
    }

    .ps-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        margin-bottom: 1rem;
    }
    .ps-stat {
        background: var(--surface);
        padding: 0.85rem;
        border-radius: 8px;
        text-align: center;
    }
    .ps-stat-value {
        font-family: var(--font-display);
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--brand-secondary);
        line-height: 1;
        margin-bottom: 0.25rem;
    }
    .ps-stat-label {
        font-size: 0.72rem;
        color: var(--brand-muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
    }

    .ps-users-list {
        max-height: 220px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }
    .ps-user-row {
        display: flex;
        justify-content: space-between;
        padding: 0.4rem 0;
        font-size: 0.85rem;
        border-bottom: 1px solid var(--border);
    }
    .ps-user-row:last-child { border-bottom: none; }
    .ps-user-row span:first-child { color: var(--brand-primary); }
    .ps-user-row strong { color: var(--brand-secondary); }

    .ps-related {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
    .ps-related-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 8px;
        overflow: hidden;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s;
        display: block;
    }
    .ps-related-card:hover {
        border-color: var(--brand-secondary);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.06);
        color: inherit;
        text-decoration: none;
    }
    .ps-related-img {
        width: 100%;
        aspect-ratio: 4/3;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #d1d5db;
        font-size: 2rem;
        overflow: hidden;
    }
    .ps-related-img img { width: 100%; height: 100%; object-fit: cover; }
    .ps-related-body { padding: 0.85rem; }
    .ps-related-name {
        font-weight: 600;
        font-size: 0.88rem;
        color: var(--brand-primary);
        margin-bottom: 0.2rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .ps-related-price {
        font-size: 0.92rem;
        font-weight: 700;
        color: var(--brand-secondary);
    }

    .ps-lightbox {
        position: fixed;
        inset: 0;
        width: 100vw;
        height: 100vh;
        z-index: 200000;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.95);
        padding: 2rem;
    }
    .ps-lightbox.is-open { display: flex; }

    .ps-lightbox-img {
        max-width: 90vw;
        max-height: 85vh;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
        user-select: none;
        display: block;
    }

    .ps-lightbox-close {
        position: absolute;
        top: 1.25rem;
        right: 1.25rem;
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: white;
        font-size: 1.25rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
        z-index: 2;
    }
    .ps-lightbox-close:hover { background: rgba(255, 255, 255, 0.2); }

    .ps-lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.25);
        color: white;
        font-size: 1.4rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
        z-index: 2;
    }
    .ps-lightbox-nav:hover { background: rgba(255, 255, 255, 0.2); }
    .ps-lightbox-prev { left: 1.25rem; }
    .ps-lightbox-next { right: 1.25rem; }

    .ps-lightbox-counter {
        position: absolute;
        bottom: 1.25rem;
        left: 50%;
        transform: translateX(-50%);
        color: white;
        font-size: 0.88rem;
        font-weight: 600;
        background: rgba(0, 0, 0, 0.5);
        padding: 0.4rem 1rem;
        border-radius: 9999px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        z-index: 2;
    }

    @media (max-width: 900px) {
        .ps-layout { grid-template-columns: 1fr; }
        .ps-thumbs { grid-template-columns: repeat(4, 1fr); }
        .ps-title { font-size: 1.35rem; }
        .ps-price-current { font-size: 1.5rem; }
    }
    @media (max-width: 640px) {
        .ps-lightbox { padding: 0.75rem; }
        .ps-lightbox-close { width: 38px; height: 38px; font-size: 1.05rem; top: 0.75rem; right: 0.75rem; }
        .ps-lightbox-nav { width: 42px; height: 42px; font-size: 1.1rem; }
        .ps-lightbox-prev { left: 0.5rem; }
        .ps-lightbox-next { right: 0.5rem; }
        .ps-lightbox-img { max-width: 95vw; max-height: 75vh; }
    }
</style>
@endpush

@section('content')

<a href="{{ route('properties.index') }}" class="ps-back">
    <i class="bi bi-arrow-left"></i> Back to properties
</a>

<div class="ps-header">
    <h1 class="ps-title">
        {{ $property->address }}
        @if($property->unit_type) — {{ $property->unit_type }} @endif
    </h1>
    <p class="ps-subtitle">
        {{ $property->city }}@if($property->state), {{ $property->state }}@endif
        @if($property->zip_code) {{ $property->zip_code }} @endif
    </p>
</div>

<div class="ps-layout">

    <div>
        <div class="ps-gallery-main @if($property->images->isEmpty()) ps-gallery-empty @endif"
             @if($property->images->isNotEmpty()) data-lightbox-index="0" @endif>
            @if($property->primaryImage)
                <img id="psMainImage" src="{{ $property->primaryImage->image_url }}" alt="{{ $property->address }}">
            @elseif($property->images->isNotEmpty())
                <img id="psMainImage" src="{{ $property->images->first()->image_url }}" alt="{{ $property->address }}">
            @else
                <i class="bi bi-image"></i>
                <p style="font-size:0.85rem;color:var(--brand-muted);margin:0;">No images available</p>
            @endif
        </div>

        @if($property->images->count() > 1)
            <div class="ps-thumbs" id="psThumbs">
                @foreach($property->images as $index => $image)
                    <button type="button"
                            class="ps-thumb @if($image->is_primary || ($index === 0 && !$property->primaryImage)) active @endif"
                            data-thumb-index="{{ $index }}"
                            data-image-url="{{ $image->image_url }}">
                        <img src="{{ $image->image_url }}" alt="">
                    </button>
                @endforeach
            </div>
        @endif

        @if($property->videos->isNotEmpty())
            <div class="ps-block" style="margin-top:1.5rem;">
                <h2 class="ps-block-title"><i class="bi bi-camera-video"></i> Videos</h2>
                <div class="ps-videos-grid">
                    @foreach($property->videos as $video)
                        <video src="{{ $video->video_url }}" controls preload="metadata"></video>
                    @endforeach
                </div>
            </div>
        @endif

        @if($property->virtual_tour_url)
            <div class="ps-block">
                <h2 class="ps-block-title"><i class="bi bi-box-arrow-up-right"></i> Virtual Tour</h2>
                <a href="{{ $property->virtual_tour_url }}" target="_blank" rel="noopener"
                   class="btn-create">
                    <i class="bi bi-box-arrow-up-right"></i> Open virtual tour
                </a>
            </div>
        @endif
    </div>

    <div>
        <div class="ps-block">
            <div class="ps-meta-badges">
                @if($property->featured)
                    <span class="ps-badge featured">Featured</span>
                @endif
                @if($property->status === 'active')
                    <span class="ps-badge active">Active</span>
                @else
                    <span class="ps-badge inactive">Inactive</span>
                @endif
            </div>

            <div class="ps-price-row">
                @if($property->rent)
                    <span class="ps-price-current">${{ number_format((float) $property->rent, 0) }}</span>
                    <span style="color:var(--brand-muted);font-size:0.9rem;">/ month</span>
                @else
                    <span style="font-size:1rem;color:var(--brand-muted);">Contact for pricing</span>
                @endif
                @if($property->deposit)
                    <span style="font-size:0.85rem;color:var(--brand-muted);margin-left:auto;">
                        Deposit ${{ number_format((float) $property->deposit, 0) }}
                    </span>
                @endif
            </div>

            <div class="ps-info-list">
                @if($property->category)
                    <div class="ps-info-row">
                        <span class="ps-info-label"><i class="bi bi-door-closed"></i> Category</span>
                        <span class="ps-info-value">{{ $property->category->name }}</span>
                    </div>
                @endif
                @if($property->bathrooms)
                    <div class="ps-info-row">
                        <span class="ps-info-label"><i class="bi bi-droplet"></i> Bathrooms</span>
                        <span class="ps-info-value">{{ $property->bathrooms }}</span>
                    </div>
                @endif
                @if($property->square_feet)
                    <div class="ps-info-row">
                        <span class="ps-info-label"><i class="bi bi-rulers"></i> Square feet</span>
                        <span class="ps-info-value">{{ number_format($property->square_feet) }}</span>
                    </div>
                @endif
                @if($property->leaseTerm)
                    <div class="ps-info-row">
                        <span class="ps-info-label"><i class="bi bi-file-text"></i> Lease term</span>
                        <span class="ps-info-value">{{ $property->leaseTerm->name }}</span>
                    </div>
                @endif
                @if($property->available_date)
                    <div class="ps-info-row">
                        <span class="ps-info-label"><i class="bi bi-calendar-check"></i> Available</span>
                        <span class="ps-info-value">{{ $property->available_date->format('m/d/Y') }}</span>
                    </div>
                @endif
                @if($property->views_count)
                    <div class="ps-info-row">
                        <span class="ps-info-label"><i class="bi bi-eye"></i> Views</span>
                        <span class="ps-info-value">{{ number_format($property->views_count) }}</span>
                    </div>
                @endif
            </div>
        </div>

        @if($property->description)
            <div class="ps-block">
                <h2 class="ps-block-title"><i class="bi bi-text-paragraph"></i> Description</h2>
                <p class="ps-description">{{ $property->description }}</p>
            </div>
        @endif

        @if($property->parkingType || $property->coolingSystem || $property->heatingSystem || $property->laundry || $property->amenities->isNotEmpty())
            <div class="ps-block">
                <h2 class="ps-block-title"><i class="bi bi-stars"></i> Amenities</h2>
                <div class="ps-info-list" style="margin-bottom:1rem;">
                    @if($property->parkingType)
                        <div class="ps-info-row">
                            <span class="ps-info-label"><i class="bi bi-car-front"></i> Parking</span>
                            <span class="ps-info-value">{{ $property->parkingType->name }}@if($property->parking_spot) — {{ $property->parking_spot }} @endif</span>
                        </div>
                    @endif
                    @if($property->coolingSystem)
                        <div class="ps-info-row">
                            <span class="ps-info-label"><i class="bi bi-snow"></i> Cooling</span>
                            <span class="ps-info-value">{{ $property->coolingSystem->name }}</span>
                        </div>
                    @endif
                    @if($property->heatingSystem)
                        <div class="ps-info-row">
                            <span class="ps-info-label"><i class="bi bi-thermometer-half"></i> Heating</span>
                            <span class="ps-info-value">{{ $property->heatingSystem->name }}</span>
                        </div>
                    @endif
                    @if($property->laundry)
                        <div class="ps-info-row">
                            <span class="ps-info-label"><i class="bi bi-basket"></i> Laundry</span>
                            <span class="ps-info-value">{{ $property->laundry->name }}</span>
                        </div>
                    @endif
                </div>

                @if($property->amenities->isNotEmpty())
                    <div class="ps-chips">
                        @foreach($property->amenities as $amenity)
                            <span class="ps-chip">
                                @if($amenity->icon)<i class="bi {{ $amenity->icon }}"></i>@endif
                                {{ $amenity->name }}
                            </span>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        @if($property->utilities->isNotEmpty())
            <div class="ps-block">
                <h2 class="ps-block-title"><i class="bi bi-plug"></i> Utilities included</h2>
                <div class="ps-chips">
                    @foreach($property->utilities as $utility)
                        <span class="ps-chip">
                            @if($utility->icon)<i class="bi {{ $utility->icon }}"></i>@endif
                            {{ $utility->name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        @if($property->pet_friendly)
            <div class="ps-block">
                <h2 class="ps-block-title"><i class="bi bi-heart"></i> Pet policy</h2>
                <div class="ps-chips" style="margin-bottom:0.75rem;">
                    @if($property->allow_cats)
                        <span class="ps-chip"><i class="bi bi-check-circle-fill"></i> Cats allowed</span>
                    @endif
                    @if($property->allow_small_dogs)
                        <span class="ps-chip">
                            <i class="bi bi-check-circle-fill"></i> Small dogs
                            @if($property->small_dog_max_weight) (≤ {{ $property->small_dog_max_weight }} lbs) @endif
                        </span>
                    @endif
                    @if($property->allow_large_dogs)
                        <span class="ps-chip"><i class="bi bi-check-circle-fill"></i> Large dogs</span>
                    @endif
                </div>
                @if($property->max_pets)
                    <p style="font-size:0.85rem;color:var(--brand-muted);margin:0;">
                        Up to <strong>{{ $property->max_pets }}</strong> {{ \Illuminate\Support\Str::plural('pet', $property->max_pets) }} allowed.
                    </p>
                @endif
            </div>
        @endif

        @if($property->restrictions)
            <div class="ps-block">
                <h2 class="ps-block-title"><i class="bi bi-exclamation-triangle"></i> Restrictions</h2>
                <div class="ps-restrictions">{{ $property->restrictions }}</div>
            </div>
        @endif

        @if($property->internal_restrictions && (auth()->user()?->isAdmin() || auth()->user()?->isAdviser()))
            <div class="ps-block">
                <h2 class="ps-block-title">
                    <i class="bi bi-lock-fill" style="color:#b91c1c;"></i>
                    Internal Restrictions
                    <span style="margin-left:0.4rem;font-size:0.65rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;background:#fee2e2;color:#991b1b;padding:0.15rem 0.5rem;border-radius:9999px;">
                        Staff only
                    </span>
                </h2>
                <div class="ps-restrictions" style="background:#fef2f2;border-color:#fecaca;color:#7f1d1d;">
                    {{ $property->internal_restrictions }}
                </div>
            </div>
        @endif

        @if(auth()->user() && auth()->user()->canSeeSidebar() && isset($stats))
            <div class="ps-block">
                <h2 class="ps-block-title"><i class="bi bi-graph-up"></i> View statistics</h2>

                <div class="ps-stats-grid">
                    <div class="ps-stat">
                        <div class="ps-stat-value">{{ $stats['total'] ?? 0 }}</div>
                        <div class="ps-stat-label">Total views</div>
                    </div>
                    <div class="ps-stat">
                        <div class="ps-stat-value">{{ $stats['unique_users'] ?? 0 }}</div>
                        <div class="ps-stat-label">Unique users</div>
                    </div>
                    <div class="ps-stat">
                        <div class="ps-stat-value">{{ $stats['unique_ips'] ?? 0 }}</div>
                        <div class="ps-stat-label">Unique IPs</div>
                    </div>
                </div>

                @if(!empty($stats['by_user']) && $stats['by_user']->isNotEmpty())
                    <div style="font-size:0.8rem;font-weight:600;color:var(--brand-muted);text-transform:uppercase;letter-spacing:0.05em;margin-bottom:0.5rem;">
                        Views by user
                    </div>
                    <div class="ps-users-list">
                        @foreach($stats['by_user'] as $row)
                            <div class="ps-user-row">
                                <span>{{ $row->user?->name ?? 'User #' . $row->user_id }}</span>
                                <strong>{{ $row->total }} views</strong>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        @if($relatedProperties->isNotEmpty())
            <div class="ps-block">
                <h2 class="ps-block-title"><i class="bi bi-house-door"></i> Related properties</h2>
                <div class="ps-related">
                    @foreach($relatedProperties as $related)
                        <a href="{{ route('properties.show', $related->slug) }}" class="ps-related-card">
                            <div class="ps-related-img">
                                @if($related->primaryImage)
                                    <img src="{{ $related->primaryImage->image_url }}" alt="{{ $related->address }}">
                                @else
                                    <i class="bi bi-image"></i>
                                @endif
                            </div>
                            <div class="ps-related-body">
                                <div class="ps-related-name">{{ $related->address }}</div>
                                @if($related->rent)
                                    <div class="ps-related-price">${{ number_format((float) $related->rent, 0) }}/mo</div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

@if($property->images->isNotEmpty())
<div class="ps-lightbox" id="psLightbox" role="dialog" aria-modal="true" aria-label="Image viewer">
    <button type="button" class="ps-lightbox-close" id="psLightboxClose" aria-label="Close">
        <i class="bi bi-x-lg"></i>
    </button>
    <button type="button" class="ps-lightbox-nav ps-lightbox-prev" id="psLightboxPrev" aria-label="Previous image">
        <i class="bi bi-chevron-left"></i>
    </button>
    <img id="psLightboxImg" class="ps-lightbox-img" src="" alt="Property image">
    <button type="button" class="ps-lightbox-nav ps-lightbox-next" id="psLightboxNext" aria-label="Next image">
        <i class="bi bi-chevron-right"></i>
    </button>
    <div class="ps-lightbox-counter">
        <span id="psLightboxCurrent">1</span> / <span id="psLightboxTotal">{{ count($lightboxUrls) }}</span>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    var lbUrls = @json($lightboxUrls);
    var lbEl = document.getElementById('psLightbox');
    var lbImg = document.getElementById('psLightboxImg');
    var lbCur = document.getElementById('psLightboxCurrent');
    var lbTotal = document.getElementById('psLightboxTotal');
    var lbClose = document.getElementById('psLightboxClose');
    var lbPrev = document.getElementById('psLightboxPrev');
    var lbNext = document.getElementById('psLightboxNext');

    var lbIdx = 0;

    function lbRender() {
        if (!lbImg || lbUrls.length === 0) return;
        lbImg.src = lbUrls[lbIdx];
        if (lbCur) lbCur.textContent = lbIdx + 1;
        if (lbTotal) lbTotal.textContent = lbUrls.length;
    }

    function openLightbox(idx) {
        if (!lbEl || !lbImg || lbUrls.length === 0) return;
        var n = parseInt(idx, 10);
        if (isNaN(n)) n = 0;
        lbIdx = Math.max(0, Math.min(n, lbUrls.length - 1));
        lbRender();
        lbEl.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        if (!lbEl) return;
        lbEl.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    function lightboxNext() {
        if (!lbUrls.length) return;
        lbIdx = (lbIdx + 1) % lbUrls.length;
        lbRender();
    }

    function lightboxPrev() {
        if (!lbUrls.length) return;
        lbIdx = (lbIdx - 1 + lbUrls.length) % lbUrls.length;
        lbRender();
    }

    if (lbEl) {
        if (lbClose) lbClose.addEventListener('click', closeLightbox);
        if (lbPrev) lbPrev.addEventListener('click', lightboxPrev);
        if (lbNext) lbNext.addEventListener('click', lightboxNext);

        lbEl.addEventListener('click', function (e) {
            if (e.target === lbEl) closeLightbox();
        });
    }

    var mainImg = document.getElementById('psMainImage');
    var mainWrap = mainImg ? mainImg.closest('.ps-gallery-main') : null;
    if (mainWrap) {
        mainWrap.addEventListener('click', function () {
            if (lbUrls.length === 0) return;

            var activeThumb = document.querySelector('#psThumbs .ps-thumb.active');
            var idx = 0;
            if (activeThumb) {
                idx = parseInt(activeThumb.dataset.thumbIndex, 10) || 0;
            }
            openLightbox(idx);
        });
    }

    var thumbs = document.querySelectorAll('#psThumbs .ps-thumb');
    thumbs.forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            var url = thumb.dataset.imageUrl;
            var idx = parseInt(thumb.dataset.thumbIndex, 10) || 0;

            if (mainImg && url) {
                mainImg.src = url;
            }
            thumbs.forEach(function (t) { t.classList.remove('active'); });
            thumb.classList.add('active');

            openLightbox(idx);
        });
    });

    document.addEventListener('keydown', function (e) {
        if (lbEl && lbEl.classList.contains('is-open')) {
            if (e.key === 'Escape') { closeLightbox(); return; }
            if (e.key === 'ArrowRight') { lightboxNext(); return; }
            if (e.key === 'ArrowLeft') { lightboxPrev(); return; }
        }
    });
})();
</script>
@endpush