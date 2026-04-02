<style>

    .shortcode-car-types {
        background-color: #f8f9fa !important;
        border-radius: 24px !important;
        padding: 30px 40px !important;
        margin: 0 auto;
        max-width: 1240px;
        transition: background-color 0.3s ease;
    }

    .shortcode-car-types .slider-header-flex {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        margin-bottom: 32px !important;
    }
   
    .car-types-title-wrap {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 16px !important;
        white-space: nowrap !important;
        margin-bottom: 8px !important;
    }

    .car-types-needle {
        display: block !important;
        height: 4px !important;
        width: 64px !important;
        max-width: 64px !important;
        border-radius: 0px 4px 4px 0px !important;
        background: linear-gradient(90deg, #ECE7EB 0%, #2A0722 100%) !important;
        clip-path: polygon(0% 50%, 100% 0%, 100% 100%) !important;
    }
    
    .car-types-needle.right {
        border-radius: 4px 0px 0px 4px !important;
        background: linear-gradient(90deg, #2A0722 0%, #ECE7EB 100%) !important;
        clip-path: polygon(0% 0%, 100% 50%, 0% 100%) !important;
    }

    .shortcode-car-types .heading-3 {
        font-weight: 800 !important;
        color: #111827 !important;
        letter-spacing: -0.5px;
        margin: 0 !important;
    }

    .shortcode-car-types .shortcode-subtitle {
        color: #6b7280 !important;
        font-weight: 500 !important;
        margin: 0 !important;
        text-align: center !important;
    }

    .car-types-outer {
        position: relative;
        padding: 0 48px;
    }

    .car-types-nav-btn {
        position: absolute !important;
        top: 38% !important;
        transform: translateY(-50%) !important;
        width: 36px !important;
        height: 36px !important;
        border-radius: 50% !important;
        border: 1px solid #e5e7eb !important;
        background: #ffffff !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: #374151 !important;
        cursor: pointer !important;
        z-index: 10 !important;
        box-shadow: 0 1px 6px rgba(0,0,0,0.12) !important;
        transition: all 0.2s ease !important;
    }

    .car-types-prev { left: 0 !important; }
    .car-types-next { right: 0 !important; }
    .car-types-nav-btn:hover {
        background: #f9fafb !important;
        border-color: #9ca3af !important;
    }

    .car-type-card {
        display: block !important;
        text-decoration: none !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        overflow: visible !important;
        border-radius: 0 !important;
        transition: none !important;
    }

    .car-type-card:hover,
    .car-type-card:focus {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        transform: none !important;
        text-decoration: none !important;
        outline: none !important;
    }

    .car-type-card .card-image {
        aspect-ratio: 16 / 7 !important;
        position: relative !important;
        width: 100% !important;
        height: auto !important;
        background: #EEF2F7 !important;
        overflow: hidden !important;
        display: block !important;
        transition: transform 4s ease; 
        box-shadow 0.3s ease !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08) !important;
    }
    .car-type-card:hover .card-image {
        transform: translateY(-3px) !important;
        box-shadow: 0 10px 28px rgba(0,0,0,0.13) !important;
    }
    .car-type-card .card-image img {
        object-fit: cover !important;
        object-position: center center !important;
        padding: 0 !important;
        width: 100% !important;
        height: 100% !important;
        display: block !important;
        border-radius: 0 !important;
        transition: transform 0.4s ease !important;
    }

    .car-type-card:hover .card-image img {
        transform: scale(1.08) !important;
    }

    .car-type-card .card-arrow {
        position: relative !important;
        height: 0 !important;
        overflow: visible !important;
        z-index: 6 !important;
    }
    .car-type-card .card-arrow-inner {
        position: absolute !important;
        bottom: 14px !important;
        right: 14px !important;
        width: 42px !important;
        height: 42px !important;
        border-radius: 50% !important;
        background: rgba(238, 240, 243, 0.92) !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: #374151 !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.10) !important;
        transition: background 0.3s ease, color 0.3s ease !important;
        pointer-events: none !important;
    }
    .car-type-card:hover .card-arrow-inner {
        background: #d84a38 !important;
        color: #ffffff !important;
    }

    .car-type-card .card-info {
        padding: 11px 2px 0 !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: flex-start !important;
    }
    .car-type-card .card-title {
        font-size: 1.2rem !important;
        font-weight: 700 !important;
        color: #111827 !important;
        margin: 0 0 3px !important;
        line-height: 1.4 !important;
        transition: color 0.4s ease !important;
    }
    .car-type-card:hover .card-title {
        color: #d84a38 !important;
    }
    .car-type-card .card-meta {
        font-size: 0.92rem !important;
        color: #6b7280 !important;
        font-weight: 400 !important;
        margin: 0 !important;
    }

    .car-types-slider {
        overflow: hidden !important;
        padding: 0 !important;
        margin: 0 !important;
    }

    .car-types-dots-wrap {
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        gap: 6px !important;
        margin-top: 20px !important;
        height: 16px !important;
        position: static !important;
        width: 100% !important;
    }
    .car-types-dots-wrap .swiper-pagination-bullet {
        width: 9px !important;
        height: 9px !important;
        border-radius: 50% !important;
        background: #d1d5db !important;
        opacity: 1 !important;
        transition: all 0.3s ease !important;
        cursor: pointer !important;
        display: inline-block !important;
        margin: 0 !important;
    }
    .car-types-dots-wrap .swiper-pagination-bullet-active {
        background: #111827 !important;
        width: 26px !important;
        border-radius: 5px !important;
    }

    html[data-bs-theme="dark"] .shortcode-car-types .heading-3,
    html[data-theme="dark"] .shortcode-car-types .heading-3 { color: #ffffff !important; }
    html[data-bs-theme="dark"] .shortcode-car-types .shortcode-subtitle,
    html[data-theme="dark"] .shortcode-car-types .shortcode-subtitle { color: #94a3b8 !important; }
    html[data-bs-theme="dark"] .car-types-needle {
        background: linear-gradient(90deg, #4a3048 0%, #d4a0cc 100%) !important;
    }
    html[data-bs-theme="dark"] .car-types-needle.right {
        background: linear-gradient(90deg, #d4a0cc 0%, #4a3048 100%) !important;
    }
    html[data-bs-theme="dark"] .car-type-card .card-title,
    html[data-theme="dark"] .car-type-card .card-title { color: #f1f5f9 !important; }
    html[data-bs-theme="dark"] .car-type-card:hover .card-title,
    html[data-theme="dark"] .car-type-card:hover .card-title { color: #d84a38 !important; }
    html[data-bs-theme="dark"] .car-type-card .card-meta,
    html[data-theme="dark"] .car-type-card .card-meta { color: #94a3b8 !important; }
    html[data-bs-theme="dark"] .car-type-card .card-arrow-inner,
    html[data-theme="dark"] .car-type-card .card-arrow-inner {
        background: rgba(255,255,255,0.18) !important;
        color: #ffffff !important;
    }
    html[data-bs-theme="dark"] .car-type-card:hover .card-arrow-inner,
    html[data-theme="dark"] .car-type-card:hover .card-arrow-inner { background: #d84a38 !important; }
    html[data-bs-theme="dark"] .car-types-nav-btn,
    html[data-theme="dark"] .car-types-nav-btn {
        background: #1e293b !important;
        border-color: rgba(255,255,255,0.2) !important;
        color: #ffffff !important;
    }
    html[data-bs-theme="dark"] .car-types-dots-wrap .swiper-pagination-bullet,
    html[data-theme="dark"] .car-types-dots-wrap .swiper-pagination-bullet { background: #475569 !important; }
    html[data-bs-theme="dark"] .car-types-dots-wrap .swiper-pagination-bullet-active,
    html[data-theme="dark"] .car-types-dots-wrap .swiper-pagination-bullet-active { background: #f1f5f9 !important; }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-car-types car-type-style-1 section-box background-body py-5">
    <div class="container">

        {{-- TITLE WITH NEEDLE — center aligned like Zoomcar --}}
        <div class="slider-header-flex wow fadeInUp">
            <div class="car-types-title-wrap">
                <span class="car-types-needle"></span>
                @if(!empty($title))
                    <h2 class="heading-3 shortcode-title">{!! BaseHelper::clean($title) !!}</h2>
                @endif
                <span class="car-types-needle right"></span>
            </div>
            @if(!empty($subTitle))
                <p class="shortcode-subtitle">{!! BaseHelper::clean($subTitle) !!}</p>
            @endif
        </div>

        <div class="car-types-outer">

            <div class="car-types-nav-btn car-types-prev">
                <x-core::icon name="ti ti-arrow-left" size="16" />
            </div>

            <div class="swiper car-types-slider">
                <div class="swiper-wrapper">
                    @foreach($carTypes as $carType)
                        @php
                            $labelCar = ($carType->cars_count > 1 || $carType->cars_count == 0) ? __('Vehicles') : __('Vehicle');
                            $redirectUrl = "{$shortcode->redirect_url}?car_types[]={$carType->id}"
                        @endphp
                        <div class="swiper-slide">
                            <a href="{{ $redirectUrl }}" class="car-type-card">

                                @if(!empty($carType->image))
                                    <div class="card-image">
                                        {{ RvMedia::image($carType->image, $carType->name) }}
                                    </div>
                                    <div class="card-arrow">
                                        <div class="card-arrow-inner">
                                            <x-core::icon name="ti ti-arrow-up-right" size="17" />
                                        </div>
                                    </div>
                                @endif

                                <div class="card-info">
                                    @if(!empty($carType->name))
                                        <div class="card-title">{!! BaseHelper::clean($carType->name) !!}</div>
                                    @endif
                                    <div class="card-meta">
                                        {!! BaseHelper::clean($carType->cars_count ?: 0) !!} {{ $labelCar }}
                                    </div>
                                </div>

                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="car-types-nav-btn car-types-next">
                <x-core::icon name="ti ti-arrow-right" size="16" />
            </div>

        </div>

        {{-- DOTS: center, outside everything --}}
        <div class="swiper-pagination car-types-dots-wrap"></div>

    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Swiper !== 'undefined') {
            new Swiper('.car-types-slider', {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                autoplay: {
                    delay: 3000,
                    disableOnInteraction: false,
                    pauseOnMouseEnter: true,
                },
                navigation: {
                    nextEl: '.car-types-next',
                    prevEl: '.car-types-prev',
                },
                pagination: {
                    el: '.car-types-dots-wrap',
                    clickable: true,
                },
                breakpoints: {
                    576: { slidesPerView: 1 },
                    992: { slidesPerView: 2 },
                    1200: { slidesPerView: 3 }
                }
            });
        }
    });
</script>