@if (CarRentalsHelper::isRentalBookingEnabled() && ! $car->is_for_sale)
    <div class="card-button">
        <a class="btn btn-gray rounded-pill" href="{{ $car->url }}">{{ __('Book Now') }}</a>
    </div>
@endif