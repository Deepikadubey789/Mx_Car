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
<div class="col-xl-12 col-lg-12">
    <div class="card-flight card-hotel card-property background-card border">
        <div class="card-image">
            <a href="{{ $carUrl }}">
                {{ RvMedia::image($car->image , $car->name, 'medium-rectangle') }}
            </a></div>
        <div class="card-info p-md-40 p-3">
            @if($avgReview = $car->avg_review)
            <div class="tour-rate">
                <div class="rate-element">
                    <span class="rating">
                       <x-core::icon name="ti ti-star" size="16" class="icon icon-tabler icons-tabler-filled icon-tabler-star" />

                        <span>
                            {{ $avgReview }}
                        </span>
                            @if($reviewsCount = $car->reviews_count ?? 0)
                                <span class="text-sm-medium neutral-500">
                                    ({{ $reviewsCount }} {{ $reviewsCount > 1 ? __('reviews') : __('review') }})
                                </span>
                            @endif
                    </span>
                </div>
            </div>
            @endif
            <div class="card-title">
                <a class="heading-6 neutral-1000 text-ellipsis-2-lines" href="{{ $carUrl }}">
                    {{ $car->name }}
                </a>
            </div>
            <div class="card-program">
                <div class="card-location mb-25">
                    @if($car->current_location)
                        <p class="text-location text-md-medium neutral-500 text-truncate" title="{{ $car->current_location }}">
                            <x-core::icon name="ti ti-map-pin" />
                            {{ BaseHelper::clean($car->current_location) }}
                        </p>
                    @endif
                </div>
                <div class="card-facilities">
                    <div class="item-facilities">
                        <p class="room text-md-medium neutral-1000">{{ $car->mileage_display }}</p>
                    </div>

                    @if($transmission && $transmission->name)
                        <div class="item-facilities">
                            <p class="size text-md-medium neutral-1000">{{ $transmission->name }}</p>
                        </div>
                    @endif
                    @if($types && $types->name)
                        <div class="item-facilities">
                            <p class="parking text-md-medium neutral-1000">{{ $types->name }}</p>
                        </div>
                    @endif
                    @if($numberOfSeat = $car->number_of_seats)
                        <div class="item-facilities">
                            <p class="bathroom text-md-medium neutral-1000">{{ $numberOfSeat }} {{ $numberOfSeat == 1 ? __('seat') : __('seats') }}</p>
                        </div>
                    @endif
                    @if($make && $make->name)
                        <div class="item-facilities">
                            <p class="pet text-md-medium neutral-1000">{{ $make->name }}</p>
                        </div>
                    @endif
                </div>
                <div class="endtime">
                    @include(Theme::getThemeNamespace('views.car-rentals.price'), ['car' => $car])
                    @include(Theme::getThemeNamespace('views.car-rentals.book-now-button'), ['car' => $car])
                </div>
            </div>
        </div>
    </div>
</div>
