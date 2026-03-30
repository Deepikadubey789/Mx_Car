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

    /* The Main Card Container - NOW OFF-WHITE */
    .turo-zoom-card {
        background: #f8fafc !important; /* Subtle off-white background */
        border-radius: 12px !important; 
        border: 1px solid #e2e8f0 !important; /* Slightly darker border to frame the off-white */
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
        border-color: #cbd5e1 !important; /* Slightly darker border on hover */
    }

    /* Image Wrapper */
    .turo-zoom-card .card-image {
        position: relative;
        width: 100%;
        height: 200px; 
        background: #ffffff; /* Keeps image area clean */
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
    
    .turo-zoom-card .card-program > ul,
    .turo-zoom-card .card-program > div,
    .turo-zoom-card .card-program .facilities-list {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important; 
        column-gap: 8px !important;
        row-gap: 8px !important;
        padding: 0 !important;
        margin: 0 !important;
        list-style: none !important;
        width: 100% !important;
    }

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
        border-top: 1px solid #e2e8f0 !important; /* Adjusted to match outer border */
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-cars car-style-feature section-box box-flights background-body py-5">
    <div class="container">
        
        <div class="row align-items-end mb-4">
            <div class="col-md-8">
                @if(!empty($title))
                    <h2 class="heading-3 shortcode-title wow fadeInUp">{!! BaseHelper::clean($title) !!}</h2>
                @endif
                @if(!empty($subtitle))
                    <p class="text-lg-medium shortcode-subtitle mt-2 wow fadeInUp">{!! BaseHelper::clean($subtitle) !!}</p>
                @endif
            </div>
            
            @if(empty($buttonLabel) === false)
                <div class="col-md-4 mt-md-0 mt-4">
                    <div class="d-flex justify-content-md-end">
                        <a class="btn btn-primary wow fadeInUp" href="{{ $buttonUrl }}">
                            {!! BaseHelper::clean($buttonLabel) !!}
                            <x-core::icon name="ti ti-arrow-right" class="ms-2" size="18" />
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <div class="row pt-30">
            @foreach($cars as $car)
                <div class="col-lg-3 col-md-6 mb-4 wow fadeIn" data-wow-delay="0.1s">
                    
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
        </div>
        
    </div>
</section>