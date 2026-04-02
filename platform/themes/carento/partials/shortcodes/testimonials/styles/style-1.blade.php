@php
    $title = $shortcode->title;
    $subtitle = $shortcode->subtitle;
@endphp

<style>
    /* =========================================
       1. HEADER & SECTION STYLES
       ========================================= */
    .shortcode-testimonial {
        background-color: transparent !important;
        padding-top: 40px;
        padding-bottom: 40px;
    }

    /* THE BENTO CONTAINER - Matches Car Style Latest */
    .testimonial-bento-container {
        background-color: #f8f9fa; /* Off-white container */
        border-radius: 24px;
        padding: 50px 60px;
        max-width: 1200px; 
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

    /* Author Stack (Top of Header) */
    .author-stack {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
    }
    .author-stack img {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        border: 2px solid #f8f9fa;
        margin-left: -14px;
        object-fit: cover;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .author-stack img:first-child { margin-left: 0; }

    /* =========================================
       2. MODERN CARD DESIGN - PURE WHITE
       ========================================= */
    .testimonial-card-modern {
        background-color: #ffffff !important; 
        border-radius: 16px;
        padding: 32px 24px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%;
        border: 1px solid #e2e8f0 !important; 
        margin-bottom: 10px; /* Buffer for hover shadow */
    }
    .testimonial-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1 !important; 
    }

    .star-row {
        color: #f59e0b; /* Amber */
        margin-bottom: 16px;
        display: flex;
        gap: 4px;
    }
    .star-row svg {
        width: 18px;
        height: 18px;
    }

    .testimonial-quote {
        font-size: 1rem;
        line-height: 1.6;
        color: #475569;
        font-style: italic;
        margin-bottom: 24px;
        flex-grow: 1;
    }

    /* Author Footer inside Card */
    .testimonial-author-footer {
        display: flex;
        align-items: center;
        padding-top: 20px;
        border-top: 1px solid #f1f5f9;
    }
    .testimonial-author-footer img {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        object-fit: cover;
        margin-right: 16px;
    }
    .testimonial-author-info .author-name {
        font-weight: 700;
        color: #111827;
        margin: 0;
        font-size: 1.05rem;
    }
    .testimonial-author-info .author-company {
        color: #94a3b8;
        font-size: 0.85rem;
        margin: 0;
        margin-top: 2px;
    }

    /* =========================================
       3. SLIDER NAVIGATION & DOTS
       ========================================= */
    .slider-nav-wrapper {
        position: relative;
    }

    /* Floating SVG Arrows */
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
    .swiper-button-prev.custom-arrow::after,
    .swiper-button-next.custom-arrow::after { display: none !important; }

    .swiper-button-prev.custom-arrow { left: -24px !important; transform: translateY(-50%) !important; }
    .swiper-button-next.custom-arrow { right: -24px !important; transform: translateY(-50%) !important; }

    .swiper-button-prev.custom-arrow:hover,
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
        margin-bottom: 5px;
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

    /* Dark Mode Support */
    html[data-bs-theme="dark"] .testimonial-bento-container { background-color: #1e293b; box-shadow: none; border: 1px solid rgba(255,255,255,0.05); }
    html[data-bs-theme="dark"] .section-title-center { color: #fff; }
    html[data-bs-theme="dark"] .section-subtitle-center { color: #94a3b8; }
    html[data-bs-theme="dark"] .testimonial-card-modern { background: #0f172a !important; border-color: #334155 !important; }
    html[data-bs-theme="dark"] .testimonial-quote { color: #cbd5e1; }
    html[data-bs-theme="dark"] .testimonial-author-info .author-name { color: #f8fafc; }
    html[data-bs-theme="dark"] .testimonial-author-footer { border-color: #334155; }
    html[data-bs-theme="dark"] .author-stack img { border-color: #1e293b; }
    
    @media (max-width: 768px) {
        .testimonial-bento-container { padding: 40px 20px; }
        .swiper-button-prev.custom-arrow { left: -10px !important; }
        .swiper-button-next.custom-arrow { right: -10px !important; }
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-testimonial testimonial-zoom-style py-5">
    <div class="container">
        
        <div class="testimonial-bento-container wow fadeInUp" data-wow-delay="0.1s">
            
            <div class="row">
                <div class="col-12">
                    @if($testimonials->count() > 0)
                        <div class="author-stack">
                            @foreach($testimonials->take(5) as $testimonial)
                                {{ RvMedia::image($testimonial->image, $testimonial->name, 'thumb') }}
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($title))
                        <h2 class="section-title-center">{!! BaseHelper::clean($title) !!}</h2>
                    @endif
                    
                    @if(!empty($subtitle))
                        <p class="section-subtitle-center">{!! BaseHelper::clean($subtitle) !!}</p>
                    @endif
                </div>
            </div>

            <div class="slider-nav-wrapper">
                
                <div class="swiper-container testimonial-swiper-container pt-2 pb-2">
                    <div class="swiper-wrapper">
                        
                        @foreach($testimonials as $testimonial)
                            <div class="swiper-slide h-auto">
                                <div class="testimonial-card-modern">
                                    
                                    <div class="card-top-content">
                                        <div class="star-row">
                                            @php $stars = (int) $testimonial->getMetaData('rating_star', true) ?: 5; @endphp
                                            @for($i = 0; $i < $stars; $i++)
                                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                            @endfor
                                        </div>
                                        
                                        @if($content = $testimonial->content) 
                                            <div class="testimonial-quote">
                                                "{!! BaseHelper::clean($content) !!}"
                                            </div> 
                                        @endif
                                    </div>

                                    <div class="testimonial-author-footer">
                                        {{ RvMedia::image($testimonial->image, $testimonial->name, attributes: ['class' => 'img-fluid']) }}
                                        <div class="testimonial-author-info">
                                            <p class="author-name">{!! BaseHelper::clean($testimonial->name) !!}</p>
                                            @if($company = $testimonial->company) 
                                                <p class="author-company">{!! BaseHelper::clean($company) !!}</p> 
                                            @endif
                                        </div>
                                    </div>
                                    
                                </div>
                            </div>
                        @endforeach
                        
                    </div>
                </div>
                
                <div class="swiper-button-prev custom-arrow testimonial-arrow-prev">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                </div>
                <div class="swiper-button-next custom-arrow testimonial-arrow-next">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                </div>

            </div>

            <div class="swiper-pagination swiper-pagination-custom testimonial-pagination text-center"></div>

        </div>
        
    </div>
</section>

{{-- SLIDER INITIALIZATION SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swiper !== 'undefined') {
            new Swiper('.shortcode-testimonial .testimonial-swiper-container', {
                slidesPerView: 1,
                spaceBetween: 24,
                loop: false,
                watchOverflow: true,
                navigation: {
                    nextEl: '.shortcode-testimonial .testimonial-arrow-next',
                    prevEl: '.shortcode-testimonial .testimonial-arrow-prev',
                },
                pagination: {
                    el: '.shortcode-testimonial .testimonial-pagination',
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