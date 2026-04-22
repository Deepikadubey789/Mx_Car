@php
    $transmission = $car->transmission;
    $types = $car->types;
    $make = $car->make;
    $carUrl = $car->url;

    $query = [];

    // Only include rental date params if rental mode is enabled and car is not for sale
    if (CarRentalsHelper::isRentalBookingEnabled() && ! $car->is_for_sale) {
        if ($startDate = BaseHelper::stringify(request()->query('start_date'))) {
            $query['rental_start_date'] = $startDate;
        }

        if ($endDate = BaseHelper::stringify(request()->query('end_date'))) {
            $query['rental_end_date'] = $endDate;
        }
    }

    if ($query) {
        $carUrl = $car->url . '?' . http_build_query($query);
    }
@endphp

<style>
    /* Modern List Card Styling */
    .modern-list-card {
        display: flex;
        flex-direction: row;
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .modern-list-card:hover {
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        transform: translateY(-3px);
        border-color: var(--primary-color, #df4827);
    }

    .modern-list-image-wrapper {
        width: 340px;
        flex-shrink: 0;
        position: relative;
        overflow: hidden;
    }
    .modern-list-image-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .modern-list-card:hover .modern-list-image-wrapper img {
        transform: scale(1.05);
    }

    .modern-list-content {
        flex-grow: 1;
        padding: 25px 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .modern-list-specs {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }
    .modern-spec-pill {
        background: #f8f9fa;
        border: 1px solid #f3f4f6;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.85rem;
        color: #4b5563;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .modern-list-action {
        width: 240px;
        flex-shrink: 0;
        background: #f8f9fa;
        border-left: 1px solid #e5e7eb;
        padding: 30px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
    }

    /* Dark Mode Adjustments */
    [data-bs-theme="dark"] .modern-list-card {
        background: #1e293b;
        border-color: rgba(255,255,255,0.05);
    }
    [data-bs-theme="dark"] .modern-spec-pill,
    [data-bs-theme="dark"] .modern-list-action {
        background: #0f172a;
        border-color: rgba(255,255,255,0.05);
        color: #cbd5e1;
    }
    [data-bs-theme="dark"] .modern-list-card:hover {
        border-color: var(--primary-color, #df4827);
    }

    /* Mobile Responsiveness */
    @media (max-width: 991px) {
        .modern-list-card {
            flex-direction: column;
        }
        .modern-list-image-wrapper {
            width: 100%;
            height: 250px;
        }
        .modern-list-action {
            width: 100%;
            border-left: none;
            border-top: 1px solid #e5e7eb;
            flex-direction: row;
            justify-content: space-between;
            padding: 20px 25px;
        }
    }
</style>

<div class="col-xl-12 col-lg-12 mb-4">
    <article class="modern-list-card">
        <div class="modern-list-image-wrapper">
            <a href="{{ $carUrl }}" class="d-block w-100 h-100">
                {{ RvMedia::image($car->image , $car->name, 'medium-rectangle') }}
            </a>
            
            @if($avgReview = $car->avg_review)
                <div style="position: absolute; top: 15px; left: 15px; z-index: 2;">
                    <span class="rating text-xs-medium rounded-pill bg-white text-dark shadow-sm px-2 py-1 d-inline-flex align-items-center gap-1 border">
                        <x-core::icon name="ti ti-star-filled" size="14" class="text-warning" />
                        <span class="fw-bold">{{ $avgReview }}</span>
                        @if($reviewsCount = $car->reviews_count ?? 0)
                            <span class="text-muted small">({{ $reviewsCount }})</span>
                        @endif
                    </span>
                </div>
            @endif
        </div>

        <div class="modern-list-content">
            <div>
                <span class="badge bg-danger-subtle text-danger rounded-pill mb-3 px-3 py-1">{{ __('Premium choice') }}</span>
                
                <a class="d-block fw-bold mb-2 text-decoration-none" href="{{ $carUrl }}" style="font-size: 1.35rem; color: var(--bs-heading-color, #111827);">
                    {{ $car->name }}
                </a>

                @if($car->current_location)
                    <p class="text-sm-medium neutral-500 mb-0 d-flex align-items-center gap-2">
                        <x-core::icon name="ti ti-map-pin" size="18" class="text-danger" />
                        {{ BaseHelper::clean($car->current_location) }}
                    </p>
                @endif
            </div>

            <div class="modern-list-specs">
                <div class="modern-spec-pill">
                    <x-core::icon name="ti ti-dashboard" size="16" /> {{ $car->mileage_display }}
                </div>
                @if($transmission && $transmission->name)
                    <div class="modern-spec-pill">
                        <x-core::icon name="ti ti-manual-gearbox" size="16" /> {{ $transmission->name }}
                    </div>
                @endif
                @if($types && $types->name)
                    <div class="modern-spec-pill">
                        <x-core::icon name="ti ti-car" size="16" /> {{ $types->name }}
                    </div>
                @endif
                @if($numberOfSeat = $car->number_of_seats)
                    <div class="modern-spec-pill">
                        <x-core::icon name="ti ti-users" size="16" /> {{ $numberOfSeat }} {{ $numberOfSeat == 1 ? __('seat') : __('seats') }}
                    </div>
                @endif
                @if($make && $make->name)
                    <div class="modern-spec-pill">
                        <x-core::icon name="ti ti-steering-wheel" size="16" /> {{ $make->name }}
                    </div>
                @endif
            </div>
        </div>

        <div class="modern-list-action">
            <div class="mb-3 text-start w-100 d-none d-lg-block">
                <p class="text-sm-medium neutral-500 mb-1 text-center" style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;">{{ __('Starting from') }}</p>
                <div class="text-center">
                    @include(Theme::getThemeNamespace('views.car-rentals.price'), ['car' => $car])
                </div>
            </div>
            
            <div class="d-lg-none d-flex flex-column text-start">
                <p class="text-sm-medium neutral-500 mb-0" style="font-size: 0.8rem;">{{ __('Starting from') }}</p>
                @include(Theme::getThemeNamespace('views.car-rentals.price'), ['car' => $car])
            </div>

            <div class="w-100 mt-lg-2">
                @include(Theme::getThemeNamespace('views.car-rentals.book-now-button'), ['car' => $car])
            </div>
        </div>
    </article>
</div>