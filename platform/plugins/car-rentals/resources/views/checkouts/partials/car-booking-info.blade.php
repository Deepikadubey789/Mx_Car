{{--
    Car Booking Information Component

    Props:
    - $carImage: URL to car image
    - $carName: Car name string
    - $carUrl: (optional) URL to car detail page
    - $startDate: Carbon instance for rental start date/time
    - $endDate: Carbon instance for rental end date/time
    - $rentalRate: (optional) Rental rate display (e.g., "$68/ Per day")
    - $pickupAddress: (optional) Pickup address text
    - $returnAddress: (optional) Return address text
    - $imageColSize: (optional) Bootstrap column size for image, default '3'
--}}

@php
    $imageColSize = $imageColSize ?? '3';
@endphp

<div class="row cart-item">
    <div class="col-lg-{{ $imageColSize }} col-md-{{ $imageColSize }}">
        <div class="checkout-product-img-wrapper d-inline-block">
            <img
                class="item-thumb img-thumbnail img-rounded mb-2 mb-md-0"
                src="{{ $carImage }}"
                alt="{{ $carName }}"
            >
        </div>
    </div>
    <div class="col">
        <p class="mb-2 fw-medium">
            @if (isset($carUrl) && $carUrl)
                <a href="{{ $carUrl }}" target="_blank">{{ $carName }}</a>
            @else
                {{ $carName }}
            @endif
        </p>

        @include('plugins/car-rentals::checkouts.partials.rental-period-info', [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'minWidth' => '70px'
        ])

        @if (isset($rentalRate) && $rentalRate)
            <p class="mb-0 mt-2">
                <span class="text-muted">{{ __('Rate:') }}</span> <span>{{ $rentalRate }}</span>
            </p>
        @endif

        @if (isset($pickupAddress) && $pickupAddress)
            <p class="mb-0">
                <span class="text-muted">{{ __('Pickup Address:') }}</span> <span>{{ $pickupAddress }}</span>
            </p>
        @endif

        @if (isset($returnAddress) && $returnAddress)
            <p class="mb-0">
                <span class="text-muted">{{ __('Return Address:') }}</span> <span>{{ $returnAddress }}</span>
            </p>
        @endif
    </div>
</div>
