{{-- FRONTEND CUSTOM CSS SCOPED ONLY TO CAR TYPES --}}
<style>
    /* Section Typography */
    .shortcode-car-types .heading-3 {
        font-weight: 800 !important;
        color: #111827 !important;
        letter-spacing: -0.5px;
    }
    .shortcode-car-types .shortcode-subtitle {
        color: #6b7280 !important;
        font-weight: 500 !important;
    }

    /* Car Type Modern Card */
    .car-type-modern-card {
        background: #f8fafc !important; /* Matches your off-white car cards */
        border-radius: 16px !important; 
        border: 1px solid #e2e8f0 !important;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
        text-decoration: none !important; /* Whole card is a link */
        margin-bottom: 24px;
    }

    /* Hover Lift & Shadow */
    .car-type-modern-card:hover {
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06) !important;
        transform: translateY(-4px);
        border-color: #cbd5e1 !important;
    }

    /* Image Wrapper */
    .car-type-modern-card .card-image {
        position: relative;
        width: 100%;
        height: 180px; /* Forces uniform image height */
        background: #ffffff; 
        overflow: hidden;
    }
    .car-type-modern-card .card-image img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important; 
        transition: transform 0.5s ease;
    }
    .car-type-modern-card:hover .card-image img {
        transform: scale(1.05); /* Smooth zoom on hover */
    }

    /* Info Area */
    .car-type-modern-card .card-info {
        padding: 24px 20px !important;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    /* Category Title */
    .car-type-modern-card .card-title {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #111827 !important;
        margin-bottom: 6px !important;
        transition: color 0.2s ease;
    }
    .car-type-modern-card:hover .card-title {
        color: #d84a38 !important; /* Turns brand red on hover */
    }

    /* Vehicle Count */
    .car-type-modern-card .card-meta {
        font-size: 0.95rem !important;
        color: #64748b !important;
        font-weight: 500 !important;
    }
    
    /* Circular Arrow Icon */
    .car-type-modern-card .arrow-icon {
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
    .car-type-modern-card:hover .arrow-icon {
        background: #d84a38;
        border-color: #d84a38;
        color: #ffffff;
        transform: translateX(4px); /* Slides right playfully */
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-car-types car-type-style-1 section-box background-body py-5">
    <div class="container">
        
        <div class="row align-items-end mb-4">
            <div class="col-md-8">
                @if(empty($title) === false)
                    <h2 class="heading-3 shortcode-title wow fadeInUp">{!! BaseHelper::clean($title) !!}</h2>
                @endif
                @if(empty($subTitle) === false)
                    <p class="text-xl-medium shortcode-subtitle mt-2 wow fadeInUp">{!! BaseHelper::clean($subTitle) !!}</p>
                @endif
            </div>

            @if(empty($buttonLabel) === false)
                <div class="col-md-4">
                    <div class="d-flex justify-content-md-end mt-md-0 mt-4">
                        <a class="btn btn-primary wow fadeInUp" href="{{ $shortcode->button_url }}">
                            {!! BaseHelper::clean($buttonLabel) !!}
                            <x-core::icon name="ti ti-arrow-right" class="ms-2" size="18" />
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <div class="box-list-populars pt-30">
            <div class="row">
                @foreach($carTypes as $carType)
                    @php
                        $labelCar = ($carType->cars_count > 1 || $carType->cars_count == 0) ? __('Vehicles') : __('Vehicle');
                        $redirectUrl = "{$shortcode->redirect_url}?car_types[]={$carType->id}"
                    @endphp
                    
                    <div class="col-lg-3 col-sm-6">
                        <a href="{{ $redirectUrl }}" class="car-type-modern-card wow fadeIn" data-wow-delay="0.1s">
                            
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
</section>