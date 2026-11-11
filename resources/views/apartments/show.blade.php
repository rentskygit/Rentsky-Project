@extends('layouts.app')

@php
    $isAuth = auth()->check();
    $hasQualification = $isAuth && auth()->user()->has_qualification;
    $fullDates = [];
    if ($hasQualification) {
        $fullDates = \App\Models\Showing::where('user_id', auth()->id())
            ->selectRaw('showing_date, COUNT(*) as cnt')
            ->groupBy('showing_date')
            ->havingRaw('COUNT(*) >= 3')
            ->pluck('showing_date')
            ->map(fn($d) => \Carbon\Carbon::parse($d)->toDateString())
            ->values()
            ->toArray();
    }
    $lightboxUrls = $apartment->images
        ->map(fn($img) => $img->image_url)
        ->filter()
        ->values()
        ->all();
    $currentUser = auth()->user();
    $canSeeInternal = false;
    if ($currentUser) {
        if ($currentUser->isSuperAdmin()) {
            $canSeeInternal = true;
        } else {
            $canSeeInternal = $currentUser->roles->contains(function ($role) {
                $roleId = (int) $role->id;
                $slug   = strtolower($role->slug ?? '');
                $name   = strtolower($role->name ?? '');
                return $roleId === 1
                    || in_array($slug, ['admin', 'adviser', 'advisor'], true)
                    || in_array($name, ['admin', 'adviser', 'advisor'], true);
            });
        }
    }
@endphp

