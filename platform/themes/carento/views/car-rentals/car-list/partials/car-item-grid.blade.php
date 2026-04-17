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
    <article class="card-journey-small card-journey-small--modern car-card-grid background-card hover-up h-100">
        <div class="card-image car-image car-card-grid__media">
            <a href="{{ $carUrl }}" class="car-card-grid__image-link">
                {{ RvMedia::image($car->image , $car->name, 'medium-rectangle') }}
            </a>



            <div class="car-card-grid__media-shade"></div>

            @if($avgReview = $car->avg_review)
                <div class="car-card-grid__rating">
                    <span class="rating text-xs-medium rounded-pill">
                        <x-core::icon name="ti ti-star" size="16" class="icon icon-tabler icons-tabler-filled icon-tabler-star" />
                        <span>{{ $avgReview }}</span>
                        @if($reviewsCount = $car->reviews_count ?? 0)
                            <span class="text-xs-medium neutral-500">
                                ({{ $reviewsCount }} {{ $reviewsCount > 1 ? __('reviews') : __('review') }})
                            </span>
                        @endif
                    </span>
                </div>
            @endif
        </div>

        <div class="card-info p-4 pt-30">
            <div class="car-card-grid__meta-strip">
                
            @php
                    $qs = \Botble\CarRentals\Models\VendorQualityScore::where('vendor_id', $car->author_id)->first();
                    $effectiveBadge = ($qs?->badge_override) ? $qs?->override_badge : $qs?->badge_tier;
                    $badgeCfg = match($effectiveBadge ?? 'none') {
                        'all_star'    => ['label' => '⭐ All-Star Host', 'bg' => '#f59e0b'],
                        'top_host'    => ['label' => '🏆 Top Host',      'bg' => '#3b82f6'],
                        'rising_star' => ['label' => '✦ Rising Star',   'bg' => '#10b981'],
                        default       => null,
                    };
                @endphp
                @if($badgeCfg)
                    <span style="background:{{ $badgeCfg['bg'] }};color:#fff;font-size:10px;font-weight:700;padding:2px 8px;border-radius:20px;letter-spacing:0.3px;">
                        {{ $badgeCfg['label'] }}
                    </span>
                @endif
                <span class="car-card-grid__chip">{{ __('Top pick') }}</span>
                @if($car->number_of_seats)
                    <span class="car-card-grid__chip car-card-grid__chip--soft">
                        {{ __(':number seats', ['number' => $car->number_of_seats]) }}
                    </span>
                @endif
            </div>

            <div class="card-title">
                <a class="text-lg-bold neutral-1000 text-ellipsis-2-lines" title="{{ $car->name }}" href="{{ $carUrl }}">
                    {{ $car->name }}
                </a>
            </div>

            <div class="card-program car-card-grid__program">
                @if($car->current_location)
                    <div class="card-location">
                        <p class="text-location text-sm-medium neutral-500 text-truncate" title="{{ BaseHelper::clean($car->current_location) }}">
                            <x-core::icon name="ti ti-map-pin" />
                            {{ BaseHelper::clean($car->current_location) }}
                        </p>
                    </div>
                @endif

                <div class="car-card-grid__specs">
                    @include(Theme::getThemeNamespace('views.car-rentals.car-facilities'), ['car' => $car])
                </div>

                <div class="endtime car-card-grid__cta">
                    @include(Theme::getThemeNamespace('views.car-rentals.price'), ['car' => $car])
                    @include(Theme::getThemeNamespace('views.car-rentals.book-now-button'), ['car' => $car])
                </div>
            </div>
        </div>
    </article>
</div>
