@php
    $col = BaseHelper::stringify(request()->query('col'));

    if (empty($col)) {
        $col = (int) ($layoutCol ?? 4);
    }

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

<div class="col-lg-{{ $layoutCol }} col-md-6">
    <div class="card-journey-small background-card hover-up">
        <div class="card-image car-image">
            <a href="{{ $carUrl }}">
                {{ RvMedia::image($car->image , $car->name, 'medium-rectangle') }}
            </a>
        </div>
        <div class="card-info p-4 pt-30">
            <div class="card-rating">
                <div class="card-left"></div>
                <div class="card-right">
                    @if($avgReview = $car->avg_review)
                        <span class="rating text-xs-medium rounded-pill">
                           <x-core::icon name="ti ti-star" size="16" class="icon icon-tabler icons-tabler-filled icon-tabler-star" />

                            <span>
                                {{ $avgReview }}
                            </span>
                            @if($reviewsCount = $car->reviews_count ?? 0)
                                <span class="text-xs-medium neutral-500">
                                    ({{ $reviewsCount }} {{ $reviewsCount > 1 ? __('reviews') : __('review') }})
                                </span>
                            @endif
                        </span>
                    @endif
                </div>
            </div>
            <div class="card-title">
                <a class="text-lg-bold neutral-1000 text-ellipsis-2-lines" title="{{ $car->name }}" href="{{ $carUrl }}">
                    {{ $car->name }}
                </a>
            </div>
            <div class="card-program">
                @if($car->current_location)
                    <div class="card-location">
                        <p class="text-location text-sm-medium neutral-500 text-location text-sm-medium neutral-500 text-truncate" title="{{ BaseHelper::clean($car->current_location) }}">
                            <x-core::icon name="ti ti-map-pin" />
                            {{ BaseHelper::clean($car->current_location) }}
                        </p>
                    </div>
                @endif

                @include(Theme::getThemeNamespace('views.car-rentals.car-facilities'), ['car' => $car])

                <div class="endtime">
                    @include(Theme::getThemeNamespace('views.car-rentals.price'), ['car' => $car])
                    @include(Theme::getThemeNamespace('views.car-rentals.book-now-button'), ['car' => $car])
                </div>
            </div>
        </div>
    </div>
</div>
