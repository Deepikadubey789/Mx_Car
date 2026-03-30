@php
    $buttonName = $shortcode->button_label;
    $buttonUrl = $shortcode->button_url;
    $title = $shortcode->title;
    $mainContent = $shortcode->main_content;
    $redirectUrl = $shortcode->redirect_url ?: '';
@endphp

{{-- FRONTEND CUSTOM CSS SCOPED ONLY TO THE LOCATIONS SECTION --}}
<style>
    /* Section Typography */
    .shortcode-cars-by-locations .heading-3 {
        font-weight: 800 !important;
        color: #111827 !important;
        letter-spacing: -0.5px;
    }
    .shortcode-cars-by-locations .text-lg-medium {
        color: #6b7280 !important;
        font-weight: 500 !important;
    }

    /* Immersive Location Card */
    .location-turo-card {
        position: relative;
        border-radius: 16px !important;
        overflow: hidden;
        height: 340px; /* Taller, vertical aspect ratio like Turo destinations */
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: block; /* Makes the whole card a clickable link */
        background: #000; /* Fallback */
    }

    /* Hover Physics */
    .location-turo-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    }

    /* Full-Cover Image */
    .location-turo-card .card-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }
    .location-turo-card .card-image img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        transition: transform 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }
    .location-turo-card:hover .card-image img {
        transform: scale(1.05); /* Smooth zoom on hover */
    }

    /* Dark Gradient Overlay for Text Readability */
    .location-turo-card .gradient-overlay {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 60%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0) 100%);
        z-index: 1;
        transition: opacity 0.3s ease;
    }

    /* Text Content */
    .location-turo-card .card-content {
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        padding: 24px 20px;
        z-index: 2;
        display: flex;
        flex-direction: column;
    }

    .location-turo-card .location-title {
        color: #ffffff !important;
        font-size: 1.5rem !important;
        font-weight: 700 !important;
        margin-bottom: 4px !important;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }

    .location-turo-card .location-count {
        color: rgba(255, 255, 255, 0.85) !important;
        font-size: 0.95rem !important;
        font-weight: 500 !important;
    }

    /* Restyled Swiper Navigation Arrows */
    .shortcode-cars-by-locations .swiper-button-prev,
    .shortcode-cars-by-locations .swiper-button-next {
        background: #ffffff !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 50% !important;
        width: 40px !important;
        height: 40px !important;
        color: #374151 !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05) !important;
        transition: all 0.2s ease;
        margin-top: -20px !important; /* Vertically center them on the slider */
    }
    .shortcode-cars-by-locations .swiper-button-prev:hover,
    .shortcode-cars-by-locations .swiper-button-next:hover {
        border-color: #111827 !important;
        color: #111827 !important;
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-cars-by-locations section-box box-properties-area pt-96 pb-50 background-body">
    <div class="container">
        
        <div class="row align-items-end mb-40">
            <div class="col-md-8">
                @if($title)
                    <h2 class="heading-3 neutral-1000 wow fadeInUp">{!! BaseHelper::clean($title) !!}</h2>
                @endif

                @if($mainContent)
                    <p class="text-lg-medium neutral-500 mt-2 wow fadeInUp" data-wow-delay="0.1s">{!! BaseHelper::clean($mainContent) !!}</p>
                @endif
            </div>
            
            @if($buttonName && $buttonUrl)
                <div class="col-md-4 mt-md-0 mt-4 wow fadeInUp" data-wow-delay="0.2s">
                    <div class="d-flex justify-content-md-end justify-content-start">
                        <a class="btn btn-primary" href="{{ $buttonUrl }}">
                            {!! BaseHelper::clean($buttonName) !!}
                            <svg class="svg-icon-arrow ms-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 15L15 8L8 1M15 8L1 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="box-list-featured wow fadeInUp" data-wow-delay="0.2s">
            <div class="box-swiper mt-0 position-relative">
                <div class="swiper-container swiper-group-cars-locations swiper-group-journey pb-4">
                    <div class="swiper-wrapper">
                        @foreach($locations as $location)
                            @php
                                $name = $location['name'] ?? '';
                                $image = $location['image'] ?? '';
                                $countCar = $location['count_cars'] ?? 0;
                                $countCarLabel = $countCar === 1 ? __('Vehicle') : __('Vehicles');
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
                                        <div class="location-count">{{ $countCar }} {{ $countCarLabel }}</div>
                                    </div>
                                </a>
                                </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="swiper-button-prev swiper-button-prev-style-1 swiper-button-prev-cars-locations position-absolute top-50 start-0 translate-middle-y z-3 ms-n3">
                    <x-core::icon name="ti ti-arrow-left" size="18"/>
                </div>
                <div class="swiper-button-next swiper-button-next-style-1 swiper-button-next-cars-locations position-absolute top-50 end-0 translate-middle-y z-3 me-n3">
                    <x-core::icon name="ti ti-arrow-right" size="18"/>
                </div>
                
            </div>
        </div>
        
    </div>
</section>