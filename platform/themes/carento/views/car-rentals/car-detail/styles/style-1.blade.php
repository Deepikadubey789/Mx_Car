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
        /* ═══════════════════════════════════════════════════════════════════
           MXCar Car Detail — Brand Consistent Redesign (v2)
           Class names verified from:
             includes/attributes.blade.php     → .car-detail-modern__spec-pill
             includes/additional-info.blade.php → .car-detail-modern__badge, .car-detail-modern__collapse-card
             includes/amenities.blade.php       → .car-detail-modern__amenity-item
             includes/booking-form.blade.php    → .head-booking-form, .booking-form--modern
           Color system matching /car-list-1:
             Background: #F4F6F8 | Cards: #fff | Brand red: #B03A2E
        ═══════════════════════════════════════════════════════════════════ */

        /* ── Page & Section Backgrounds ── */
        .car-detail-page--style-1,
        .car-detail-page--style-1 .background-body {
            background-color: #F4F6F8 !important;
        }

        /* ── All white content cards ── */
        .car-detail-modern__header,
        .car-detail-modern__layout .col-lg-8 > div,
        .car-detail-modern__sidebar .car-detail-modern__sidebar-stack > div {
            background-color: #ffffff !important;
            border: 1px solid #E9ECEF !important;
            border-radius: 20px !important;
            padding: 28px 32px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03) !important;
            margin-bottom: 20px;
        }

        /* ── Typography ── */
        .car-detail-page--style-1 h1,
        .car-detail-page--style-1 h2,
        .car-detail-page--style-1 h3,
        .car-detail-page--style-1 h4,
        .car-detail-page--style-1 h5,
        .car-detail-page--style-1 .neutral-1000 {
            color: #111111 !important;
            font-weight: 700 !important;
        }

        /* ── Brand Red Eyebrow ("FEATURED VEHICLE") ── */
        .car-detail-modern__eyebrow {
            color: #B03A2E !important;
            font-size: 0.72rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 1.2px !important;
        }

        /* ── Quick-meta pills (2015, MANUAL, ELECTRIC) ── */
        .car-detail-modern__quick-meta span {
            display: inline-flex;
            align-items: center;
            background: #F4F6F8 !important;
            border: 1px solid #E9ECEF !important;
            border-radius: 20px !important;
            padding: 4px 14px !important;
            font-size: 0.78rem !important;
            font-weight: 600 !important;
            color: #555 !important;
            margin-right: 6px;
            margin-top: 6px;
        }

        /* ── Spec Attribute Pills (mileage, fuel, seats, doors etc.) ── */
        /* Real class from attributes.blade.php: .item-feature-car-inner / .car-detail-modern__spec-pill */
        .car-detail-modern__spec-pill,
        .item-feature-car-inner {
            background-color: #F8F9FA !important;
            border: 1px solid #E9ECEF !important;
            border-radius: 12px !important;
            padding: 14px 18px !important;
            transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
        }

        .car-detail-modern__spec-pill:hover,
        .item-feature-car-inner:hover {
            border-color: rgba(176, 58, 46, 0.4) !important;
            box-shadow: 0 2px 8px rgba(176, 58, 46, 0.08) !important;
        }

        /* Spec pill icons — brand red */
        .car-detail-modern__spec-pill .feature-image svg,
        .car-detail-modern__spec-pill .feature-image i,
        .item-feature-car-inner .feature-image svg,
        .item-feature-car-inner .feature-image i {
            color: #B03A2E !important;
        }

        /* Spec pill text */
        .car-detail-modern__spec-pill .neutral-1000,
        .item-feature-car-inner .neutral-1000 {
            color: #111111 !important;
            font-weight: 600 !important;
        }

        /* ── Collapse Section Headers (Additional Information, Accessories, Overview) ── */
        /* Real class from additional-info.blade.php: .car-detail-modern__collapse-trigger */
        .car-detail-modern__collapse-trigger {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            width: 100% !important;
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            color: #111111 !important;
            font-weight: 700 !important;
        }

        .car-detail-modern__collapse-trigger strong {
            color: #111111 !important;
            font-size: 1rem !important;
            font-weight: 700 !important;
        }

        .car-detail-modern__collapse-trigger svg path {
            stroke: #B03A2E !important;
        }

        /* ── Collapse card body ── */
        .car-detail-modern__collapse-card {
            background-color: transparent !important;
            border: none !important;
            padding: 16px 0 0 !important;
        }

        /* ── Attribute Badge Pills (Classic, Brown, tags) ── */
        /* Real class from additional-info.blade.php: .car-detail-modern__badge */
        .car-detail-modern__badge {
            display: inline-flex !important;
            align-items: center !important;
            background-color: #FFF5F5 !important;
            color: #B03A2E !important;
            border: 1px solid rgba(176, 58, 46, 0.2) !important;
            border-radius: 6px !important;
            padding: 3px 10px !important;
            font-size: 0.75rem !important;
            font-weight: 600 !important;
        }

        /* Info row icons (folder, palette, calendar, barcode) */
        .car-detail-modern__info-grid .feature-image svg,
        .car-detail-modern__info-grid .feature-image i {
            color: #B03A2E !important;
        }

        /* ── Amenity / Accessory Items ── */
        /* Real class from amenities.blade.php: .car-detail-modern__amenity-item */
        .car-detail-modern__amenity-item {
            display: inline-flex !important;
            align-items: center !important;
            gap: 8px !important;
            background-color: #F8F9FA !important;
            border: 1px solid #E9ECEF !important;
            border-radius: 8px !important;
            padding: 8px 14px !important;
            margin: 4px !important;
            list-style: none !important;
            font-size: 0.875rem !important;
            color: #333 !important;
            transition: border-color 0.15s !important;
        }

        .car-detail-modern__amenity-item:hover {
            border-color: rgba(176, 58, 46, 0.35) !important;
        }

        .car-detail-modern__amenity-item svg,
        .car-detail-modern__amenity-item i {
            color: #B03A2E !important;
        }

        /* Amenity list reset */
        .car-detail-modern__collapse-card ul {
            display: flex !important;
            flex-wrap: wrap !important;
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        /* ── Amenity Category Headings ── */
        .car-detail-modern__amenity-category {
            color: #B03A2E !important;
            font-size: 0.8rem !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.8px !important;
        }

        .car-detail-modern__amenity-category svg,
        .car-detail-modern__amenity-category i {
            color: #B03A2E !important;
        }

        /* ── Star Ratings & Map Pins ── */
        .car-detail-page--style-1 .icon-tabler-star,
        .car-detail-page--style-1 .rate-element svg,
        .car-detail-page--style-1 .icon-tabler-map-pin,
        .car-detail-page--style-1 .tour-location svg,
        .car-detail-page--style-1 .tour-location i {
            color: #B03A2E !important;
        }

        /* ── Booking Sidebar ── */
        /* Real class from booking-form.blade.php: .head-booking-form, .booking-form--modern */
        .car-detail-modern__sidebar-stack > div {
            padding: 28px 32px !important;
            overflow: hidden !important; 
        }

        /* Red Header - Reset to bleed to edges */
        .head-booking-form {
            background: linear-gradient(135deg, #B03A2E 0%, #8E2B21 100%) !important;
            margin: -29px -33px 24px !important; 
            padding: 22px 32px !important;
            border-radius: 20px 20px 0 0 !important;
            display: block !important;
        }

        .head-booking-form p,
        .head-booking-form .text-xl-bold {
            color: #ffffff !important;
            font-size: 1.05rem !important;
            font-weight: 700 !important;
            margin: 0 !important;
        }

        /* AGGRESSIVE Removal of Theme's boxy grey containers — EXCEPT the header */
        .booking-form--modern .content-booking-form div,
        .booking-form--modern .availability-check,
        .booking-form--modern .form-group,
        .booking-form--modern .mb-20 {
            background-color: transparent !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
        }

        /* Labels */
        .booking-form--modern label,
        .car-detail-modern__sidebar label {
            color: #111111 !important;
            font-weight: 600 !important;
            font-size: 0.85rem !important;
            margin-bottom: 8px !important;
            display: block !important;
        }

        /* Form inputs - KEEP subtle grey fill only on the fields */
        .booking-form--modern .form-control,
        .booking-form--modern .form-select,
        .car-detail-modern__sidebar .form-control,
        .car-detail-modern__sidebar .form-select {
            background-color: #F8F9FA !important;
            border: 1px solid #E9ECEF !important;
            border-radius: 10px !important;
            color: #111111 !important;
            height: auto !important;
            padding: 12px 16px !important;
        }


        .booking-form--modern .form-control:focus,
        .booking-form--modern .form-select:focus {
            border-color: #B03A2E !important;
            box-shadow: 0 0 0 3px rgba(176, 58, 46, 0.1) !important;
        }

        /* Additional Services section */
        .booking-form--modern .form-check-label {
            color: #333 !important;
            font-size: 0.875rem !important;
        }

        /* Subtotal / Total */
        .booking-form--modern .text-sm-bold,
        .booking-form--modern .text-heading-5,
        .car-detail-modern__sidebar .text-end .heading-6 {
            color: #111111 !important;
            font-weight: 700 !important;
        }

        /* ── Book Now Button ── */
        .car-detail-page--style-1 .btn-book,
        .car-detail-page--style-1 .btn-primary,
        .car-detail-page--style-1 button[type="submit"].btn {
            background-color: #B03A2E !important;
            border-color: #B03A2E !important;
            color: #ffffff !important;
            font-weight: 700 !important;
            border-radius: 10px !important;
            padding: 14px 24px !important;
            letter-spacing: 0.3px !important;
            transition: all 0.25s ease !important;
            width: 100% !important;
        }

        .car-detail-page--style-1 .btn-book:hover,
        .car-detail-page--style-1 .btn-primary:hover {
            background-color: #8E2B21 !important;
            box-shadow: 0 8px 24px rgba(176, 58, 46, 0.25) !important;
            transform: translateY(-2px) !important;
        }

        /* ── Gallery Styling ── */
        .car-detail-modern__gallery-main .wrapper-image img {
            border-radius: 20px !important;
        }

        .car-detail-modern__gallery-thumbs .banner-slide img {
            border-radius: 10px !important;
            border: 2px solid transparent;
            transition: border-color 0.2s;
        }

        .car-detail-modern__gallery-thumbs .slick-current img {
            border-color: #B03A2E !important;
        }

        /* ── Sidebar Sticky ── */
        .car-detail-modern__sidebar-stack--sticky {
            position: sticky;
            top: 96px;
        }

        /* ── Dark Mode ── */
        [data-bs-theme="dark"] .car-detail-page--style-1,
        [data-bs-theme="dark"] .car-detail-page--style-1 .background-body {
            background-color: #151515 !important;
        }

        [data-bs-theme="dark"] .car-detail-modern__header,
        [data-bs-theme="dark"] .car-detail-modern__layout .col-lg-8 > div,
        [data-bs-theme="dark"] .car-detail-modern__sidebar .car-detail-modern__sidebar-stack > div {
            background-color: #1e1e1e !important;
            border-color: #2e2e2e !important;
        }

        [data-bs-theme="dark"] .car-detail-modern__spec-pill,
        [data-bs-theme="dark"] .item-feature-car-inner {
            background-color: #252525 !important;
            border-color: #333 !important;
        }

        [data-bs-theme="dark"] .car-detail-modern__amenity-item {
            background-color: #252525 !important;
            border-color: #333 !important;
            color: #ccc !important;
        }

        /* ── Question & Answers (FAQ) ── */
        /* Real class from faqs.blade.php: .car-detail-modern__faq-item */
        .car-detail-modern__faq-item {
            background-color: #F8F9FA !important;
            border: 1px solid #E9ECEF !important;
            border-radius: 16px !important;
            padding: 20px 24px !important;
            margin-bottom: 16px !important;
            position: relative;
            padding-left: 56px !important; /* Space for icon */
        }

        .car-detail-modern__faq-item::before {
            content: '?' !important;
            position: absolute !important;
            left: 18px !important;
            top: 20px !important;
            width: 26px !important;
            height: 26px !important;
            background-color: #B03A2E !important;
            color: #ffffff !important;
            border-radius: 50% !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 0.85rem !important;
            font-weight: 700 !important;
        }

        .car-detail-modern__faq-item .head-question p {
            color: #111111 !important;
            font-size: 0.95rem !important;
            font-weight: 700 !important;
            margin-bottom: 8px !important;
        }

        .car-detail-modern__faq-item .content-question {
            color: #555 !important;
            font-size: 0.875rem !important;
            line-height: 1.6 !important;
        }

        [data-bs-theme="dark"] .car-detail-modern__faq-item {
            background-color: #252525 !important;
            border-color: #333 !important;
        }

        [data-bs-theme="dark"] .car-detail-modern__faq-item .head-question p {
            color: #f1f1f1 !important;
        }

        [data-bs-theme="dark"] .car-detail-modern__faq-item .content-question {
            color: #aaa !important;
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
