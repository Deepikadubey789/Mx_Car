<div class="pt-3 mb-5 order-item-info">
    <div class="align-items-center">
        <h6 class="d-inline-block">{{ __('Booking ID') }}: {{ $booking->booking_number }}</h6>
    </div>

    <div class="checkout-success-products">
        <div id="cart-item-{{ $booking->id }}">
                @php
                    $bookingCar = $booking->car;
                    $services = $booking->services;
                @endphp

                @if($bookingCar)
                    @php
                        $carUrl = null;
                        $rentalRate = null;
                        if ($bookingCar->car->exists && ($car = $bookingCar->car)) {
                            $carUrl = $car->url;
                            if ($car->rental_rate && $car->rental_type) {
                                $rentalRate = format_price($car->rental_rate, $car->currency_id) . '/ ' . $car->rental_type->label();
                            }
                        }
                    @endphp
                    @include('plugins/car-rentals::checkouts.partials.car-booking-info', [
                        'carImage' => RvMedia::getImageUrl($bookingCar->car_image, 'thumb', false, RvMedia::getDefaultImage()),
                        'carName' => $bookingCar->car_name,
                        'carUrl' => $carUrl,
                        'startDate' => $bookingCar->rental_start_date,
                        'endDate' => $bookingCar->rental_end_date,
                        'rentalRate' => $rentalRate,
                        'imageColSize' => '3'
                    ])
                @endif

                @if(isset($services) && $services->isNotEmpty())
                    <h6 class="mb-2 mt-4">{{ __('Services') }}</h6>

                    @foreach($services as $service)
                        <div class="row cart-item">
                            <div class="col">
                                <p class="mb-0">
                                    {{ $service->name }}
                                </p>
                            </div>
                            <div class="col-auto text-end">
                                <p class="mb-0">{{ format_price($service->price) }}</p>
                            </div>
                        </div>
                    @endforeach
                @endif

            @if (!empty($isShowTotalInfo))
                @include('plugins/car-rentals::checkouts.partials.total-info', compact('booking'))
            @endif
        </div>
    </div>
</div>
