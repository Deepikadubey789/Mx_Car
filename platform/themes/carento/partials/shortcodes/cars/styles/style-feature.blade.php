@php
    // Flatten any chunks so Swiper handles the layout smoothly
    $displayCars = collect($cars)->flatten();
@endphp

{{-- FRONTEND CUSTOM CSS SCOPED ONLY TO THE CAR CARDS --}}
<style>
    /* =========================================
       1. HEADER & SECTION STYLES
       ========================================= */
    .shortcode-cars.car-style-feature {
        background-color: transparent !important;
        padding-top: 40px;
        padding-bottom: 40px;
    }

    /* THE BENTO CONTAINER - MATCHING car-style-latest EXACTLY */
    .feature-bento-container {
        background-color: #f8f9fa; /* Off-white container */
        border-radius: 24px;
        padding: 50px 60px;
        max-width: 1200px; /* Locked to 1200px to match other sections */
        margin: 0 auto;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }

    /* Centered Title with Decorative Lines */
    .section-title-center {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 8px;
    }
    .section-title-center::before, 
    .section-title-center::after {
        content: '';
        height: 2px;
        width: 40px;
        background: #475569; 
        margin: 0 20px;
        border-radius: 2px;
    }
    
    .section-subtitle-center {
        text-align: center;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 40px;
    }

    /* =========================================
       2. MODERN CARD DESIGN - PURE WHITE
       ========================================= */
    .car-card-modern {
        background-color: #ffffff !important; 
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        border: 1px solid #e2e8f0 !important; 
        margin-bottom: 10px; /* Buffer for hover shadow */
    }
    .car-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1 !important; 
    }

    /* --- IMAGE & OVERLAY AREA --- */
    .car-card-modern .img-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 16 / 9 !important; /* Locks width/height ratio consistently */
        overflow: hidden;
        background: #ffffff;
    }
    .car-card-modern .img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center; 
        transition: transform 0.5s ease;
    }
    .car-card-modern:hover .img-wrap img {
        transform: scale(1.05);
    }

    .car-card-modern .img-wrap::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 60%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.85), transparent);
        pointer-events: none;
    }

    /* Favorite Heart Icon */
    .car-card-modern .favorite-btn {
        position: absolute;
        top: 15px !important;
        right: 15px !important;
        width: 32px;
        height: 32px;
        background: rgba(0, 0, 0, 0.3);
        backdrop-filter: blur(4px);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        z-index: 10;
        transition: background 0.3s ease;
        cursor: pointer;
    }
    .car-card-modern .favorite-btn:hover {
        background: rgba(0, 0, 0, 0.6);
        color: #ff4757;
    }

    /* Overlay Content (Title, Specs) */
    .img-overlay-content {
        position: absolute;
        bottom: 15px;
        left: 15px;
        right: 15px;
        z-index: 2;
        display: flex;
        flex-direction: column;
    }
    .img-overlay-content .car-title {
        color: #ffffff;
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 4px;
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }
    .img-overlay-content .car-title a {
        color: inherit;
        text-decoration: none;
        display: block;
        width: 100%;
    }

    /* Force Car Facilities to display inline */
    .img-overlay-content .card-program > ul,
    .img-overlay-content .card-program > div {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 6px !important;
        padding: 0 !important;
        margin: 0 !important;
        list-style: none !important;
    }
    .img-overlay-content .card-program li,
    .img-overlay-content .card-program div,
    .img-overlay-content .card-program span {
        color: #cbd5e1 !important;
        font-size: 0.8rem !important;
        display: flex !important;
        align-items: center !important;
    }
    .img-overlay-content .card-program i,
    .img-overlay-content .card-program svg {
        display: none !important; 
    }
    .img-overlay-content .card-program li:not(:last-child)::after,
    .img-overlay-content .card-program > div > div:not(:last-child)::after {
        content: '•';
        margin-left: 6px;
        color: #94a3b8;
    }

    /* --- BOTTOM DETAILS AREA (LOCATION & PRICE) --- */
    .car-card-modern .card-details {
        padding: 16px 16px 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        background: transparent; 
    }
    
    .location-col {
        font-size: 0.85rem;
        color: #475569;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        margin-top: 2px;
    }
    .location-col svg {
        width: 14px;
        height: 14px;
        fill: #111827; 
        flex-shrink: 0;
    }
    
    .price-col {
        text-align: right;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }
    
    .price-amount {
        font-size: 1.15rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 2px;
        line-height: 1.2;
    }
    .price-amount * {
        margin: 0 !important;
        padding: 0 !important;
        font-size: inherit !important;
        font-weight: inherit !important;
        color: inherit !important;
        display: inline-block;
    }

    .price-excluding {
        font-size: 0.75rem;
        color: #94a3b8;
        text-decoration: underline; 
        text-underline-offset: 2px;
    }

    /* --- HOST FOOTER (CYAN BACKGROUND) --- */
    .card-host-footer {
        padding: 12px 16px;
        background: #f0fdf4; 
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.75rem;
        color: #0369a1; 
        font-weight: 600;
        border-bottom-left-radius: 16px;
        border-bottom-right-radius: 16px;
    }
    
    .host-avatar {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #0ea5e9; 
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        flex-shrink: 0;
    }
    .host-avatar svg {
        width: 12px;
        height: 12px;
        fill: currentColor;
    }

    /* =========================================
       3. SLIDER NAVIGATION & BUTTON
       ========================================= */
    .slider-nav-wrapper {
        position: relative;
    }

    /* Hide Swiper's default ugly text-based arrows */
    .swiper-button-prev.custom-arrow::after,
    .swiper-button-next.custom-arrow::after {
        display: none !important;
    }

    /* New Beautiful Floating Arrows */
    .swiper-button-prev.custom-arrow,
    .swiper-button-next.custom-arrow {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 50% !important;
        width: 48px !important;
        height: 48px !important;
        color: #111827 !important;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08) !important;
        top: 50% !important;
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        z-index: 20 !important;
    }
    /* Position them beautifully on the edges */
    .swiper-button-prev.custom-arrow { left: -24px !important; transform: translateY(-50%) !important; }
    .swiper-button-next.custom-arrow { right: -24px !important; transform: translateY(-50%) !important; }

    /* Hover scale effect */
    .swiper-button-prev.custom-arrow:hover {
        background: #111827 !important;
        color: #ffffff !important;
        border-color: #111827 !important;
        transform: translateY(-50%) scale(1.1) !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
    .swiper-button-next.custom-arrow:hover {
        background: #111827 !important;
        color: #ffffff !important;
        border-color: #111827 !important;
        transform: translateY(-50%) scale(1.1) !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }

    /* Fixed Pagination Dots */
    .swiper-pagination-custom {
        position: relative !important;
        margin-top: 30px;
        margin-bottom: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }
    .swiper-pagination-custom .swiper-pagination-bullet {
        background: #cbd5e1 !important;
        opacity: 1 !important;
        width: 8px !important;
        height: 8px !important;
        margin: 0 !important; 
        border-radius: 50% !important;
        transition: all 0.3s ease;
    }
    .swiper-pagination-custom .swiper-pagination-bullet-active {
        background: #10b981 !important; 
        width: 24px !important; 
        border-radius: 8px !important;
    }

    /* Bottom Button - Pill Shaped and Theme-Colored */
    .btn-theme-pill {
        border-radius: 50px !important; /* Perfect pill shape */
        padding: 14px 40px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        font-size: 0.9rem !important;
        transition: all 0.3s ease !important;
    }
    .btn-theme-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15) !important;
    }

    /* Dark Mode Support */
    html[data-bs-theme="dark"] .feature-bento-container { background-color: #1e293b; box-shadow: none; border: 1px solid rgba(255,255,255,0.05); }
    html[data-bs-theme="dark"] .section-title-center { color: #fff; }
    html[data-bs-theme="dark"] .section-subtitle-center { color: #94a3b8; }
    html[data-bs-theme="dark"] .car-card-modern { background: #0f172a !important; border-color: #334155 !important; }
    html[data-bs-theme="dark"] .location-col, html[data-bs-theme="dark"] .price-col .price-amount { color: #f8fafc; }
    html[data-bs-theme="dark"] .location-col svg { fill: #f8fafc; }
    html[data-bs-theme="dark"] .card-host-footer { background: #1e293b; border-color: #334155; }
    
    @media (max-width: 768px) {
        .feature-bento-container { padding: 30px 20px; }
        .swiper-button-prev.custom-arrow { left: -10px !important; }
        .swiper-button-next.custom-arrow { right: -10px !important; }
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-cars car-style-feature section-box py-5">
    <div class="container">
        
        <div class="feature-bento-container wow fadeInUp" data-wow-delay="0.1s">
            
            <div class="row">
                <div class="col-12">
                    @if(!empty($title))
                        <h2 class="section-title-center">{!! BaseHelper::clean($title) !!}</h2>
                    @endif
                    @if(!empty($subtitle))
                        <p class="section-subtitle-center">{!! BaseHelper::clean($subtitle) !!}</p>
                    @endif
                </div>
            </div>

            <div class="slider-nav-wrapper">
                
                <div class="swiper-container feature-swiper-container pt-2 pb-2">
                    <div class="swiper-wrapper">
                        
                        @foreach($displayCars as $car)
                            <div class="swiper-slide h-auto"> 
                                <div class="car-card-modern">
                                    
                                    <div class="img-wrap">
                                        <a href="{{ $car->url }}">
                                            {{ RvMedia::image($car->image, $car->name, 'default') }}
                                        </a>
                                        
                                        <div class="favorite-btn">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
                                            </svg>
                                        </div>

                                        <div class="img-overlay-content">
                                            <div class="car-title text-truncate">
                                                <a href="{{ $car->url }}">
                                                    {!! BaseHelper::clean($car->name) !!}
                                                </a>
                                            </div>
                                            <div class="card-program">
                                                @include(Theme::getThemeNamespace('views.car-rentals.car-facilities'), ['car' => $car])
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card-details">
                                        <div class="location-col text-truncate" style="max-width: 130px;" title="{{ $car->location }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                                            {{ $car->location ? BaseHelper::clean($car->location) : __('Location N/A') }}
                                        </div>
                                        
                                        <div class="price-col">
                                            <div class="price-amount">
                                                @include(Theme::getThemeNamespace('views.car-rentals.price'), ['car' => $car])
                                            </div>
                                            <div class="price-excluding">{{ __('excluding fees') }}</div>
                                        </div>
                                    </div>

                                    <div class="card-host-footer">
                                        <div class="host-avatar">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                                        </div>
                                        <span class="text-truncate">
                                            {{ __('By') }} <span class="host-name" style="text-transform: uppercase;">{{ $car->vendor->name ?? $car->author->name ?? __('Verified Host') }}</span> 
                                        </span>
                                    </div>
                                    
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="swiper-button-prev custom-arrow feature-arrow-prev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                </div>
                <div class="swiper-button-next custom-arrow feature-arrow-next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                </div>

            </div>

            <div class="swiper-pagination swiper-pagination-custom feature-pagination text-center"></div>
            
            @if(empty($buttonLabel) === false)
                <div class="d-flex justify-content-center mt-3">
                    <a class="btn btn-primary btn-theme-pill" href="{{ $buttonUrl }}">
                        {!! BaseHelper::clean($buttonLabel) !!}
                    </a>
                </div>
            @endif

        </div> 
        
    </div>
</section>

{{-- SLIDER INITIALIZATION SCRIPT FOR FEATURE BLOCK --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swiper !== 'undefined') {
            new Swiper('.shortcode-cars.car-style-feature .feature-swiper-container', {
                slidesPerView: 1,
                spaceBetween: 24,
                loop: false,
                watchOverflow: true,
                slidesPerGroup: 1,   
                navigation: {
                    nextEl: '.shortcode-cars.car-style-feature .feature-arrow-next',
                    prevEl: '.shortcode-cars.car-style-feature .feature-arrow-prev',
                },
                pagination: {
                    el: '.shortcode-cars.car-style-feature .feature-pagination',
                    clickable: true,
                    dynamicBullets: false 
                },
                breakpoints: {
                    768: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 } 
                }
            });
        }
    });
</script>