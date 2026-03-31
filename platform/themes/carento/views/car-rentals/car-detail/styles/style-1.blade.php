@use(Botble\Theme\Supports\Youtube;)

@php
    Theme::set('breadcrumbs', false);
    Theme::layout('full-width');

    $youtubeUrl = $car->getMetaData('youtube_video_url', true);

    $youtubeId = $youtubeUrl ? Youtube::getYoutubeVideoID($youtubeUrl) : null;

    $images = $car->getImages();
@endphp

<div class="car-detail-page car-detail-page--style-1">
    <style>
        /* MXCar Ultra-Clean Single Car Page Brand Overrides */
        .car-detail-page--style-1,
        .car-detail-page--style-1 .background-body {
            background-color: #FDFBF8 !important; /* Soft premium cream background */
        }

        .car-detail-modern__header,
        .car-detail-modern__layout .col-lg-8 > div,
        .car-detail-modern__sidebar .car-detail-modern__sidebar-stack > div {
            background-color: #ffffff !important;
            border: 1px solid #E9ECEF !important;
            border-radius: 20px !important;
            padding: 30px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02) !important;
            margin-bottom: 24px;
        }

        /* Title & Logo Colors */
        .car-detail-page--style-1 h4, 
        .car-detail-page--style-1 h1, 
        .car-detail-page--style-1 h2, 
        .car-detail-page--style-1 h3, 
        .car-detail-page--style-1 .neutral-1000 {
            color: #000000 !important; /* Standard MXCar Logo Black */
            font-weight: 700 !important;
        }

        .car-detail-modern__eyebrow {
            color: #B03A2E !important; /* Logo Red for eyebrow text */
            font-weight: 600 !important;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Icon & Badge Colors */
        .car-detail-page--style-1 .icon-tabler-star,
        .car-detail-page--style-1 .rate-element svg,
        .car-detail-page--style-1 .icon-tabler-map-pin,
        .car-detail-page--style-1 .tour-location svg {
            color: #B03A2E !important; /* Brand red for primary signals */
        }

        .car-detail-page--style-1 .card-program-info,
        .car-detail-page--style-1 .item-attribute {
            background-color: #FDFBF8 !important;
            border: 1px solid #E9ECEF !important;
            border-radius: 12px !important;
            padding: 15px !important;
        }

        /* Buttons & CTA */
        .car-detail-page--style-1 .btn-primary,
        .car-detail-page--style-1 .btn-book-now,
        .car-detail-page--style-1 .booking-form button[type="submit"] {
            background-color: #B03A2E !important;
            border-color: #B03A2E !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            padding: 14px 24px !important;
            border-radius: 10px !important;
            transition: all 0.3s ease;
        }

        .car-detail-page--style-1 .btn-primary:hover {
            background-color: #8E2B21 !important;
            box-shadow: 0 10px 20px rgba(176, 58, 46, 0.2) !important;
            transform: translateY(-2px);
        }

        /* Gallery Overrides */
        .car-detail-modern__gallery-main .wrapper-image img {
            border-radius: 20px !important;
        }

        .car-detail-modern__gallery-thumbs .banner-slide img {
            border-radius: 12px !important;
            border: 2px solid transparent;
        }

        .car-detail-modern__gallery-thumbs .slick-current img {
            border-color: #B03A2E !important;
        }

        /* Sidebar Sidebar sticky fix */
        .car-detail-modern__sidebar-stack--sticky {
            top: 100px;
        }

        /* Dark Mode Support */
        [data-bs-theme="dark"] .car-detail-page--style-1,
        [data-bs-theme="dark"] .car-detail-page--style-1 .background-body {
            background-color: transparent !important;
        }
        [data-bs-theme="dark"] .car-detail-modern__header,
        [data-bs-theme="dark"] .car-detail-modern__layout .col-lg-8 > div,
        [data-bs-theme="dark"] .car-detail-modern__sidebar .car-detail-modern__sidebar-stack > div {
            background-color: #1a1a1a !important;
            border-color: #2e2e2e !important;
        }
        [data-bs-theme="dark"] .car-detail-page--style-1 h4, 
        [data-bs-theme="dark"] .car-detail-page--style-1 .neutral-1000 {
            color: #f1f1f1 !important;
        }
    </style>
    @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.breadcrumbs'), compact('car'))

    <div class="section-box box-banner-home2 background-body car-detail-modern__gallery-shell">
        <div class="container">
            <div class="container-banner-activities car-detail-galleries car-detail-modern__gallery-wrap">
                <div class="box-banner-activities car-detail-modern__gallery-main">
                    <div class="banner-activities-detail">
                        @foreach($images as $image)
                            <div class="banner-slide-activity">
                                <div class="wrapper-image">
                                    {{ RvMedia::image($image, $car->name, 'large-rectangle') }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-none d-sm-block">
                        @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.gallery-buttons'), ['car' => $car])
                    </div>
                </div>

                <div class="slider-thumnail-activities car-detail-modern__gallery-thumbs">
                    <div class="slider-nav-thumbnails-activities-detail">
                        @foreach($images as $image)
                            <div class="banner-slide">
                                {{ RvMedia::image($image, $car->name, 'medium-rectangle') }}
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="d-block d-sm-none">
                    @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.gallery-buttons'), ['car' => $car, 'renderLightboxLinks' => false])
                </div>
            </div>
        </div>
    </div>

    <div class="box-section box-content-tour-detail background-body car-detail-modern__content-shell">
        <div class="container">
            <div class="tour-header car-detail-modern__header">
                @if ($car->reviews_count)
                    <div class="tour-rate">
                        <div class="rate-element">
                            @include(Theme::getThemeNamespace('views.car-rentals.rating'), ['car' => $car])
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-8">
                        <div class="tour-title-main car-detail-modern__title-wrap">
                            <p class="car-detail-modern__eyebrow mb-0">{{ __('Featured vehicle') }}</p>
                            <h4 class="neutral-1000">{{ $car->name }}</h4>

                            <div class="car-detail-modern__quick-meta">
                                @if($car->year)
                                    <span>{{ $car->year }}</span>
                                @endif

                                @if($car->transmission)
                                    <span>{!! BaseHelper::clean($car->transmission->name) !!}</span>
                                @endif

                                @if($car->fuel)
                                    <span>{!! BaseHelper::clean($car->fuel->name) !!}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tour-metas car-detail-modern__meta-row">
                    <div class="tour-meta-left">
                        @if ($car->current_location)
                            <p class="text-md-medium neutral-1000 mr-20 tour-location">
                                {!! BaseHelper::renderIcon('ti ti-map-pin') !!}

                                {!! BaseHelper::clean($car->current_location) !!}
                            </p>
                            <a class="text-md-medium neutral-1000 mr-30" href="https://maps.google.com/maps?q={{ addslashes($car->current_location) }}">{{ __('Show on map') }}</a>
                        @endif
                    </div>
                    <div>
                        @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.share-button'), compact('car'))
                    </div>
                </div>
            </div>

            <div class="row mt-20 car-detail-modern__layout">
                <div class="col-lg-8">
                    @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.attributes'), compact('car'))

                    @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.additional-info'), compact('car'))

                    @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.amenities'), compact('car'))

                    <div class="box-collapse-expand">
                        @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.content'), compact('car'))

                        @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.owner-info'), compact('car'))

                        @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.faqs'), compact('car'))

                        @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.reviews'), compact('car', 'reviews'))
                    </div>
                </div>
                <div class="col-lg-4 car-detail-modern__sidebar">
                    <div class="car-detail-modern__sidebar-stack car-detail-modern__sidebar-stack--sticky">
                        @if($car->is_for_sale && get_car_rentals_setting('enabled_car_sale', true))
                            @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.sale-info'), compact('car'))
                        @elseif(!$car->is_for_sale && CarRentalsHelper::isRentalBookingEnabled())
                            @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.booking-form'), compact('car'))
                        @endif

                        @include(Theme::getThemeNamespace('views.car-rentals.message-form'), compact('car'))
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