@section('title', $apartment->address . ' — ' . ($apartment->category?->name ?? 'Apartment'))

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<style>
    .apt-back { display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.85rem; color: var(--brand-muted); text-decoration: none; margin-bottom: 1.5rem; transition: color 0.2s; }
    .apt-back:hover { color: var(--brand-primary); }

    .apt-header { margin-bottom: 1.75rem; }
    .apt-title { font-family: var(--font-display); font-size: 2.5rem; font-weight: 700; letter-spacing: -0.03em; line-height: 1.1; color: var(--brand-primary); margin-bottom: 0.4rem; }
    .apt-address { font-size: 1rem; color: var(--brand-muted); }

    .apt-gallery { display: grid; grid-template-columns: 2fr 1fr; grid-template-rows: 1fr 1fr; gap: 0.5rem; border-radius: 12px; overflow: hidden; margin-bottom: 2rem; height: 500px; }
    .apt-gallery-item { position: relative; overflow: hidden; background: #f3f4f6; border: none; padding: 0; display: block; cursor: pointer; text-align: left; width: 100%; height: 100%; font-family: inherit; }
    .apt-gallery-item:first-child { grid-row: 1 / 3; }
    .apt-gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease; display: block; }
    .apt-gallery-item:hover img { transform: scale(1.04); }
    .apt-gallery-more { position: absolute; inset: 0; background: rgba(0, 0, 0, 0.6); color: white; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 0.25rem; font-weight: 700; font-size: 1rem; pointer-events: none; }
    .apt-gallery-more i { font-size: 1.5rem; }
    .apt-gallery-empty { grid-column: 1 / -1; grid-row: 1 / -1; display: flex; align-items: center; justify-content: center; background: #f3f4f6; color: #d1d5db; font-size: 4rem; }

    .apt-info-row { display: flex; gap: 1.5rem; flex-wrap: wrap; padding: 1.15rem 0; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); margin-bottom: 2rem; }
    .apt-info-item { display: flex; flex-direction: column; gap: 0.2rem; }
    .apt-info-label { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.06em; color: var(--brand-muted); font-weight: 600; }
    .apt-info-value { font-size: 1.05rem; font-weight: 700; color: var(--brand-primary); }

    .apt-layout { display: grid; grid-template-columns: 1fr 360px; gap: 2.5rem; align-items: start; }

    .apt-block { margin-bottom: 2rem; }
    .apt-block:last-child { margin-bottom: 0; }
    .apt-block-title { font-family: var(--font-display); font-size: 1.15rem; font-weight: 700; letter-spacing: -0.01em; color: var(--brand-primary); margin-bottom: 1rem; padding-bottom: 0.7rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; gap: 0.5rem; }
    .apt-description { font-size: 0.95rem; color: var(--brand-primary); line-height: 1.75; white-space: pre-line; }

    .apt-list { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.6rem 1.5rem; list-style: none; padding: 0; margin: 0; }
    .apt-list li { display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; color: var(--brand-primary); }
    .apt-list li i { color: var(--brand-secondary); font-size: 0.85rem; flex-shrink: 0; }

    .apt-restrictions {
        background: #fef3c7;
        border: 1px solid #fde68a;
        border-radius: 8px;
        padding: 0.9rem 1.1rem;
        font-size: 0.88rem;
        color: #78350f;
        line-height: 1.6;
        white-space: pre-line;
    }

    .apt-internal-restrictions {
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 0.9rem 1.1rem;
        font-size: 0.88rem;
        color: #7f1d1d;
        line-height: 1.6;
        white-space: pre-line;
    }

    .apt-staff-badge {
        margin-left: 0.4rem;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        background: #fee2e2;
        color: #991b1b;
        padding: 0.15rem 0.5rem;
        border-radius: 9999px;
    }

    .apt-videos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
    }
    .apt-videos-grid video {
        width: 100%;
        border-radius: 8px;
        background: black;
        aspect-ratio: 16/9;
        display: block;
    }

    .apt-tour-cta {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.85rem 1.5rem;
        background: var(--brand-secondary);
        color: white;
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s;
    }
    .apt-tour-cta:hover { background: #2a3d85; color: white; text-decoration: none; }

    .apt-sidebar { position: sticky; top: calc(var(--header-height) + 1.5rem); }
    .apt-sidebar-card { background: white; border: 1px solid var(--border); border-radius: 12px; padding: 1.75rem; }
    .apt-sidebar-price { font-size: 2rem; font-weight: 700; letter-spacing: -0.02em; color: var(--brand-primary); margin-bottom: 0.2rem; }
    .apt-sidebar-price span { font-size: 0.95rem; font-weight: 500; color: var(--brand-muted); margin-left: 0.1rem; }
    .apt-sidebar-cta { display: flex; align-items: center; justify-content: center; gap: 0.5rem; width: 100%; padding: 0.9rem 1.25rem; background: var(--brand-primary); color: white; border-radius: 6px; font-weight: 600; font-size: 0.9rem; text-decoration: none; border: none; cursor: pointer; font-family: inherit; transition: all 0.2s; margin: 1.25rem 0; }
    .apt-sidebar-cta:hover { background: var(--brand-secondary); color: white; text-decoration: none; }

    .apt-facts { display: flex; flex-direction: column; gap: 0.75rem; padding-top: 1.25rem; border-top: 1px solid var(--border); }
    .apt-fact { display: flex; justify-content: space-between; align-items: baseline; font-size: 0.88rem; gap: 1rem; }
    .apt-fact-label { color: var(--brand-muted); }
    .apt-fact-value { font-weight: 600; color: var(--brand-primary); text-align: right; }

    .apt-related { margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border); }
    .apt-related-title { font-family: var(--font-display); font-size: 1.35rem; font-weight: 700; letter-spacing: -0.02em; margin-bottom: 1.25rem; color: var(--brand-primary); }
    .apt-related-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(240px, 1fr)); gap: 1rem; }
    .apt-related-card { background: white; border: 1px solid var(--border); border-radius: 8px; overflow: hidden; text-decoration: none; color: inherit; transition: all 0.2s ease; }
    .apt-related-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.06); text-decoration: none; color: inherit; }
    .apt-related-card-image { width: 100%; aspect-ratio: 4/3; background: #f3f4f6; overflow: hidden; }
    .apt-related-card-image img { width: 100%; height: 100%; object-fit: cover; }
    .apt-related-card-body { padding: 0.85rem; }
    .apt-related-card-title { font-size: 0.9rem; font-weight: 600; color: var(--brand-primary); margin-bottom: 0.2rem; line-height: 1.3; }
    .apt-related-card-price { font-size: 1rem; font-weight: 700; color: var(--brand-primary); }

    .show-modal-backdrop {
        position: fixed;
        inset: 0;
        z-index: 99999;
        display: none;
        align-items: center;
        justify-content: center;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        padding: 1.5rem;
    }
    .show-modal-backdrop.open { display: flex; }
    .show-modal {
        background: white;
        border-radius: 12px;
        max-width: 500px;
        width: 100%;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
        overflow: hidden;
        animation: showModalIn 0.2s ease;
    }
    @keyframes showModalIn {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }
    .show-modal-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .show-modal-header h3 {
        font-family: var(--font-display);
        font-size: 1.05rem;
        font-weight: 700;
        margin: 0;
    }
    .show-modal-close {
        background: none;
        border: none;
        font-size: 1.25rem;
        color: var(--brand-muted);
        cursor: pointer;
        width: 32px;
        height: 32px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .show-modal-close:hover {
        background: var(--surface);
        color: var(--brand-primary);
    }
    .show-modal-body { padding: 1.5rem; }
    .show-modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        background: var(--surface);
    }
    .show-property-preview {
        background: var(--surface);
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1.25rem;
    }
    .show-property-address {
        font-weight: 700;
        color: var(--brand-primary);
        font-size: 0.92rem;
        margin-bottom: 0.25rem;
    }
    .show-property-meta {
        font-size: 0.8rem;
        color: var(--brand-muted);
    }
    .show-property-price {
        font-family: var(--font-display);
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--brand-secondary);
        margin-top: 0.5rem;
    }
    .show-qualification-card {
        text-align: center;
        padding: 2rem 1rem;
    }
    .show-qualification-icon {
        font-size: 2.5rem;
        color: var(--brand-secondary);
        margin-bottom: 1rem;
        display: block;
    }
    .flatpickr-calendar {
        font-family: var(--font-sans);
        border-radius: 6px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        z-index: 100000;
    }
    .flatpickr-day.selected,
    .flatpickr-day.selected:hover {
        background: var(--brand-secondary);
        border-color: var(--brand-secondary);
    }
    .flatpickr-day.today { border-color: var(--brand-secondary); }

    .apt-lightbox {
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
    .apt-lightbox.is-open { display: flex; }

    .apt-lightbox-img {
        max-width: 90vw;
        max-height: 85vh;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
        user-select: none;
        display: block;
    }

    .apt-lightbox-close {
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
    .apt-lightbox-close:hover { background: rgba(255, 255, 255, 0.2); }

    .apt-lightbox-nav {
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
    .apt-lightbox-nav:hover { background: rgba(255, 255, 255, 0.2); }
    .apt-lightbox-prev { left: 1.25rem; }
    .apt-lightbox-next { right: 1.25rem; }

    .apt-lightbox-counter {
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

    @media (max-width: 1024px) {
        .apt-layout { grid-template-columns: 1fr; }
        .apt-sidebar { position: static; }
        .apt-gallery { height: 380px; }
    }
    @media (max-width: 640px) {
        .apt-title { font-size: 1.5rem; }
        .apt-gallery { grid-template-columns: 1fr; grid-template-rows: 1fr; height: 260px; }
        .apt-gallery-item:not(:first-child) { display: none; }
        .apt-list { grid-template-columns: 1fr; }

        .apt-lightbox { padding: 0.75rem; }
        .apt-lightbox-close { width: 38px; height: 38px; font-size: 1.05rem; top: 0.75rem; right: 0.75rem; }
        .apt-lightbox-nav { width: 42px; height: 42px; font-size: 1.1rem; }
        .apt-lightbox-prev { left: 0.5rem; }
        .apt-lightbox-next { right: 0.5rem; }
        .apt-lightbox-img { max-width: 95vw; max-height: 75vh; }
    }
</style>
@endpush

@section('content')

<a href="{{ route('apartments.index') }}" class="apt-back">
    <i class="bi bi-arrow-left"></i> Back to apartments
</a>

<div class="apt-header">
    <h1 class="apt-title">
        {{ $apartment->address }}
        @if($apartment->unit_type) {{ $apartment->unit_type }} @endif
    </h1>
    <p class="apt-address">
        {{ $apartment->city }}@if($apartment->state), {{ $apartment->state }}@endif
        {{ $apartment->zip_code }}
    </p>
</div>

@if($apartment->images->isNotEmpty())
    <div class="apt-gallery" id="aptGallery">
        @foreach($apartment->images->take(3) as $i => $img)
            <button type="button"
                    class="apt-gallery-item"
                    data-lightbox-index="{{ $i }}"
                    aria-label="View image {{ $i + 1 }}">
                <img src="{{ $img->image_url }}" alt="{{ $apartment->address }} image {{ $i + 1 }}">
                @if($i === 2 && $apartment->images->count() > 3)
                    <div class="apt-gallery-more">
                        <i class="bi bi-images"></i>
                        +{{ $apartment->images->count() - 3 }} more
                    </div>
                @endif
            </button>
        @endforeach
    </div>
@else
    <div class="apt-gallery" style="height:260px;grid-template-columns:1fr;grid-template-rows:1fr;">
        <div class="apt-gallery-empty"><i class="bi bi-image"></i></div>
    </div>
@endif

<div class="apt-info-row">
    @if($apartment->category)
        <div class="apt-info-item">
            <span class="apt-info-label">Bedrooms</span>
            <span class="apt-info-value">{{ $apartment->category->name }}</span>
        </div>
    @endif
    @if($apartment->bathrooms)
        <div class="apt-info-item">
            <span class="apt-info-label">Bathrooms</span>
            <span class="apt-info-value">{{ $apartment->bathrooms }}</span>
        </div>
    @endif
    @if($apartment->square_feet)
        <div class="apt-info-item">
            <span class="apt-info-label">Square Feet</span>
            <span class="apt-info-value">{{ number_format($apartment->square_feet) }}</span>
        </div>
    @endif
    @if($apartment->available_date)
        <div class="apt-info-item">
            <span class="apt-info-label">Available</span>
            <span class="apt-info-value">{{ $apartment->available_date->format('m/d/Y') }}</span>
        </div>
    @endif
</div>

<div class="apt-layout">
    <div>
        @if($apartment->description)
            <div class="apt-block">
                <h2 class="apt-block-title">About this apartment</h2>
                <p class="apt-description">{{ $apartment->description }}</p>
            </div>
        @endif

        @if($apartment->videos->isNotEmpty())
            <div class="apt-block">
                <h2 class="apt-block-title"><i class="bi bi-camera-video"></i> Videos</h2>
                <div class="apt-videos-grid">
                    @foreach($apartment->videos as $video)
                        <video src="{{ $video->video_url }}" controls preload="metadata"></video>
                    @endforeach
                </div>
            </div>
        @endif

        @if($apartment->virtual_tour_url)
            <div class="apt-block">
                <h2 class="apt-block-title"><i class="bi bi-box-arrow-up-right"></i> Virtual Tour</h2>
                <a href="{{ $apartment->virtual_tour_url }}" target="_blank" rel="noopener" class="apt-tour-cta">
                    <i class="bi bi-box-arrow-up-right"></i> Open virtual tour
                </a>
            </div>
        @endif

        @if($apartment->amenities->isNotEmpty() || $apartment->parkingType || $apartment->coolingSystem || $apartment->heatingSystem || $apartment->laundry)
            <div class="apt-block">
                <h2 class="apt-block-title">Amenities & Features</h2>
                <ul class="apt-list">
                    @if($apartment->parkingType)
                        <li><i class="bi bi-car-front"></i> Parking: {{ $apartment->parkingType->name }}@if($apartment->parking_spot) — {{ $apartment->parking_spot }} @endif</li>
                    @endif
                    @if($apartment->coolingSystem)
                        <li><i class="bi bi-snow"></i> Cooling: {{ $apartment->coolingSystem->name }}</li>
                    @endif
                    @if($apartment->heatingSystem)
                        <li><i class="bi bi-thermometer-half"></i> Heating: {{ $apartment->heatingSystem->name }}</li>
                    @endif
                    @if($apartment->laundry)
                        <li><i class="bi bi-basket"></i> Laundry: {{ $apartment->laundry->name }}</li>
                    @endif
                    @foreach($apartment->amenities as $am)
                        <li><i class="bi bi-check2"></i> {{ $am->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($apartment->utilities->isNotEmpty())
            <div class="apt-block">
                <h2 class="apt-block-title">Utilities Included</h2>
                <ul class="apt-list">
                    @foreach($apartment->utilities as $util)
                        <li><i class="bi bi-check2"></i> {{ $util->name }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($apartment->pet_friendly || $apartment->allow_cats || $apartment->allow_small_dogs || $apartment->allow_large_dogs)
            <div class="apt-block">
                <h2 class="apt-block-title">Pet Policy</h2>
                <ul class="apt-list">
                    @if($apartment->allow_cats)
                        <li><i class="bi bi-check2"></i> Cats allowed</li>
                    @endif
                    @if($apartment->allow_small_dogs)
                        <li><i class="bi bi-check2"></i> Small dogs allowed @if($apartment->small_dog_max_weight) (up to {{ $apartment->small_dog_max_weight }} lbs) @endif</li>
                    @endif
                    @if($apartment->allow_large_dogs)
                        <li><i class="bi bi-check2"></i> Large dogs allowed</li>
                    @endif
                    @if($apartment->max_pets)
                        <li><i class="bi bi-info-circle"></i> Up to {{ $apartment->max_pets }} pet{{ $apartment->max_pets > 1 ? 's' : '' }}</li>
                    @endif
                </ul>
            </div>
        @endif

        @if($apartment->restrictions)
            <div class="apt-block">
                <h2 class="apt-block-title"><i class="bi bi-exclamation-triangle"></i> Restrictions</h2>
                <div class="apt-restrictions">{{ $apartment->restrictions }}</div>
            </div>
        @endif

        @if($canSeeInternal && $apartment->internal_restrictions)
            <div class="apt-block">
                <h2 class="apt-block-title">
                    <i class="bi bi-lock-fill" style="color:#b91c1c;"></i>
                    Internal Restrictions
                    <span class="apt-staff-badge">Staff only</span>
                </h2>
                <div class="apt-internal-restrictions">{{ $apartment->internal_restrictions }}</div>
            </div>
        @endif
    </div>

    <aside class="apt-sidebar">
        <div class="apt-sidebar-card">
            @if($apartment->rent)
                <div class="apt-sidebar-price">
                    ${{ number_format($apartment->rent, 0) }}<span>/month</span>
                </div>
            @else
                <div class="apt-sidebar-price" style="font-size:1.15rem;">Contact for pricing</div>
            @endif

            <button type="button" class="apt-sidebar-cta" onclick="openScheduleModal()">
                <i class="bi bi-calendar-event"></i> Schedule a showing
            </button>

            <div class="apt-facts">
                @if($apartment->deposit)
                    <div class="apt-fact">
                        <span class="apt-fact-label">Deposit</span>
                        <span class="apt-fact-value">${{ number_format($apartment->deposit, 0) }}</span>
                    </div>
                @endif
                @if($apartment->leaseTerm)
                    <div class="apt-fact">
                        <span class="apt-fact-label">Lease term</span>
                        <span class="apt-fact-value">{{ $apartment->leaseTerm->name }}</span>
                    </div>
                @endif
                @if($apartment->available_date)
                    <div class="apt-fact">
                        <span class="apt-fact-label">Available</span>
                        <span class="apt-fact-value">{{ $apartment->available_date->format('m/d/Y') }}</span>
                    </div>
                @endif
                @if($apartment->unit_type)
                    <div class="apt-fact">
                        <span class="apt-fact-label">Unit</span>
                        <span class="apt-fact-value">{{ $apartment->unit_type }}</span>
                    </div>
                @endif
            </div>
        </div>
    </aside>
</div>

@if($related->isNotEmpty())
    <div class="apt-related">
        <h2 class="apt-related-title">Similar apartments</h2>
        <div class="apt-related-grid">
            @foreach($related as $rel)
                <a href="{{ route('apartments.show', $rel->slug) }}" class="apt-related-card">
                    <div class="apt-related-card-image">
                        @if($rel->primaryImage)
                            <img src="{{ $rel->primaryImage->image_url }}" alt="{{ $rel->address }}" loading="lazy">
                        @else
                            <div class="apt-gallery-empty" style="font-size:2rem;"><i class="bi bi-image"></i></div>
                        @endif
                    </div>
                    <div class="apt-related-card-body">
                        <div class="apt-related-card-title">{{ $rel->address }} @if($rel->unit_type) {{ $rel->unit_type }} @endif</div>
                        @if($rel->rent)
                            <div class="apt-related-card-price">${{ number_format($rel->rent, 0) }}<span style="font-size:0.7rem;color:var(--brand-muted);font-weight:500;">/mo</span></div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
@endif

@if($isAuth && $hasQualification)
<div class="show-modal-backdrop" id="scheduleModal" onclick="if(event.target===this) closeScheduleModal()">
    <div class="show-modal">
        <div class="show-modal-header">
            <h3>Schedule a showing</h3>
            <button type="button" class="show-modal-close" onclick="closeScheduleModal()">&times;</button>
        </div>
        <form method="POST" action="{{ route('showings.store') }}">
            @csrf
            <input type="hidden" name="property_id" value="{{ $apartment->id }}">
            <div class="show-modal-body">
                <div class="show-property-preview">
                    <div class="show-property-address">
                        {{ $apartment->address }}
                        @if($apartment->unit_type)
                            — {{ $apartment->unit_type }}
                        @endif
                    </div>
                    <div class="show-property-meta">
                        {{ $apartment->city }}@if($apartment->state), {{ $apartment->state }}@endif
                    </div>
                    @if($apartment->rent)
                        <div class="show-property-price">${{ number_format($apartment->rent, 0) }}/mo</div>
                    @endif
                </div>

                <div class="form-group">
                    <label class="form-label">Date</label>
                    <input type="text"
                           name="showing_date"
                           id="scheduleDate"
                           class="form-control"
                           placeholder="MM/DD/YYYY"
                           data-full-dates="{{ implode(',', $fullDates) }}"
                           required>
                    <small class="text-muted" style="display:block;margin-top:0.35rem;">
                        You can schedule up to <strong>3 showings per day</strong>.
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">Time</label>
                    <select name="showing_time" class="form-control" required>
                        <option value="">— Select a time —</option>
                        @foreach(['8:00 AM','9:00 AM','10:00 AM','11:00 AM','12:00 PM','1:00 PM','2:00 PM','3:00 PM','4:00 PM','5:00 PM','6:00 PM','7:00 PM','8:00 PM'] as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="show-modal-footer">
                <button type="button" class="btn-edit" onclick="closeScheduleModal()">Cancel</button>
                <button type="submit" class="btn-create">
                    <i class="bi bi-check-lg"></i> Schedule
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@if($isAuth && !$hasQualification)
<div class="show-modal-backdrop" id="needsQualModal" onclick="if(event.target===this) closeScheduleModal()">
    <div class="show-modal">
        <div class="show-modal-header">
            <h3>Qualification required</h3>
            <button type="button" class="show-modal-close" onclick="closeScheduleModal()">&times;</button>
        </div>
        <div class="show-modal-body">
            <div class="show-qualification-card">
                <i class="bi bi-clipboard-check show-qualification-icon"></i>
                <h4 style="font-family:var(--font-display);font-size:1.05rem;font-weight:700;margin-bottom:0.5rem;">
                    Complete your qualification first
                </h4>
                <p style="color:var(--brand-muted);font-size:0.88rem;line-height:1.55;margin-bottom:1.5rem;">
                    To schedule a showing, you must first complete your rental qualification. It only takes a few minutes.
                </p>
                <a href="{{ route('qualifications.index') }}" class="btn-create">
                    <i class="bi bi-arrow-right"></i> Go to qualification
                </a>
            </div>
        </div>
    </div>
</div>
@endif

@if(!$isAuth)
<div class="show-modal-backdrop" id="signInModal" onclick="if(event.target===this) closeScheduleModal()">
    <div class="show-modal">
        <div class="show-modal-header">
            <h3>Sign in required</h3>
            <button type="button" class="show-modal-close" onclick="closeScheduleModal()">&times;</button>
        </div>
        <div class="show-modal-body">
            <div class="show-qualification-card">
                <i class="bi bi-person-lock show-qualification-icon"></i>
                <h4 style="font-family:var(--font-display);font-size:1.05rem;font-weight:700;margin-bottom:0.5rem;">
                    Sign in to schedule a showing
                </h4>
                <p style="color:var(--brand-muted);font-size:0.88rem;line-height:1.55;margin-bottom:1.5rem;">
                    You need an account to schedule showings. Sign in or create one for free.
                </p>
                <div style="display:flex;gap:0.5rem;justify-content:center;flex-wrap:wrap;">
                    <a href="{{ route('login') }}" class="btn-create">Sign in</a>
                    <a href="{{ route('register') }}" class="btn-edit">Create account</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@if($apartment->images->isNotEmpty())
<div class="apt-lightbox" id="aptLightbox" role="dialog" aria-modal="true" aria-label="Image viewer">
    <button type="button" class="apt-lightbox-close" id="aptLightboxClose" aria-label="Close">
        <i class="bi bi-x-lg"></i>
    </button>
    <button type="button" class="apt-lightbox-nav apt-lightbox-prev" id="aptLightboxPrev" aria-label="Previous image">
        <i class="bi bi-chevron-left"></i>
    </button>
    <img id="aptLightboxImg" class="apt-lightbox-img" src="" alt="Apartment image">
    <button type="button" class="apt-lightbox-nav apt-lightbox-next" id="aptLightboxNext" aria-label="Next image">
        <i class="bi bi-chevron-right"></i>
    </button>
    <div class="apt-lightbox-counter">
        <span id="aptLightboxCurrent">1</span> / <span id="aptLightboxTotal">{{ count($lightboxUrls) }}</span>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script>
(function () {
    'use strict';

    window.openScheduleModal = function () {
        var ids = ['scheduleModal', 'needsQualModal', 'signInModal'];
        for (var i = 0; i < ids.length; i++) {
            var el = document.getElementById(ids[i]);
            if (el) {
                el.classList.add('open');
                document.body.style.overflow = 'hidden';
                return;
            }
        }
    };

    window.closeScheduleModal = function () {
        var modals = document.querySelectorAll('.show-modal-backdrop');
        for (var i = 0; i < modals.length; i++) {
            modals[i].classList.remove('open');
        }
        if (!document.getElementById('aptLightbox')?.classList.contains('is-open')) {
            document.body.style.overflow = '';
        }
    };

    var dateInput = document.getElementById('scheduleDate');
    if (dateInput && typeof window.flatpickr === 'function') {
        try {
            var rawDates = dateInput.dataset.fullDates || '';
            var fullDates = rawDates ? rawDates.split(',') : [];
            window.flatpickr('#scheduleDate', {
                dateFormat: 'Y-m-d',
                altInput: true,
                altFormat: 'm/d/Y',
                allowInput: false,
                disableMobile: true,
                minDate: 'today',
                disable: [
                    function (date) {
                        var y = date.getFullYear();
                        var m = String(date.getMonth() + 1).padStart(2, '0');
                        var d = String(date.getDate()).padStart(2, '0');
                        return fullDates.indexOf(y + '-' + m + '-' + d) !== -1;
                    }
                ]
            });
        } catch (err) {
            dateInput.type = 'date';
            dateInput.removeAttribute('placeholder');
        }
    }

    var lbUrls = @json($lightboxUrls);

    var lbEl = document.getElementById('aptLightbox');
    var lbImg = document.getElementById('aptLightboxImg');
    var lbCur = document.getElementById('aptLightboxCurrent');
    var lbTotal = document.getElementById('aptLightboxTotal');
    var lbClose = document.getElementById('aptLightboxClose');
    var lbPrev = document.getElementById('aptLightboxPrev');
    var lbNext = document.getElementById('aptLightboxNext');

    var lbIdx = 0;

    function lbRender() {
        if (!lbImg || lbUrls.length === 0) return;
        lbImg.src = lbUrls[lbIdx];
        if (lbCur) lbCur.textContent = lbIdx + 1;
        if (lbTotal) lbTotal.textContent = lbUrls.length;
    }

    window.openLightbox = function (idx) {
        if (!lbEl || !lbImg || lbUrls.length === 0) return;
        var n = parseInt(idx, 10);
        if (isNaN(n)) n = 0;
        lbIdx = Math.max(0, Math.min(n, lbUrls.length - 1));
        lbRender();
        lbEl.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    };

    window.closeLightbox = function () {
        if (!lbEl) return;
        lbEl.classList.remove('is-open');
        if (!document.querySelector('.show-modal-backdrop.open')) {
            document.body.style.overflow = '';
        }
    };

    window.lightboxNext = function () {
        if (!lbUrls.length) return;
        lbIdx = (lbIdx + 1) % lbUrls.length;
        lbRender();
    };

    window.lightboxPrev = function () {
        if (!lbUrls.length) return;
        lbIdx = (lbIdx - 1 + lbUrls.length) % lbUrls.length;
        lbRender();
    };

    if (lbEl) {
        if (lbClose) lbClose.addEventListener('click', window.closeLightbox);
        if (lbPrev) lbPrev.addEventListener('click', window.lightboxPrev);
        if (lbNext) lbNext.addEventListener('click', window.lightboxNext);

        lbEl.addEventListener('click', function (e) {
            if (e.target === lbEl) window.closeLightbox();
        });
    }

    var gallery = document.getElementById('aptGallery');
    if (gallery) {
        gallery.querySelectorAll('[data-lightbox-index]').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                var idx = parseInt(btn.dataset.lightboxIndex, 10) || 0;
                window.openLightbox(idx);
            });
        });
    }

    document.addEventListener('keydown', function (e) {
        if (lbEl && lbEl.classList.contains('is-open')) {
            if (e.key === 'Escape') { window.closeLightbox(); return; }
            if (e.key === 'ArrowRight') { window.lightboxNext(); return; }
            if (e.key === 'ArrowLeft') { window.lightboxPrev(); return; }
        }
        if (e.key === 'Escape') {
            window.closeScheduleModal();
        }
    });
})();
</script>
@endpush