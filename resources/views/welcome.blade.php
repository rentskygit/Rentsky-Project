@extends('layouts.app')

@section('title', 'RentSky | Rentals in Boston')

@section('content')
<style>
    /* ── Estilos específicos de la landing page ── */
    .landing-hero {
        margin: -2rem -2rem 2rem -2rem;
        padding: 0;
        background: linear-gradient(135deg, #0F1B2D 0%, #1B2E4B 100%);
        color: white;
        position: relative;
        overflow: hidden;
        border-radius: 1rem;
    }

    .hero-content {
        max-width: 1200px;
        margin: 0 auto;
        padding: 4rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2rem;
        position: relative;
        z-index: 2;
    }

    .hero-text h1 {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 1rem;
        line-height: 1.2;
    }

    .hero-text h1 span {
        color: var(--brand-secondary);
    }

    .hero-text p {
        font-size: 1.2rem;
        color: #b8c4d6;
        margin-bottom: 2rem;
        max-width: 500px;
    }

    .hero-badge {
        display: inline-block;
        background: var(--brand-secondary);
        color: white;
        padding: 0.3rem 1rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 600;
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .hero-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn-hero-primary {
        display: inline-block;
        background: var(--brand-secondary);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 9999px;
        font-weight: 700;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-hero-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 48, 3, 0.35);
    }

    .btn-hero-outline {
        display: inline-block;
        background: transparent;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 9999px;
        font-weight: 700;
        border: 2px solid rgba(255,255,255,0.4);
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-hero-outline:hover {
        background: rgba(255,255,255,0.1);
        border-color: white;
    }

    .hero-image {
        flex-shrink: 0;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 8rem;
        color: var(--brand-secondary);
        border: 2px solid rgba(245, 48, 3, 0.2);
    }

    /* ── Carrusel ── */
    .carousel-container {
        position: relative;
        margin-bottom: 3rem;
        border-radius: 1rem;
        overflow: hidden;
        background: white;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: 1px solid #e5e7eb;
    }

    .carousel-track {
        display: flex;
        transition: transform 0.5s ease-in-out;
        will-change: transform;
    }

    .carousel-slide {
        min-width: 100%;
        display: flex;
        align-items: center;
        padding: 2rem 3rem;
        gap: 2rem;
        background: white;
    }

    .carousel-slide .slide-content {
        flex: 1;
    }

    .carousel-slide .slide-content h2 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--brand-primary);
        margin-bottom: 0.5rem;
    }

    .carousel-slide .slide-content p {
        color: var(--brand-muted);
        margin-bottom: 1rem;
    }

    .carousel-slide .slide-content .slide-tag {
        display: inline-block;
        padding: 0.25rem 1rem;
        background: var(--brand-secondary);
        color: white;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .carousel-slide .slide-image {
        flex-shrink: 0;
        width: 200px;
        height: 200px;
        background: #f3f4f6;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 5rem;
        color: var(--brand-muted);
    }

    .carousel-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        transition: all 0.2s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        font-size: 1.2rem;
        border: none;
        color: var(--brand-primary);
    }

    .carousel-btn:hover {
        background: var(--brand-secondary);
        color: white;
    }

    .carousel-btn.prev {
        left: 1rem;
    }

    .carousel-btn.next {
        right: 1rem;
    }

    .carousel-dots {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        padding: 1rem 0;
        background: white;
    }

    .carousel-dots .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #d1d5db;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        padding: 0;
    }

    .carousel-dots .dot.active {
        background: var(--brand-secondary);
        width: 24px;
        border-radius: 4px;
    }

    /* ── Sección de tarjetas (Ofertas, Liquidaciones, etc.) ── */
    .section-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--brand-primary);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .section-title span {
        color: var(--brand-secondary);
    }

    .section-subtitle {
        color: var(--brand-muted);
        margin-bottom: 1.5rem;
        font-size: 1rem;
    }

    .cards-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .card-item {
        background: white;
        border-radius: 0.75rem;
        border: 1px solid #e5e7eb;
        overflow: hidden;
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        color: inherit;
        display: block;
    }

    .card-item:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        border-color: var(--brand-secondary);
    }

    .card-item .card-image {
        width: 100%;
        height: 180px;
        background: #f9fafb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4rem;
        color: var(--brand-muted);
        position: relative;
    }

    .card-item .card-badge {
        position: absolute;
        top: 0.75rem;
        right: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .card-badge.sale {
        background: #fee2e2;
        color: #991b1b;
    }

    .card-badge.liquidation {
        background: #fef3c7;
        color: #92400e;
    }

    .card-badge.new {
        background: #dbeafe;
        color: #1e40af;
    }

    .card-badge.hot {
        background: #fce4ec;
        color: #c62828;
    }

    .card-item .card-info {
        padding: 1rem;
    }

    .card-item .card-name {
        font-weight: 600;
        color: var(--brand-primary);
        margin-bottom: 0.25rem;
        font-size: 0.95rem;
    }

    .card-item .card-category {
        font-size: 0.8rem;
        color: var(--brand-muted);
        margin-bottom: 0.5rem;
    }

    .card-item .card-price {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .card-item .card-price .current {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--brand-secondary);
    }

    .card-item .card-price .original {
        font-size: 0.85rem;
        color: var(--brand-muted);
        text-decoration: line-through;
    }

    .card-item .card-price .discount {
        font-size: 0.75rem;
        font-weight: 600;
        color: #16a34a;
        background: #dcfce7;
        padding: 0.1rem 0.5rem;
        border-radius: 9999px;
    }

    .card-item .btn-add-cart {
        width: 100%;
        margin-top: 0.75rem;
        padding: 0.4rem;
        background: var(--brand-secondary);
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .card-item .btn-add-cart:hover {
        background: #d42b02;
        transform: scale(1.02);
    }

    .view-all-link {
        display: inline-block;
        color: var(--brand-secondary);
        font-weight: 600;
        margin-top: 0.5rem;
        transition: color 0.2s ease;
    }

    .view-all-link:hover {
        color: #d42b02;
        text-decoration: underline;
    }

    /* ── Categorías ── */
    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .category-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.75rem;
        padding: 1.5rem 1rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        color: var(--brand-primary);
        display: block;
    }

    .category-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        border-color: var(--brand-secondary);
    }

    .category-card .category-icon {
        font-size: 2.5rem;
        display: block;
        margin-bottom: 0.5rem;
    }

    .category-card .category-name {
        font-weight: 600;
        font-size: 0.95rem;
    }

    .category-card .category-count {
        font-size: 0.8rem;
        color: var(--brand-muted);
    }

    /* ── Responsive ── */
    @media (max-width: 1024px) {
        .hero-content {
            padding: 3rem 1.5rem;
        }

        .hero-text h1 {
            font-size: 2.5rem;
        }

        .hero-image {
            width: 250px;
            height: 250px;
            font-size: 6rem;
        }

        .cards-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }
    }

    @media (max-width: 768px) {
        .landing-hero {
            margin: -1rem -1rem 2rem -1rem;
            border-radius: 0.5rem;
        }

        .hero-content {
            flex-direction: column;
            text-align: center;
            padding: 2.5rem 1.5rem;
            gap: 1.5rem;
        }

        .hero-text h1 {
            font-size: 2.2rem;
        }

        .hero-text p {
            max-width: 100%;
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }

        .hero-actions {
            justify-content: center;
        }

        .hero-image {
            width: 180px;
            height: 180px;
            font-size: 4.5rem;
        }

        .carousel-slide {
            flex-direction: column;
            text-align: center;
            padding: 1.5rem 1.5rem 2rem;
            gap: 1rem;
        }

        .carousel-slide .slide-image {
            width: 140px;
            height: 140px;
            font-size: 3.5rem;
        }

        .carousel-slide .slide-content h2 {
            font-size: 1.5rem;
        }

        .carousel-btn {
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
        }

        .carousel-btn.prev {
            left: 0.5rem;
        }

        .carousel-btn.next {
            right: 0.5rem;
        }

        .section-title {
            font-size: 1.5rem;
        }

        .cards-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }

        .card-item .card-image {
            height: 150px;
            font-size: 3rem;
        }

        .categories-grid {
            grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            gap: 1rem;
        }

        .category-card {
            padding: 1.25rem 0.75rem;
        }

        .category-card .category-icon {
            font-size: 2rem;
        }
    }

    @media (max-width: 480px) {
        .hero-content {
            padding: 2rem 1rem;
        }

        .hero-text h1 {
            font-size: 1.8rem;
        }

        .hero-image {
            width: 140px;
            height: 140px;
            font-size: 3.5rem;
        }

        .hero-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.75rem;
        }

        .btn-hero-primary,
        .btn-hero-outline {
            padding: 0.6rem 1.5rem;
            font-size: 0.9rem;
        }

        .carousel-slide {
            padding: 1rem 1rem 1.5rem;
        }

        .carousel-slide .slide-image {
            width: 100px;
            height: 100px;
            font-size: 2.5rem;
        }

        .carousel-slide .slide-content h2 {
            font-size: 1.2rem;
        }

        .carousel-slide .slide-content p {
            font-size: 0.9rem;
        }

        .carousel-slide .slide-content .slide-tag {
            font-size: 0.7rem;
            padding: 0.15rem 0.75rem;
        }

        .cards-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        .card-item .card-image {
            height: 120px;
            font-size: 2.5rem;
        }

        .card-item .card-info {
            padding: 0.75rem;
        }

        .card-item .card-name {
            font-size: 0.85rem;
        }

        .card-item .card-price .current {
            font-size: 0.95rem;
        }

        .card-item .card-price .original {
            font-size: 0.75rem;
        }

        .card-item .card-price .discount {
            font-size: 0.65rem;
        }

        .card-item .btn-add-cart {
            font-size: 0.75rem;
            padding: 0.3rem;
        }

        .categories-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
        }

        .category-card {
            padding: 1rem 0.5rem;
        }

        .category-card .category-icon {
            font-size: 1.5rem;
        }

        .category-card .category-name {
            font-size: 0.8rem;
        }

        .category-card .category-count {
            font-size: 0.7rem;
        }

        .section-title {
            font-size: 1.2rem;
        }

        .section-subtitle {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 360px) {
        .cards-grid {
            grid-template-columns: 1fr 1fr;
            gap: 0.5rem;
        }

        .card-item .card-image {
            height: 100px;
            font-size: 2rem;
        }

        .card-item .card-info {
            padding: 0.5rem;
        }

        .card-item .card-name {
            font-size: 0.75rem;
        }

        .categories-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<!-- HERO -->
<section class="landing-hero">
    <div class="hero-content">
        <div class="hero-text">
            <span class="hero-badge">🏙️ New in Boston</span>
            <h1>Your next <span>home</span> in Boston</h1>
            <p>Discover apartments, lofts, and brownstones for rent across Boston's most vibrant neighborhoods.</p>
            <div class="hero-actions">
                <a href="/products" class="btn-hero-primary">Browse Rentals</a>
                <!-- <a href="/login" class="btn-hero-outline">Sign In</a> -->
            </div>
        </div>
        <div class="hero-image">
            🏙️
        </div>
    </div>
</section>

<!-- CARRUSEL -->
<div class="carousel-container" id="mainCarousel">
    <div class="carousel-track" id="carouselTrack">
        <div class="carousel-slide">
            <div class="slide-content">
                <span class="slide-tag">⚡ Move-in Ready</span>
                <h2>Rentals available right now</h2>
                <p>Skip the wait. These Boston apartments are ready for immediate move-in — don't miss them.</p>
                <a href="/products?filter=flash" class="btn btn-sm" style="display:inline-block;background:var(--brand-secondary);color:white;padding:0.5rem 1.5rem;border-radius:9999px;font-weight:600;transition:all 0.2s ease;">Explore</a>
            </div>
            <div class="slide-image">⚡</div>
        </div>
        <div class="carousel-slide">
            <div class="slide-content">
                <span class="slide-tag">🏙️ Studios</span>
                <h2>Studios in Downtown Boston</h2>
                <p>Compact, modern studios in the heart of the city. Perfect for students and young professionals.</p>
                <a href="/products?filter=studios" class="btn btn-sm" style="display:inline-block;background:var(--brand-secondary);color:white;padding:0.5rem 1.5rem;border-radius:9999px;font-weight:600;transition:all 0.2s ease;">View Studios</a>
            </div>
            <div class="slide-image">🏢</div>
        </div>
        <div class="carousel-slide">
            <div class="slide-content">
                <span class="slide-tag">🆕 For Families</span>
                <h2>Family homes in Brookline</h2>
                <p>Spacious homes near top-rated schools, parks, and quiet residential streets.</p>
                <a href="/products?filter=family" class="btn btn-sm" style="display:inline-block;background:var(--brand-secondary);color:white;padding:0.5rem 1.5rem;border-radius:9999px;font-weight:600;transition:all 0.2s ease;">Explore</a>
            </div>
            <div class="slide-image">🏡</div>
        </div>
    </div>
    <button class="carousel-btn prev" id="prevBtn">‹</button>
    <button class="carousel-btn next" id="nextBtn">›</button>
    <div class="carousel-dots" id="carouselDots"></div>
</div>

<!-- SECCIONES DE TARJETAS -->
<!-- Featured Rentals -->
<div>
    <div class="section-title">
        🔥 <span>Featured</span> Rentals
    </div>
    <p class="section-subtitle">Handpicked Boston apartments you'll love.</p>
    <div class="cards-grid" id="ofertas-grid">
        <!-- Cards generadas por JavaScript -->
    </div>
    <a href="/products?filter=sale" class="view-all-link">View all featured →</a>
</div>

<!-- Price Drops -->
<div style="margin-top: 2.5rem;">
    <div class="section-title">
        📉 <span>Price</span> Drops
    </div>
    <p class="section-subtitle">Recently reduced rents across Boston. Limited availability.</p>
    <div class="cards-grid" id="liquidaciones-grid">
        <!-- Cards generadas por JavaScript -->
    </div>
    <a href="/products?filter=liquidation" class="view-all-link">See all price drops →</a>
</div>

<!-- New Listings -->
<div style="margin-top: 2.5rem;">
    <div class="section-title">
        🆕 <span>New</span> Listings
    </div>
    <p class="section-subtitle">Fresh on the market this week in Boston.</p>
    <div class="cards-grid" id="nuevos-grid">
        <!-- Cards generadas por JavaScript -->
    </div>
    <a href="/products?filter=new" class="view-all-link">See all new listings →</a>
</div>

<!-- Most Popular -->
<div style="margin-top: 2.5rem;">
    <div class="section-title">
        ⭐ <span>Most</span> Popular
    </div>
    <p class="section-subtitle">The most-viewed rentals in Boston this month.</p>
    <div class="cards-grid" id="vendidos-grid">
        <!-- Cards generadas por JavaScript -->
    </div>
    <a href="/products?filter=bestseller" class="view-all-link">See what's trending →</a>
</div>

<!-- CATEGORÍAS -->
<div style="margin-top: 3rem;">
    <div class="section-title">
        📂 <span>Browse by</span> Bedrooms
    </div>
    <p class="section-subtitle">Find the perfect layout for your lifestyle.</p>
    <div class="categories-grid" id="categoriesGrid">
        <!-- Categorías generadas por JavaScript -->
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── CARRUSEL ──
    const track = document.getElementById('carouselTrack');
    const slides = track.querySelectorAll('.carousel-slide');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const dotsContainer = document.getElementById('carouselDots');
    let currentIndex = 0;
    const totalSlides = slides.length;

    // Crear dots
    slides.forEach((_, i) => {
        const dot = document.createElement('button');
        dot.className = 'dot' + (i === 0 ? ' active' : '');
        dot.dataset.index = i;
        dot.addEventListener('click', () => goToSlide(i));
        dotsContainer.appendChild(dot);
    });

    const dots = dotsContainer.querySelectorAll('.dot');

    function goToSlide(index) {
        if (index < 0) index = totalSlides - 1;
        if (index >= totalSlides) index = 0;
        currentIndex = index;
        track.style.transform = `translateX(-${currentIndex * 100}%)`;
        dots.forEach((dot, i) => {
            dot.classList.toggle('active', i === currentIndex);
        });
    }

    prevBtn.addEventListener('click', () => goToSlide(currentIndex - 1));
    nextBtn.addEventListener('click', () => goToSlide(currentIndex + 1));

    // Auto-play
    let autoPlay = setInterval(() => goToSlide(currentIndex + 1), 5000);

    // Pausar auto-play en hover
    const carousel = document.getElementById('mainCarousel');
    carousel.addEventListener('mouseenter', () => clearInterval(autoPlay));
    carousel.addEventListener('mouseleave', () => {
        clearInterval(autoPlay);
        autoPlay = setInterval(() => goToSlide(currentIndex + 1), 5000);
    });

    // ── DATOS DE EJEMPLO (Boston Rentals) ──
    const categories = [
        { id: 1, name: 'Studio', icon: '🏢', count: 42 },
        { id: 2, name: '1 Bedroom', icon: '🛏️', count: 28 },
        { id: 3, name: '2 Bedrooms', icon: '🏠', count: 35 },
        { id: 4, name: '3 Bedrooms', icon: '🏡', count: 19 },
        { id: 5, name: '4 Bedrooms', icon: '🏘️', count: 23 },
        { id: 6, name: '5+ Bedrooms', icon: '🏰', count: 31 },
    ];

    const products = {
        ofertas: [
            { id: 1, name: 'Back Bay Studio', category: 'Back Bay', price: 2400, original: 2700, discount: '11%', badge: 'sale', icon: '🏢' },
            { id: 2, name: 'Seaport 1BR Loft', category: 'Seaport', price: 3200, original: 3600, discount: '11%', badge: 'sale', icon: '🌇' },
            { id: 3, name: 'Beacon Hill Brownstone', category: 'Beacon Hill', price: 4100, original: 4500, discount: '9%', badge: 'sale', icon: '🏠' },
            { id: 4, name: 'Fenway Modern 1BR', category: 'Fenway', price: 2850, original: 3100, discount: '8%', badge: 'sale', icon: '🏙️' },
        ],
        liquidaciones: [
            { id: 5, name: 'South End 2BR', category: 'South End', price: 3400, original: 4200, discount: '19%', badge: 'liquidation', icon: '🏡' },
            { id: 6, name: 'Cambridge 3BR', category: 'Cambridge', price: 4800, original: 5600, discount: '14%', badge: 'liquidation', icon: '🏘️' },
            { id: 7, name: 'North End Studio', category: 'North End', price: 2100, original: 2600, discount: '19%', badge: 'liquidation', icon: '🏢' },
            { id: 8, name: 'Charlestown Loft', category: 'Charlestown', price: 3500, original: 4100, discount: '15%', badge: 'liquidation', icon: '🏙️' },
        ],
        nuevos: [
            { id: 9, name: 'Jamaica Plain 1BR', category: 'Jamaica Plain', price: 2300, original: null, discount: null, badge: 'new', icon: '🛏️' },
            { id: 10, name: 'Allston 2BR', category: 'Allston', price: 2900, original: null, discount: null, badge: 'new', icon: '🏠' },
            { id: 11, name: 'Brighton Studio', category: 'Brighton', price: 1900, original: null, discount: null, badge: 'new', icon: '🏢' },
            { id: 12, name: 'Somerville 1BR', category: 'Somerville', price: 2500, original: null, discount: null, badge: 'new', icon: '🏙️' },
        ],
        vendidos: [
            { id: 13, name: 'Back Bay Penthouse', category: 'Back Bay', price: 6500, original: 7200, discount: '10%', badge: 'hot', icon: '🏰' },
            { id: 14, name: 'Seaport 2BR', category: 'Seaport', price: 4200, original: 4800, discount: '12%', badge: 'hot', icon: '🌇' },
            { id: 15, name: 'Beacon Hill 1BR', category: 'Beacon Hill', price: 3100, original: 3500, discount: '11%', badge: 'hot', icon: '🏠' },
            { id: 16, name: 'Fenway Studio', category: 'Fenway', price: 2200, original: 2500, discount: '12%', badge: 'hot', icon: '🏢' },
        ]
    };

    // ── FUNCIÓN PARA RENDERIZAR CARDS ──
    function renderCards(containerId, items, categorySlug) {
        const container = document.getElementById(containerId);
        if (!container) return;
        container.innerHTML = items.map(item => `
            <a href="/products/${item.id}" class="card-item">
                <div class="card-image">
                    ${item.icon || '🏢'}
                    ${item.badge ? `<span class="card-badge ${item.badge}">${item.badge === 'sale' ? 'Featured' : item.badge === 'liquidation' ? 'Reduced' : item.badge === 'new' ? 'New' : 'Trending'}</span>` : ''}
                </div>
                <div class="card-info">
                    <div class="card-name">${item.name}</div>
                    <div class="card-category">${item.category}</div>
                    <div class="card-price">
                        <span class="current">$${item.price.toLocaleString()}/mo</span>
                        ${item.original ? `<span class="original">$${item.original.toLocaleString()}/mo</span>` : ''}
                        ${item.discount ? `<span class="discount">-${item.discount}</span>` : ''}
                    </div>
                    <button class="btn-add-cart" onclick="event.preventDefault(); event.stopPropagation(); alert('Tour scheduled! We will contact you soon.')">
                        🗓️ Schedule Tour
                    </button>
                </div>
            </a>
        `).join('');
    }

    // ── RENDERIZAR CATEGORÍAS ──
    function renderCategories() {
        const container = document.getElementById('categoriesGrid');
        if (!container) return;
        container.innerHTML = categories.map(cat => `
            <a href="/categories/${cat.id}" class="category-card">
                <span class="category-icon">${cat.icon}</span>
                <div class="category-name">${cat.name}</div>
                <div class="category-count">${cat.count} listings</div>
            </a>
        `).join('');
    }

    // ── RENDERIZA TODO ──
    renderCards('ofertas-grid', products.ofertas);
    renderCards('liquidaciones-grid', products.liquidaciones);
    renderCards('nuevos-grid', products.nuevos);
    renderCards('vendidos-grid', products.vendidos);
    renderCategories();
});
</script>
@endsection