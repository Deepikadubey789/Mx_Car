@php
    $carsChunkSize = $cars->chunk($shortcode->number_rows);
@endphp

{{-- FRONTEND CUSTOM CSS SCOPED ONLY TO THE CAR CARDS --}}
{{-- FRONTEND CUSTOM CSS SCOPED ONLY TO THE CAR CARDS --}}
<style>
    /* Section Typography */
    .shortcode-cars .heading-3 {
        font-weight: 800 !important;
        color: #111827 !important;
        letter-spacing: -0.5px;
    }
    .shortcode-cars .shortcode-subtitle {
        color: #6b7280 !important;
        font-weight: 500 !important;
    }

    /* Navigation Arrows (Turo Style) */
    .box-button-slider-team .swiper-button-prev,
    .box-button-slider-team .swiper-button-next {
        background: #ffffff !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 50% !important;
        width: 40px !important;
        height: 40px !important;
        color: #374151 !important;
        box-shadow: 0 2px 6px rgba(0,0,0,0.05) !important;
        transition: all 0.2s ease;
    }
    .box-button-slider-team .swiper-button-prev:hover,
    .box-button-slider-team .swiper-button-next:hover {
        border-color: #111827 !important;
        color: #111827 !important;
    }

    /* The Main Card Container */
    .turo-zoom-card {
        background: #ffffff !important;
        border-radius: 12px !important; 
        border: 1px solid #f3f4f6 !important;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        height: 100%; 
    }

    /* Hover Lift & Shadow */
    .turo-zoom-card:hover {
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08) !important;
        transform: translateY(-4px);
        border-color: #e5e7eb !important;
    }

    /* Image Wrapper */
    .turo-zoom-card .card-image {
        position: relative;
        width: 100%;
        height: 200px; 
    }
    
    .turo-zoom-card .card-image img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important; 
    }

    /* Optional: Zoomcar style favorite button */
    .turo-zoom-card .favorite-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(0, 0, 0, 0.2);
        backdrop-filter: blur(4px);
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        transition: background 0.2s;
        z-index: 10;
    }
    .turo-zoom-card .favorite-btn:hover {
        background: rgba(0, 0, 0, 0.5);
    }

    /* Info Area */
    .turo-zoom-card .card-info {
        padding: 16px 20px 20px !important;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    /* Title */
    .turo-zoom-card .card-title {
        font-size: 1.15rem !important;
        font-weight: 700 !important;
        color: #111827 !important;
        margin-bottom: 6px !important;
        line-height: 1.3 !important;
    }
    .turo-zoom-card .card-title a {
        color: inherit !important;
        text-decoration: none !important;
    }

    /* Meta Row (Location & Rating Inline) */
    .turo-zoom-card .card-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 12px;
        flex-wrap: wrap;
    }
    
    .turo-zoom-card .card-location, 
    .turo-zoom-card .card-rating {
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* === NUCLEAR 2-COLUMN SPECIFICATIONS GRID === */
    .turo-zoom-card .card-program {
        margin-bottom: 16px;
    }
    
    /* This targets any ul, div, or container directly inside .card-program 
       that the theme uses to hold the features/specifications 
    */
    .turo-zoom-card .card-program > ul,
    .turo-zoom-card .card-program > div,
    .turo-zoom-card .card-program .facilities-list {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important; /* Forces exactly two equal columns */
        column-gap: 8px !important;
        row-gap: 8px !important;
        padding: 0 !important;
        margin: 0 !important;
        list-style: none !important;
        width: 100% !important;
    }

    /* This styles the individual list items / spans inside the grid */
    .turo-zoom-card .card-program li,
    .turo-zoom-card .card-program > div > div,
    .turo-zoom-card .card-program > div > span {
        display: flex !important;
        align-items: center !important;
        font-size: 0.85rem !important;
        color: #4b5563 !important;
        margin: 0 !important;
        padding: 0 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
    }
    
    /* Adjusts the icons inside the specs */
    .turo-zoom-card .card-program i,
    .turo-zoom-card .card-program svg {
        font-size: 14px !important;
        color: #9ca3af !important;
        margin-right: 6px !important;
        flex-shrink: 0 !important;
    }

    /* Bottom Price & Button Area */
    .turo-zoom-card .endtime {
        margin-top: auto !important; 
        padding-top: 16px !important;
        border-top: 1px solid #f3f4f6 !important;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-cars car-style-lasted section-box box-flights background-body">
    <div class="container">
        
        <div class="row align-items-end mb-4">
            <div class="col-md-9 wow fadeInUp">
                @if ($title)
                    <h2 class="heading-3 title-svg shortcode-title">{!! BaseHelper::clean($title) !!}</h2>
                @endif

                @if ($subtitle)
                    <p class="text-lg-medium shortcode-subtitle mt-2">{!! BaseHelper::clean($subtitle) !!}</p>
                @endif
            </div>
            <div class="col-md-3 position-relative wow fadeInUp">
                <div class="box-button-slider box-button-slider-team justify-content-end">
                    <div class="swiper-button-prev swiper-button-prev-style-1 swiper-button-prev-2">
                        <x-core::icon name="ti ti-arrow-left" size="18"/>
                    </div>
                    <div class="swiper-button-next swiper-button-next-style-1 swiper-button-next-2">
                        <x-core::icon name="ti ti-arrow-right" size="18"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="block-flights wow fadeInUp" data-wow-delay="0.1s">
            <div class="box-swiper mt-20">
                <div class="swiper-container swiper-group-4 swiper-group-journey pb-4">
                    <div class="swiper-wrapper">
                        @foreach($carsChunkSize as $cars)
                            @foreach($cars as $car)
                                <div class="swiper-slide h-auto"> 
                                    <div class="turo-zoom-card">
                                        
                                        <div class="card-image">
                                            <a href="{{ $car->url }}">
                                                {{ RvMedia::image($car->image, $car->name, 'medium-rectangle') }}
                                            </a>
                                            
                                            <div class="favorite-btn">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
                                                </svg>
                                            </div>
                                        </div>
                                        
                                        <div class="card-info">
                                            
                                            <div class="card-title">
                                                <a class="text-ellipsis-2-lines" href="{{ $car->url }}">
                                                    {!! BaseHelper::clean($car->name) !!}
                                                </a>
                                            </div>
                                            
                                            <div class="card-meta">
                                                @if($car->location)
                                                    <div class="card-location text-truncate" style="max-width: 140px;" title="{{ $car->location }}">
                                                        <x-core::icon name="ti ti-map-pin" size="14" />
                                                        {{ BaseHelper::clean($car->location) }}
                                                    </div>
                                                @endif
                                                
                                                <div class="card-rating">
                                                    @include(Theme::getThemeNamespace('views.car-rentals.rating'), ['car' => $car])
                                                </div>
                                            </div>
                                            
                                            <div class="card-program">
                                                @include(Theme::getThemeNamespace('views.car-rentals.car-facilities'), ['car' => $car])
                                            </div>

                                            <div class="endtime">
                                                @include(Theme::getThemeNamespace('views.car-rentals.price'), ['car' => $car])
                                                @include(Theme::getThemeNamespace('views.car-rentals.book-now-button'), ['car' => $car])
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        @if(empty($buttonLabel) === false)
            <div class="d-flex justify-content-center mt-3">
                <a class="btn btn-primary btn-brand-2 wow fadeInUp" href="{{ $buttonUrl }}">
                    {!! BaseHelper::clean($buttonLabel) !!}
                    <x-core::icon name="ti ti-arrow-right" class="ms-2" size="18" />
                </a>
            </div>
        @endif
        
    </div>
</section>