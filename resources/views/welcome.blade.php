@extends('layouts.app')
@section('title', 'Find your home in Massachusetts')

@push('styles')
<style>
    .home-hero {
        background: linear-gradient(135deg, #0a0a0a 0%, #16203d 100%);
        color: white;
        padding: 5rem 2rem 6rem;
        margin: -2rem -2rem 0 -2rem;
        position: relative;
        overflow: hidden;
    }
    .home-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 600px;
        height: 600px;
        background: radial-gradient(circle, rgba(55, 79, 167, 0.22) 0%, transparent 70%);
        pointer-events: none;
    }
    .home-hero-inner {
        max-width: 1200px;
        margin: 0 auto;
        position: relative;
        z-index: 2;
    }
    .home-hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.6);
        margin-bottom: 1.5rem;
    }
    .home-hero-eyebrow::before {
        content: '';
        width: 24px;
        height: 1px;
        background: rgba(255, 255, 255, 0.4);
    }
    .home-hero-title {
        font-family: var(--font-display);
        font-size: clamp(2.5rem, 6vw, 4.5rem);
        font-weight: 700;
        line-height: 1.05;
        letter-spacing: -0.035em;
        margin-bottom: 1.5rem;
        max-width: 900px;
    }
    .home-hero-title em {
        font-style: italic;
        color: #7d92e0;
    }
    .home-hero-subtitle {
        font-size: clamp(1rem, 1.5vw, 1.15rem);
        color: rgba(255, 255, 255, 0.7);
        line-height: 1.6;
        max-width: 640px;
        margin-bottom: 2.5rem;
    }
    .home-hero-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    .btn-hero-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.95rem 1.75rem;
        background: white;
        color: #0a0a0a;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.92rem;
        text-decoration: none;
        transition: all 0.2s;
        letter-spacing: 0.01em;
    }
    .btn-hero-primary:hover {
        background: #f5f5f5;
        color: #0a0a0a;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .btn-hero-secondary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.95rem 1.75rem;
        background: transparent;
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 6px;
        font-weight: 600;
        font-size: 0.92rem;
        text-decoration: none;
        transition: all 0.2s;
    }
    .btn-hero-secondary:hover {
        background: rgba(255, 255, 255, 0.08);
        color: white;
        text-decoration: none;
        border-color: rgba(255, 255, 255, 0.5);
    }

    .home-hero-stats {
        display: flex;
        gap: 3rem;
        margin-top: 4rem;
        padding-top: 2rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        flex-wrap: wrap;
    }
    .home-hero-stat-value {
        font-family: var(--font-display);
        font-size: 1.85rem;
        font-weight: 700;
        letter-spacing: -0.02em;
        line-height: 1;
        margin-bottom: 0.35rem;
    }
    .home-hero-stat-label {
        font-size: 0.78rem;
        color: rgba(255, 255, 255, 0.55);
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-weight: 500;
    }

    .home-section {
        padding: 5rem 0;
        max-width: 1200px;
        margin: 0 auto;
        padding-left: 2rem;
        padding-right: 2rem;
    }
    .home-section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 3rem;
        gap: 2rem;
        flex-wrap: wrap;
    }
    .home-section-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        font-weight: 600;
        color: var(--brand-secondary);
        margin-bottom: 0.75rem;
    }
    .home-section-eyebrow::before {
        content: '';
        width: 24px;
        height: 1px;
        background: var(--brand-secondary);
    }
    .home-section-title {
        font-family: var(--font-display);
        font-size: clamp(1.85rem, 3vw, 2.5rem);
        font-weight: 700;
        letter-spacing: -0.03em;
        line-height: 1.15;
        color: var(--brand-primary);
        max-width: 600px;
    }
    .home-section-title em {
        font-style: italic;
        color: var(--brand-secondary);
    }

    /* ═══════════════════════════════════════════════════════
       PROCESS CAROUSEL
       ═══════════════════════════════════════════════════════ */
    .process-progress {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 2.5rem;
        gap: 0.5rem;
    }
    .process-progress-step {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1rem;
        background: white;
        border: 1px solid var(--border);
        border-radius: 9999px;
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
        color: var(--brand-muted);
        font-size: 0.83rem;
        font-weight: 500;
        flex-shrink: 0;
    }
    .process-progress-step:hover {
        border-color: var(--brand-primary);
        color: var(--brand-primary);
    }
    .process-progress-step.active {
        background: var(--brand-secondary);
        border-color: var(--brand-secondary);
        color: white;
    }
    .process-progress-step.done {
        border-color: var(--brand-secondary);
        color: var(--brand-secondary);
    }
    .process-progress-num {
        font-weight: 700;
        font-size: 0.72rem;
        letter-spacing: 0.05em;
    }
    .process-progress-label {
        font-size: 0.82rem;
        font-weight: 600;
    }
    .process-progress-line {
        flex: 1;
        height: 1px;
        background: var(--border);
        min-width: 12px;
    }

    .process-carousel {
        position: relative;
    }
    .process-track-wrap {
        position: relative;
        overflow: hidden;
        border-radius: 16px;
    }
    .process-track {
        display: flex;
        transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        will-change: transform;
    }
    .process-slide {
        min-width: 100%;
        padding: 0;
    }
    .process-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 4rem 3rem;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 380px;
        justify-content: center;
    }
    .process-card-icon {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: rgba(55, 79, 167, 0.08);
        color: var(--brand-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        margin-bottom: 1.5rem;
    }
    .process-card-num {
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.15em;
        font-weight: 700;
        color: var(--brand-muted);
        margin-bottom: 0.75rem;
    }
    .process-card-title {
        font-family: var(--font-display);
        font-size: clamp(1.5rem, 2.5vw, 2rem);
        font-weight: 700;
        letter-spacing: -0.025em;
        color: var(--brand-primary);
        margin-bottom: 1rem;
        max-width: 600px;
    }
    .process-card-desc {
        font-size: 1rem;
        color: var(--brand-muted);
        line-height: 1.65;
        max-width: 560px;
        margin: 0 auto 2rem;
    }
    .btn-process {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.85rem 1.75rem;
        background: var(--brand-secondary);
        color: white;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.2s;
        letter-spacing: 0.01em;
    }
    .btn-process:hover {
        background: #2a3d85;
        color: white;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: 0 8px 20px rgba(55, 79, 167, 0.25);
    }

    .process-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: var(--brand-secondary);
        border: 2px solid white;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        z-index: 5;
        box-shadow: 0 8px 24px rgba(55, 79, 167, 0.4), 0 2px 6px rgba(0, 0, 0, 0.15);
        font-size: 1.35rem;
    }
    .process-nav:hover {
        background: #2a3d85;
        border-color: white;    
        color: white;
        transform: translateY(-50%) scale(1.08);
    }
    .process-nav.prev { left: -28px; }
    .process-nav.next { right: -28px; }

    .process-dots {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1.5rem;
    }
    .process-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--border-strong);
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        padding: 0;
    }
    .process-dot.active {
        background: var(--brand-secondary);
        width: 24px;
        border-radius: 4px;
    }

    .home-why-section {
        background: #fafafa;
        border-top: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
    }
    .home-why-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 2rem;
        margin-top: 3rem;
    }
    .home-why-card {
        padding: 2rem 1.5rem;
        background: white;
        border: 1px solid var(--border);
        border-radius: 12px;
    }
    .home-why-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        background: rgba(55, 79, 167, 0.08);
        color: var(--brand-secondary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        margin-bottom: 1.25rem;
    }
    .home-why-title {
        font-family: var(--font-display);
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--brand-primary);
        margin-bottom: 0.5rem;
    }
    .home-why-desc {
        font-size: 0.88rem;
        color: var(--brand-muted);
        line-height: 1.6;
        margin: 0;
    }

    .testimonials-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
    }
    .testimonial-card {
        background: white;
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 2rem;
        display: flex;
        flex-direction: column;
    }
    .testimonial-stars {
        color: #f59e0b;
        font-size: 0.85rem;
        margin-bottom: 1rem;
        letter-spacing: 0.1em;
    }
    .testimonial-quote {
        font-family: var(--font-display);
        font-size: 0.95rem;
        color: var(--brand-primary);
        line-height: 1.65;
        margin-bottom: 1.5rem;
        flex: 1;
        font-style: italic;
    }
    .testimonial-author {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-top: 1.25rem;
        border-top: 1px solid var(--border);
    }
    .testimonial-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--brand-secondary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.9rem;
        flex-shrink: 0;
    }
    .testimonial-author-info { display: flex; flex-direction: column; }
    .testimonial-author-name { font-size: 0.88rem; font-weight: 600; color: var(--brand-primary); }
    .testimonial-author-role { font-size: 0.75rem; color: var(--brand-muted); }

    .home-cta {
        background: linear-gradient(135deg, #0a0a0a 0%, #16203d 100%);
        color: white;
        padding: 5rem 2rem;
        text-align: center;
        border-radius: 16px;
        margin: 0 2rem 3rem;
    }
    .home-cta-inner {
        max-width: 640px;
        margin: 0 auto;
    }
    .home-cta h2 {
        font-family: var(--font-display);
        font-size: clamp(1.85rem, 3.5vw, 2.75rem);
        font-weight: 700;
        letter-spacing: -0.03em;
        line-height: 1.15;
        margin-bottom: 1rem;
    }
    .home-cta h2 em {
        font-style: italic;
        color: #7d92e0;
    }
    .home-cta p {
        font-size: 1rem;
        color: rgba(255, 255, 255, 0.7);
        line-height: 1.6;
        margin-bottom: 2rem;
    }

    @media (max-width: 768px) {
        .home-hero { padding: 3.5rem 1.25rem 4rem; margin: -1rem -1rem 0 -1rem; }
        .home-hero-title { font-size: 2.25rem; }
        .home-hero-stats { gap: 2rem; margin-top: 2.5rem; }
        .home-hero-stat-value { font-size: 1.35rem; }
        .home-section { padding: 3rem 1.25rem; }
        .home-section-header { margin-bottom: 2rem; }
        .testimonial-card { padding: 1.5rem; }
        .home-cta { margin: 0 1rem 2rem; padding: 3rem 1.25rem; }

        .process-card { padding: 2.5rem 1.5rem; min-height: 320px; }
        .process-card-icon { width: 64px; height: 64px; font-size: 1.75rem; margin-bottom: 1rem; }
        .process-progress-label { display: none; }
        .process-progress-step { padding: 0.55rem 0.75rem; }
        .process-progress-line { display: none; }
        .process-nav { width: 36px; height: 36px; font-size: 0.85rem; }
        .process-nav.prev { left: -8px; }
        .process-nav.next { right: -8px; }
    }
</style>
@endpush

@section('content')

<section class="home-hero">
    <div class="home-hero-inner">
        <div class="home-hero-eyebrow"> Massachusetts</div>
        <h1 class="home-hero-title">
            Find your next home<br>
            in <em>Massachusetts</em>
        </h1>
        <p class="home-hero-subtitle">
            Thoughtfully curated apartments, responsive support, and a renting experience that actually feels human. Browse our available listings and find the place that fits.
        </p>
        <div class="home-hero-actions">
            <a href="{{ route('apartments.index') }}" class="btn-hero-primary">
                Browse apartments <i class="bi bi-arrow-right"></i>
            </a>
            @guest
                <a href="{{ route('register') }}" class="btn-hero-secondary">
                    Create an account
                </a>
            @else
                <a href="{{ route('profile.edit') }}" class="btn-hero-secondary">
                    My profile
                </a>
            @endguest
        </div>

        <div class="home-hero-stats">
            <div>
                <div class="home-hero-stat-value">{{ $properties->count() }}</div>
                <div class="home-hero-stat-label">Available listings</div>
            </div>
            <div>
                <div class="home-hero-stat-value">{{ $categories->count() }}</div>
                <div class="home-hero-stat-label">Property categories</div>
            </div>
            <div>
                <div class="home-hero-stat-value">24h</div>
                <div class="home-hero-stat-label">Response time</div>
            </div>
        </div>
    </div>
</section>

<section class="home-section" id="howItWorks">
    <div class="home-section-header">
        <div>
            <div class="home-section-eyebrow">How it works</div>
            <h2 class="home-section-title">
                From browsing to <em>move-in</em>
            </h2>
        </div>
    </div>

    <div class="process-carousel">
        <div class="process-progress">
            <button type="button" class="process-progress-step active" data-step="0">
                <span class="process-progress-num">01</span>
                <span class="process-progress-label">Browse</span>
            </button>
            <div class="process-progress-line"></div>
            <button type="button" class="process-progress-step" data-step="1">
                <span class="process-progress-num">02</span>
                <span class="process-progress-label">Qualify</span>
            </button>
            <div class="process-progress-line"></div>
            <button type="button" class="process-progress-step" data-step="2">
                <span class="process-progress-num">03</span>
                <span class="process-progress-label">Apply</span>
            </button>
            <div class="process-progress-line"></div>
            <button type="button" class="process-progress-step" data-step="3">
                <span class="process-progress-num">04</span>
                <span class="process-progress-label">Get approved</span>
            </button>
        </div>

        <div class="process-track-wrap">
            <div class="process-track" id="processTrack">

                <div class="process-slide">
                    <div class="process-card">
                        <div class="process-card-icon"><i class="bi bi-search"></i></div>
                        <div class="process-card-num">Step 01 of 04</div>
                        <h3 class="process-card-title">Browse our apartments</h3>
                        <p class="process-card-desc">
                            Explore all our available listings and find the property that fits your needs. Filter by location, category, and price.
                        </p>
                        <a href="{{ route('apartments.index') }}" class="btn-process">
                            Browse apartments <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>

                <div class="process-slide">
                    <div class="process-card">
                        <div class="process-card-icon"><i class="bi bi-clipboard-check"></i></div>
                        <div class="process-card-num">Step 02 of 04</div>
                        <h3 class="process-card-title">Complete a qualification</h3>
                        <p class="process-card-desc">
                            Fill out a quick qualification form so we can match you with properties that fit your budget and rental history. It only takes a few minutes.
                        </p>
                        @auth
                            <a href="{{ route('qualifications.index') }}" class="btn-process">
                                Start qualification <i class="bi bi-arrow-right"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-process">
                                Sign in to start <i class="bi bi-arrow-right"></i>
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="process-slide">
                    <div class="process-card">
                        <div class="process-card-icon"><i class="bi bi-file-earmark-text"></i></div>
                        <div class="process-card-num">Step 03 of 04</div>
                        <h3 class="process-card-title">Submit an application</h3>
                        <p class="process-card-desc">
                            Once your qualification is on file, submit a full rental application for the property you're interested in. All the details in one place.
                        </p>
                        @auth
                            <a href="{{ route('applications.index') }}" class="btn-process">
                                Start application <i class="bi bi-arrow-right"></i>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-process">
                                Sign in to apply <i class="bi bi-arrow-right"></i>
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="process-slide">
                    <div class="process-card">
                        <div class="process-card-icon"><i class="bi bi-check-circle"></i></div>
                        <div class="process-card-num">Step 04 of 04</div>
                        <h3 class="process-card-title">Get approved</h3>
                        <p class="process-card-desc">
                            Our team reviews your application and reaches out to schedule a showing. Once approved, you're one step away from move-in.
                        </p>
                        @auth
                            <a href="{{ route('applications.index') }}" class="btn-process">
                                Check my application <i class="bi bi-arrow-right"></i>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn-process">
                                Create an account <i class="bi bi-arrow-right"></i>
                            </a>
                        @endauth
                    </div>
                </div>

            </div>

            <button type="button" class="process-nav prev" id="processPrev" aria-label="Previous step">
                <i class="bi bi-arrow-left"></i>
            </button>
            <button type="button" class="process-nav next" id="processNext" aria-label="Next step">
                <i class="bi bi-arrow-right"></i>
            </button>
        </div>

        <div class="process-dots" id="processDots">
            <button type="button" class="process-dot active" data-step="0"></button>
            <button type="button" class="process-dot" data-step="1"></button>
            <button type="button" class="process-dot" data-step="2"></button>
            <button type="button" class="process-dot" data-step="3"></button>
        </div>
    </div>
</section>

<section class="home-why-section">
    <div class="home-section">
        <div class="home-section-header">
            <div>
                <div class="home-section-eyebrow">Why JCM Realty Group</div>
                <h2 class="home-section-title">
                    A rental experience<br>
                    built for <em>real people</em>
                </h2>
            </div>
        </div>

        <div class="home-why-grid">
            <div class="home-why-card">
                <div class="home-why-icon"><i class="bi bi-house-check"></i></div>
                <h3 class="home-why-title">Curated listings</h3>
                <p class="home-why-desc">Every property is vetted before it goes live. No bait-and-switch, no hidden surprises.</p>
            </div>
            <div class="home-why-card">
                <div class="home-why-icon"><i class="bi bi-chat-dots"></i></div>
                <h3 class="home-why-title">Real human support</h3>
                <p class="home-why-desc">Questions about a listing? Reach out directly and hear back from a real person within a day.</p>
            </div>
            <div class="home-why-card">
                <div class="home-why-icon"><i class="bi bi-shield-check"></i></div>
                <h3 class="home-why-title">Transparent process</h3>
                <p class="home-why-desc">Clear pricing, honest timelines, and a straightforward qualification process.</p>
            </div>
            <div class="home-why-card">
                <div class="home-why-icon"><i class="bi bi-geo-alt"></i></div>
                <h3 class="home-why-title">Local expertise</h3>
                <p class="home-why-desc">We know Massachusetts and the surrounding neighborhoods — and we help you find the right fit.</p>
            </div>
        </div>
    </div>
</section>

@php
    $testimonials = [
        [
            'quote' => 'The team made finding a place feel easy. They responded fast, answered every question, and never once made me feel rushed.',
            'name'  => 'Sarah M.',
            'role'  => 'Rented in Lynn',
        ],
        [
            'quote' => 'I was worried about the qualification process, but they walked me through every step. Transparent, patient, and genuinely helpful.',
            'name'  => 'James T.',
            'role'  => 'Rented in Chelsea',
        ],
        [
            'quote' => 'They helped me find a place in under a week. I would recommend JCM Realty Group to anyone looking for an apartment in Massachusetts.',
            'name'  => 'Priya K.',
            'role'  => 'Rented in New Boston',
        ],
    ];
@endphp

<section class="home-section">
    <div class="home-section-header">
        <div>
            <div class="home-section-eyebrow">Reviews</div>
            <h2 class="home-section-title">
                What our <em>tenants</em> say
            </h2>
        </div>
    </div>

    <div class="testimonials-grid">
        @foreach($testimonials as $testimonial)
            <div class="testimonial-card">
                <div class="testimonial-stars">
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                    <i class="bi bi-star-fill"></i>
                </div>
                <blockquote class="testimonial-quote">
                    "{{ $testimonial['quote'] }}"
                </blockquote>
                <div class="testimonial-author">
                    <div class="testimonial-avatar">{{ strtoupper(substr($testimonial['name'], 0, 1)) }}</div>
                    <div class="testimonial-author-info">
                        <span class="testimonial-author-name">{{ $testimonial['name'] }}</span>
                        <span class="testimonial-author-role">{{ $testimonial['role'] }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>

<section class="home-cta">
    <div class="home-cta-inner">
        <h2>Ready to find your <em>next home?</em></h2>
        <p>Browse our available apartments or reach out to our team — we're here to help you find the right place.</p>
        <a href="{{ route('apartments.index') }}" class="btn-hero-primary">
            Browse apartments <i class="bi bi-arrow-right"></i>
        </a>
    </div>
</section>

@endsection

@push('scripts')
<script>
(function () {
    const track = document.getElementById('processTrack');
    if (!track) return;

    const slides = track.querySelectorAll('.process-slide');
    const dots = document.querySelectorAll('.process-dot');
    const progressSteps = document.querySelectorAll('.process-progress-step');
    const prevBtn = document.getElementById('processPrev');
    const nextBtn = document.getElementById('processNext');
    const carousel = track.closest('.process-carousel');

    const total = slides.length;
    let current = 0;
    let autoplayTimer;

    function goTo(index) {
        if (index < 0) index = total - 1;
        if (index >= total) index = 0;
        current = index;
        track.style.transform = 'translateX(-' + (current * 100) + '%)';

        dots.forEach((d, i) => d.classList.toggle('active', i === current));
        progressSteps.forEach((p, i) => {
            p.classList.toggle('active', i === current);
            p.classList.toggle('done', i < current);
        });
    }

    function startAutoplay() {
        autoplayTimer = setInterval(() => goTo(current + 1), 6000);
    }
    function stopAutoplay() {
        clearInterval(autoplayTimer);
    }
    function resetAutoplay() {
        stopAutoplay();
        startAutoplay();
    }

    if (prevBtn) prevBtn.addEventListener('click', () => { goTo(current - 1); resetAutoplay(); });
    if (nextBtn) nextBtn.addEventListener('click', () => { goTo(current + 1); resetAutoplay(); });

    dots.forEach((dot) => {
        dot.addEventListener('click', () => {
            goTo(parseInt(dot.dataset.step, 10));
            resetAutoplay();
        });
    });

    progressSteps.forEach((step) => {
        step.addEventListener('click', () => {
            goTo(parseInt(step.dataset.step, 10));
            resetAutoplay();
        });
    });

    if (carousel) {
        carousel.addEventListener('mouseenter', stopAutoplay);
        carousel.addEventListener('mouseleave', startAutoplay);
    }

    let touchStartX = 0;
    track.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    track.addEventListener('touchend', (e) => {
        const diff = touchStartX - e.changedTouches[0].screenX;
        if (Math.abs(diff) > 50) {
            goTo(current + (diff > 0 ? 1 : -1));
            resetAutoplay();
        }
    }, { passive: true });

    goTo(0);
    startAutoplay();
})();
</script>
@endpush