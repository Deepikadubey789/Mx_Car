@extends(CarRentalsHelper::viewPath('customers.layouts.master'))

@section('content')
    <div class="customer-bookings-page">
        <header class="customer-bookings-header">
            <div>
                <h2 class="customer-bookings-title">{{ __('Bookings') }}</h2>
                <p class="customer-bookings-subtitle text-muted mb-0">
                    {{ __('Pick up where you left off — view trips and rental details anytime.') }}
                </p>
            </div>
            @if (count($bookings) > 0)
                <span class="customer-bookings-count badge rounded-pill" title="{{ __('Total bookings') }}">
                    {{ $bookings->total() }}
                </span>
            @endif
        </header>

        @if (count($bookings) > 0)
            <div class="customer-bookings-grid">
                @foreach ($bookings as $booking)
                    @php
                        $bc = $booking->car;
                        if ($bc->car->exists && ($liveCar = $bc->car)) {
                            $cardImage = RvMedia::getImageUrl($liveCar->image, 'medium', false, RvMedia::getDefaultImage());
                            $cardTitle = $liveCar->name;
                            $cardUrl = $liveCar->url;
                        } else {
                            $cardImage = RvMedia::getImageUrl($bc->car_image, 'medium', false, RvMedia::getDefaultImage());
                            $cardTitle = $bc->name;
                            $cardUrl = null;
                        }
                    @endphp
                    <article class="customer-booking-card h-100">
                            <div class="customer-booking-card__media">
                                @if ($cardUrl)
                                    <a href="{{ $cardUrl }}" target="_blank" rel="noopener noreferrer" class="customer-booking-card__media-link">
                                        <img
                                            src="{{ $cardImage }}"
                                            alt="{{ $cardTitle }}"
                                            class="customer-booking-card__img"
                                            loading="lazy"
                                        >
                                    </a>
                                @else
                                    <img
                                        src="{{ $cardImage }}"
                                        alt="{{ $cardTitle }}"
                                        class="customer-booking-card__img"
                                        loading="lazy"
                                    >
                                @endif
                                <span class="customer-booking-card__status badge bg-{{ $booking->status->getColor() }}">
                                    {{ $booking->status->label() }}
                                </span>
                            </div>
                            <div class="customer-booking-card__body p-2">
                                <div class="customer-booking-card__meta text-muted small">
                                    <span class="customer-booking-card__booked">
                                        <x-core::icon name="ti ti-calendar" class="customer-booking-card__icon" />
                                        {{ __('Booked') }} {{ $booking->created_at->format('M j, Y') }}
                                    </span>
                                    @if ($booking->booking_number)
                                        <span class="customer-booking-card__ref" title="{{ __('Booking number') }}">#{{ $booking->booking_number }}</span>
                                    @endif
                                </div>
                                @if ($cardUrl)
                                    <h3 class="customer-booking-card__title">
                                        <a href="{{ $cardUrl }}" target="_blank" rel="noopener noreferrer">{{ $cardTitle }}</a>
                                    </h3>
                                @else
                                    <h3 class="customer-booking-card__title">{{ $cardTitle }}</h3>
                                @endif
                                <p class="customer-booking-card__price">{{ format_price($bc->price) }}</p>
                                <div class="customer-booking-card__period">
                                    <span class="text-muted small text-uppercase customer-booking-card__period-label">{{ __('Rental period') }}</span>
                                    <div class="customer-booking-card__dates">
                                        <time @if ($bc->rental_start_date) datetime="{{ $bc->rental_start_date->toIso8601String() }}" @endif>{{ $bc->rental_start_date_formatted }}</time>
                                        <span class="customer-booking-card__dates-sep" aria-hidden="true">
                                            <x-core::icon name="ti ti-arrow-right" />
                                        </span>
                                        <time @if ($bc->rental_end_date) datetime="{{ $bc->rental_end_date->toIso8601String() }}" @endif>{{ $bc->rental_end_date_formatted }}</time>
                                    </div>
                                </div>
                            </div>
                            <div class="customer-booking-card__footer">
                                <a class="btn btn-primary customer-booking-card__btn w-100" href="{{ route('customer.bookings.show', $booking->transaction_id) }}">
                                    <x-core::icon name="ti ti-eye" class="me-2" />
                                    {{ __('View Details') }}
                                </a>
                            </div>
                    </article>
                @endforeach
            </div>

            <div class="customer-bookings-pagination mt-4">
                {!! $bookings->withQueryString()->links(CarRentalsHelper::viewPath('partials.pagination')) !!}
            </div>
        @else
            <div class="customer-bookings-empty bb-empty-state">
                <div class="customer-bookings-empty__icon" aria-hidden="true">
                    <x-core::icon name="ti ti-calendar-off" />
                </div>
                <h4>{{ __('No Bookings Yet') }}</h4>
                <p>{{ __("You haven't made any bookings yet. Start exploring our cars and book your first ride!") }}</p>
                <a href="{{ route('public.cars') }}" class="btn btn-primary">
                    <x-core::icon name="ti ti-car" class="me-1" />
                    {{ __('Explore Cars') }}
                </a>
            </div>
        @endif
    </div>
@endsection
