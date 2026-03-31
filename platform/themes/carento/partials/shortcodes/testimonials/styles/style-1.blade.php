@php
    $title = $shortcode->title;
    $subtitle = $shortcode->subtitle;
@endphp

<style>
    /* =========================================
       1. LIGHT MODE (DEFAULT) STYLES
       ========================================= */
    /* The Outer Island Wrapper */
    .testimonial-zoom-style .zoom-outer-wrapper {
        background-color: #f8f9fa !important; 
        border-radius: 24px !important;
        padding: 80px 0 !important; 
        margin: 0 auto;
        max-width: 1240px;
        position: relative;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    /* Header Section */
    .testimonial-zoom-style .header-centered {
        text-align: center;
        margin-bottom: 50px;
        padding: 0 40px;
    }

    .testimonial-zoom-style .author-stack {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
    }

    .testimonial-zoom-style .author-stack img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid #fff;
        margin-left: -12px;
        object-fit: cover;
    }
    
    .testimonial-zoom-style .author-stack img:first-child { margin-left: 0; }

    /* Card Styling */
    .testimonial-zoom-style .card-testimonial {
        background: #ffffff !important;
        border-radius: 16px !important;
        padding: 30px !important;
        margin: 10px; 
        border: 1px solid rgba(0,0,0,0.05) !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03) !important;
        transition: transform 0.3s ease, background-color 0.3s ease, border-color 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .testimonial-zoom-style .card-testimonial:hover {
        transform: translateY(-5px);
    }

    .testimonial-zoom-style .star-row {
        color: #ffc107;
        margin-bottom: 15px;
        display: flex;
        gap: 2px;
    }

    /* Typography Transitions */
    .testimonial-zoom-style .shortcode-title,
    .testimonial-zoom-style .neutral-1000,
    .testimonial-zoom-style .neutral-600,
    .testimonial-zoom-style .neutral-500 {
        transition: color 0.3s ease;
    }

    /* Swiper Container Adjustments */
    .testimonial-zoom-style .box-swiper {
        position: relative;
        width: 100%;
        padding: 0 40px; 
    }

    .testimonial-zoom-style .swiper-container {
        padding-bottom: 60px !important;
        overflow: visible; 
    }

    .testimonial-zoom-style .swiper-slide {
        height: auto; 
    }

    /* Standard Pagination Dots */
    .testimonial-zoom-style .swiper-pagination {
        position: absolute;
        bottom: 10px !important;
        left: 0;
        width: 100%;
        text-align: center;
    }

    .testimonial-zoom-style .swiper-pagination-bullet {
        width: 10px;
        height: 10px;
        background: #1a1a1a;
        opacity: 0.2;
        margin: 0 5px !important;
        transition: all 0.3s ease;
    }
    
    .testimonial-zoom-style .swiper-pagination-bullet-active {
        background: #1a1a1a !important; /* Default dark color for active */
        opacity: 1;
        width: 24px;
        border-radius: 5px;
    }

    /* =========================================
       2. DARK MODE OVERRIDES
       ========================================= */
    /* Island Wrapper */
    [data-bs-theme="dark"] .testimonial-zoom-style .zoom-outer-wrapper,
    .dark .testimonial-zoom-style .zoom-outer-wrapper,
    .theme-dark .testimonial-zoom-style .zoom-outer-wrapper {
        background-color: #18191a !important; 
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    /* Testimonial Cards */
    [data-bs-theme="dark"] .testimonial-zoom-style .card-testimonial,
    .dark .testimonial-zoom-style .card-testimonial,
    .theme-dark .testimonial-zoom-style .card-testimonial {
        background-color: #242526 !important; /* Slightly lighter than the wrapper to stand out */
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.4) !important; /* Darker shadow for depth */
    }

    /* Author Stack Avatar Borders */
    [data-bs-theme="dark"] .testimonial-zoom-style .author-stack img,
    .dark .testimonial-zoom-style .author-stack img,
    .theme-dark .testimonial-zoom-style .author-stack img {
        border-color: #18191a;
    }

    /* Text Colors */
    [data-bs-theme="dark"] .testimonial-zoom-style .shortcode-title,
    [data-bs-theme="dark"] .testimonial-zoom-style .neutral-1000,
    .dark .testimonial-zoom-style .shortcode-title,
    .dark .testimonial-zoom-style .neutral-1000,
    .theme-dark .testimonial-zoom-style .shortcode-title,
    .theme-dark .testimonial-zoom-style .neutral-1000 {
        color: #f8f9fa !important; /* White text for titles and names */
    }

    [data-bs-theme="dark"] .testimonial-zoom-style .neutral-600,
    [data-bs-theme="dark"] .testimonial-zoom-style .neutral-500,
    .dark .testimonial-zoom-style .neutral-600,
    .dark .testimonial-zoom-style .neutral-500,
    .theme-dark .testimonial-zoom-style .neutral-600,
    .theme-dark .testimonial-zoom-style .neutral-500 {
        color: #adb5bd !important; /* Light gray for content and companies */
    }

    /* Pagination Dots (Invert for Dark Mode) */
    [data-bs-theme="dark"] .testimonial-zoom-style .swiper-pagination-bullet,
    .dark .testimonial-zoom-style .swiper-pagination-bullet,
    .theme-dark .testimonial-zoom-style .swiper-pagination-bullet {
        background: #ffffff;
        opacity: 0.3;
    }

    [data-bs-theme="dark"] .testimonial-zoom-style .swiper-pagination-bullet-active,
    .dark .testimonial-zoom-style .swiper-pagination-bullet-active,
    .theme-dark .testimonial-zoom-style .swiper-pagination-bullet-active {
        background: #ffffff !important;
        opacity: 1;
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-testimonial shortcode-faqs testimonial-zoom-style py-96 background-body mxcar-faq-clean-section">
    <div class="container mxcar-faq-clean-container">
        <div class="zoom-outer-wrapper shadow-sm">
            
            <div class="header-centered">
                @if($testimonials->count() > 0)
                    <div class="author-stack wow fadeInDown">
                        @foreach($testimonials->take(5) as $testimonial)
                            {{ RvMedia::image($testimonial->image, $testimonial->name, 'thumb') }}
                        @endforeach
                    </div>
                @endif

                @if($subtitle)
                    <div class="section-subtitle mxcar-page-desc text-center mb-3">{!! BaseHelper::clean($subtitle) !!}</div>
                @endif

                @if($title)
                    <h2 class="mxcar-page-title wow fadeInUp text-center">{!! BaseHelper::clean($title) !!}</h2>
                @endif
            </div>

            <div class="block-testimonials">
                <div class="box-swiper">
                    <div id="testimonial-slider-{{ uniqid() }}" class="swiper-container swiper-group-journey-no-arrows">
                        <div class="swiper-wrapper">
                            @foreach($testimonials as $testimonial)
                                <div class="swiper-slide">
                                    <div class="card-testimonial">
                                        <div class="card-top-content">
                                            <div class="star-row">
                                                @php $stars = (int) $testimonial->getMetaData('rating_star', true) ?: 5; @endphp
                                                @for($i = 0; $i < $stars; $i++)
                                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                                @endfor
                                            </div>
                                            @if($content = $testimonial->content) 
                                                <p class="text-md-medium neutral-600 mb-4 italic">"{!! BaseHelper::clean($content) !!}"</p> 
                                            @endif
                                        </div>

                                        <div class="card-author d-flex align-items-center pt-3 border-top">
                                            <div class="card-image">
                                                {{ RvMedia::image($testimonial->image, $testimonial->name, attributes: ['class' => 'img-fluid', 'style' => 'width: 50px; height: 50px; border-radius: 50%; object-fit: cover;']) }}
                                            </div>
                                            <div class="card-info ms-3">
                                                <p class="text-lg-bold neutral-1000 mb-0">{!! BaseHelper::clean($testimonial->name) !!}</p>
                                                @if($company = $testimonial->company) 
                                                    <p class="text-sm neutral-500 mb-0">{!! BaseHelper::clean($company) !!}</p> 
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<script>
   document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swiper !== 'undefined') {
            
            new Swiper('.swiper-group-journey-no-arrows', {
                slidesPerView: 1,      /* Default mobile */
                spaceBetween: 30,      /* Space between cards */
                loop: true,
                centeredSlides: false, /* Ensure cards align left, not center-screen */
                grabCursor: true,      /* Show hand icon to indicate swipeability */
                
                autoplay: {
                    delay: 4000,
                    disableOnInteraction: false,
                },
                
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                    dynamicBullets: true, /* Makes dots look cleaner if there are many */
                },
                
                /* CRITICAL: breakpoints determine how many are shown */
                breakpoints: {
                    768: { 
                        slidesPerView: 2,
                        spaceBetween: 20
                    },
                    1100: { 
                        slidesPerView: 3,
                        spaceBetween: 30
                    }
                }
            });
        }
    });
</script>