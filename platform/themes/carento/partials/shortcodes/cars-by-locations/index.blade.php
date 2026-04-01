@php
    $buttonName = $shortcode->button_label;
    $buttonUrl = $shortcode->button_url;
    $title = $shortcode->title;
    $mainContent = $shortcode->main_content;
@endphp

{{-- FRONTEND CUSTOM CSS SCOPED ONLY TO THE LOCATIONS SECTION --}}
<style>
    /* =========================================
       1. MAIN CONTAINER (BENTO STYLE)
       ========================================= */
    .shortcode-cars-by-locations {
        background-color: transparent !important;
        padding-top: 40px;
        padding-bottom: 40px;
    }

    .location-bento-container {
        background-color: #f8f9fa; /* Off-white container */
        border-radius: 24px;
        padding: 50px 60px;
        max-width: 1200px;
        margin: 0 auto;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        position: relative;
    }

    /* =========================================
       2. TITLE STYLING (WITH BARS)
       ========================================= */
    .section-title-center {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 10px;
        text-align: center;
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
       3. IMMERSIVE LOCATION CARD (WITH ZOOM)
       ========================================= */
    .location-turo-card {
        position: relative;
        border-radius: 12px !important; /* Slightly sharper corners to match screenshot */
        overflow: hidden;
        height: 380px; /* Taller vertical aspect ratio */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        display: block; 
        background: #000; 
        transform: translateZ(0); /* Hardware acceleration for smooth zoom */
    }

    /* Full-Cover Image & Hover Physics */
    .location-turo-card .card-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
    }
    .location-turo-card .card-image img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    
    /* THE ZOOM EFFECT - INCREASED TO 1.2 FOR MORE ZOOM */
    .location-turo-card:hover .card-image img {
        transform: scale(1.2); /* Zooms in more noticeably */
    }

    /* Dark Gradient Overlay */
    .location-turo-card .gradient-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 50%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, rgba(0, 0, 0, 0) 100%);
        z-index: 1;
        transition: opacity 0.3s ease;
    }

    /* Text Content - Centered */
    .location-turo-card .card-content {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 24px 20px;
        z-index: 2;
        display: flex;
        justify-content: center; /* Centers horizontally */
        align-items: center;
        text-align: center;
    }

    .location-turo-card .location-title {
        color: #ffffff !important;
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        margin: 0 !important;
        text-shadow: 0 2px 4px rgba(0,0,0,0.5);
    }

    /* =========================================
       4. SLIDER NAVIGATION & DOTS
       ========================================= */
    .slider-nav-wrapper {
        position: relative;
    }

    /* Floating Arrows */
    .swiper-button-prev.custom-arrow::after,
    .swiper-button-next.custom-arrow::after { display: none !important; }

    .swiper-button-prev.custom-arrow,
    .swiper-button-next.custom-arrow {
        background: transparent !important;
        border: none !important;
        width: 40px !important;
        height: 40px !important;
        color: #111827 !important;
        top: 50% !important;
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: transform 0.2s ease !important;
        z-index: 20 !important;
    }
    .swiper-button-prev.custom-arrow { left: -40px !important; transform: translateY(-50%) !important; }
    .swiper-button-next.custom-arrow { right: -40px !important; transform: translateY(-50%) !important; }

    .swiper-button-prev.custom-arrow:hover,
    .swiper-button-next.custom-arrow:hover {
        transform: translateY(-50%) scale(1.2) !important;
    }

    /* Pagination Dots */
    .swiper-pagination-custom {
        position: relative !important;
        margin-top: 30px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
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
        background: #10b981 !important; /* Green dot matching screenshot */
        width: 20px !important; 
        border-radius: 8px !important;
    }

    /* =========================================
       5. DARK MODE OVERRIDES
       ========================================= */
    html[data-bs-theme="dark"] .location-bento-container { background-color: #1e293b; box-shadow: none; border: 1px solid rgba(255,255,255,0.05); }
    html[data-bs-theme="dark"] .section-title-center { color: #fff; }
    html[data-bs-theme="dark"] .section-subtitle-center { color: #94a3b8; }
    html[data-bs-theme="dark"] .section-title-center::before,
    html[data-bs-theme="dark"] .section-title-center::after { background: #94a3b8; }
    html[data-bs-theme="dark"] .swiper-button-prev.custom-arrow,
    html[data-bs-theme="dark"] .swiper-button-next.custom-arrow { color: #f8fafc !important; }

    /* =========================================
       6. MOBILE RESPONSIVENESS
       ========================================= */
    @media (max-width: 991px) {
        .location-bento-container { padding: 40px 30px; }
        .swiper-button-prev.custom-arrow { left: -15px !important; }
        .swiper-button-next.custom-arrow { right: -15px !important; }
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-cars-by-locations section-box py-5">
    <div class="container">
        
        <div class="location-bento-container wow fadeInUp" data-wow-delay="0.1s">
            
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    @if($title)
                        <h2 class="section-title-center">{!! BaseHelper::clean($title) !!}</h2>
                    @endif

                    @if($mainContent)
                        <p class="section-subtitle-center">{!! BaseHelper::clean($mainContent) !!}</p>
                    @endif
                </div>
            </div>
            
            <div class="slider-nav-wrapper">
                <div class="swiper-container location-swiper-container pt-2 pb-2">
                    <div class="swiper-wrapper">
                        
                        @foreach($locations as $location)
                            @php
                                $name = $location['name'] ?? '';
                                $image = $location['image'] ?? '';
                            @endphp

                            @continue(! $name)
                            
                            <div class="swiper-slide h-auto">
                                <a href="{{ route('public.cars', ['location' => $name]) }}" class="location-turo-card">
                                    <div class="card-image">
                                        {{ RvMedia::image($image, $name, 'small-rectangle-vertical') }}
                                        <div class="gradient-overlay"></div>
                                    </div>
                                    <div class="card-content">
                                        <div class="location-title">{!! BaseHelper::clean($name) !!}</div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                        
                    </div>
                </div>
                
                <div class="swiper-button-prev custom-arrow location-arrow-prev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                </div>
                <div class="swiper-button-next custom-arrow location-arrow-next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                </div>
            </div>

            <div class="swiper-pagination location-pagination swiper-pagination-custom text-center"></div>

        </div>
        
    </div>
</section>

{{-- SLIDER INITIALIZATION SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swiper !== 'undefined') {
            new Swiper('.shortcode-cars-by-locations .location-swiper-container', {
                slidesPerView: 1,
                spaceBetween: 24,
                loop: false,
                watchOverflow: true,
                navigation: {
                    nextEl: '.shortcode-cars-by-locations .location-arrow-next',
                    prevEl: '.shortcode-cars-by-locations .location-arrow-prev',
                },
                pagination: {
                    el: '.shortcode-cars-by-locations .location-pagination',
                    clickable: true,
                },
                breakpoints: {
                    576: { slidesPerView: 2 },
                    992: { slidesPerView: 3 },
                    1200: { slidesPerView: 4 } /* Shows 4 cards at once to match screenshot */
                }
            });
        }
    });
</script>