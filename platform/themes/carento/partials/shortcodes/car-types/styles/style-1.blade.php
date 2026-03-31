{{-- FRONTEND CUSTOM CSS SCOPED ONLY TO CAR TYPES --}}
<style>
    /* Section Typography */
    .shortcode-car-types .heading-3 {
        font-weight: 800 !important;
        color: #111827 !important;
        letter-spacing: -0.5px;
        margin-bottom: 0.5rem;
        transition: color 0.3s ease;
    }
    .shortcode-car-types .shortcode-subtitle {
        color: #6b7280 !important;
        font-weight: 500 !important;
        margin-bottom: 0;
        transition: color 0.3s ease;
    }

    /* Car Type Card Container */
    .car-type-card {
        background: #f8fafc !important; 
        border-radius: 12px !important; 
        border: 1px solid #e2e8f0 !important; 
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        height: 100%; 
        text-decoration: none !important;
    }

    /* Hover Lift & Shadow */
    .car-type-card:hover {
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08) !important;
        transform: translateY(-4px);
        border-color: #cbd5e1 !important; 
    }

    /* Image Wrapper */
    .car-type-card .card-image {
        position: relative;
        width: 100%;
        height: 160px; /* Perfect height for car type images */
        background: #ffffff; 
        overflow: hidden;
    }
    
    .car-type-card .card-image img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important; 
        transition: transform 0.5s ease;
    }
    .car-type-card:hover .card-image img {
        transform: scale(1.05); 
    }

    /* Info Area */
    .car-type-card .card-info {
        padding: 20px !important;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        flex-grow: 1;
    }

    /* Title (e.g., SUV, Sedan) */
    .car-type-card .card-title {
        font-size: 1.15rem !important;
        font-weight: 700 !important;
        color: #111827 !important;
        margin-bottom: 4px !important;
        line-height: 1.3 !important;
        transition: color 0.2s ease;
    }
    .car-type-card:hover .card-title {
        color: #d84a38 !important; 
    }

    /* Meta (e.g., 18 Vehicles) */
    .car-type-card .card-meta {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
        transition: color 0.3s ease;
    }

    /* Circular Arrow Icon */
    .car-type-card .arrow-icon {
        margin-top: 16px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #111827;
        transition: all 0.3s ease;
    }
    .car-type-card:hover .arrow-icon {
        background: #d84a38;
        border-color: #d84a38;
        color: #ffffff;
        transform: translateX(4px); 
    }

    /* =========================================
       SLIDER NAVIGATION & CONTAINMENT
       ========================================= */
    .slider-header-flex {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 24px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .car-types-nav-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #111827;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .car-types-nav-btn:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    /* STOPS THE SLIDER FROM BLEEDING OFF THE PAGE */
    .car-types-slider {
        padding: 15px 5px !important; 
        margin: -15px -5px !important; 
        overflow: hidden !important; 
    }

    /* =========================================
       DARK MODE OVERRIDES
       ========================================= */
    html[data-bs-theme="dark"] .shortcode-car-types .heading-3,
    html[data-theme="dark"] .shortcode-car-types .heading-3 {
        color: #ffffff !important;
    }
    html[data-bs-theme="dark"] .shortcode-car-types .shortcode-subtitle,
    html[data-theme="dark"] .shortcode-car-types .shortcode-subtitle {
        color: #94a3b8 !important;
    }

    /* Dark Mode Card */
    html[data-bs-theme="dark"] .car-type-card,
    html[data-theme="dark"] .car-type-card {
        background: #1e293b !important; 
        border-color: rgba(255, 255, 255, 0.08) !important;
    }
    html[data-bs-theme="dark"] .car-type-card:hover,
    html[data-theme="dark"] .car-type-card:hover {
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.4) !important;
        border-color: rgba(255, 255, 255, 0.15) !important;
    }

    /* Dark Mode Text */
    html[data-bs-theme="dark"] .car-type-card .card-title,
    html[data-theme="dark"] .car-type-card .card-title {
        color: #f8fafc !important;
    }
    html[data-bs-theme="dark"] .car-type-card:hover .card-title,
    html[data-theme="dark"] .car-type-card:hover .card-title {
        color: #d84a38 !important;
    }
    html[data-bs-theme="dark"] .car-type-card .card-meta,
    html[data-theme="dark"] .car-type-card .card-meta {
        color: #94a3b8 !important;
    }

    /* Dark Mode Arrow Icon inside Card */
    html[data-bs-theme="dark"] .car-type-card .arrow-icon,
    html[data-theme="dark"] .car-type-card .arrow-icon {
        background: rgba(255, 255, 255, 0.05);
        border-color: transparent;
        color: #ffffff;
    }
    html[data-bs-theme="dark"] .car-type-card:hover .arrow-icon,
    html[data-theme="dark"] .car-type-card:hover .arrow-icon {
        background: #d84a38;
        color: #ffffff;
    }

    /* Dark Mode Navigation Arrows */
    html[data-bs-theme="dark"] .car-types-nav-btn,
    html[data-theme="dark"] .car-types-nav-btn {
        background: transparent;
        border-color: rgba(255, 255, 255, 0.2);
        color: #ffffff;
    }
    html[data-bs-theme="dark"] .car-types-nav-btn:hover,
    html[data-theme="dark"] .car-types-nav-btn:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.3);
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-car-types car-type-style-1 section-box background-body py-5">
    <div class="container">
        
        <div class="slider-header-flex wow fadeInUp">
            <div>
                @if(!empty($title))
                    <h2 class="heading-3 shortcode-title">{!! BaseHelper::clean($title) !!}</h2>
                @endif
                @if(!empty($subTitle))
                    <p class="shortcode-subtitle">{!! BaseHelper::clean($subTitle) !!}</p>
                @endif
            </div>
            
            <div class="d-flex align-items-center gap-2">
                <div class="car-types-nav-btn car-types-prev">
                    <x-core::icon name="ti ti-arrow-left" size="18" />
                </div>
                <div class="car-types-nav-btn car-types-next">
                    <x-core::icon name="ti ti-arrow-right" size="18" />
                </div>
            </div>
        </div>

        <div class="box-list-populars">
            <div class="swiper car-types-slider">
                <div class="swiper-wrapper">
                    @foreach($carTypes as $carType)
                        @php
                            $labelCar = ($carType->cars_count > 1 || $carType->cars_count == 0) ? __('Vehicles') : __('Vehicle');
                            $redirectUrl = "{$shortcode->redirect_url}?car_types[]={$carType->id}"
                        @endphp
                        
                        <div class="swiper-slide">
                            <a href="{{ $redirectUrl }}" class="car-type-card wow fadeIn" data-wow-delay="0.1s">
                                
                                @if(!empty($carType->image))
                                    <div class="card-image">
                                        {{ RvMedia::image($carType->image, $carType->name) }}
                                    </div>
                                @endif
                                
                                <div class="card-info">
                                    @if(!empty($carType->name))
                                        <div class="card-title">{!! BaseHelper::clean($carType->name) !!}</div>
                                    @endif
                                    
                                    <div class="card-meta">
                                        {!! BaseHelper::clean($carType->cars_count ?: 0) !!} {{ $labelCar }}
                                    </div>
                                    
                                    <div class="arrow-icon">
                                        <x-core::icon name="ti ti-arrow-right" size="18" />
                                    </div>
                                </div>
                                
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            </div>
        
    </div>
</section>

{{-- INITIALIZE SLIDER SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swiper !== 'undefined') {
            new Swiper('.car-types-slider', {
                slidesPerView: 1,
                spaceBetween: 24,
                loop: false,
                navigation: {
                    nextEl: '.car-types-next',
                    prevEl: '.car-types-prev',
                },
                breakpoints: {
                    576: {
                        slidesPerView: 2,
                    },
                    992: {
                        slidesPerView: 3,
                    },
                    1200: {
                        slidesPerView: 4,
                    }
                }
            });
        } else {
            console.warn('Swiper.js is missing. Slider will not initialize.');
        }
    });
</script>