<div class="card-facilities {{ $cssClass ?? '' }}">
    <p class="card-miles text-md-medium">
        {!! BaseHelper::renderIcon($car->mileage_icon, attributes: ['class' => 'icon-mileage']) !!}
        {{ $car->mileage_display }}
    </p>

    @if ($car->transmission)
        <p class="card-gear text-md-medium">
            {!! BaseHelper::renderIcon($car->transmission_icon, attributes: ['class' => 'icon-transmission']) !!}
            {{ $car->transmission->name }}
        </p>
    @endif

    @if ($car->fuel)
        <p class="card-fuel text-md-medium">
            {!! BaseHelper::renderIcon($car->fuel_icon, attributes: ['class' => 'icon-fuel']) !!}
            {{ $car->fuel->name }}
        </p>
    @endif

    @if ($car->number_of_seats)
        <p class="card-seat text-md-medium">
            {!! BaseHelper::renderIcon($car->seats_icon, attributes: ['class' => 'icon-seats']) !!}
            {{ __(':number seats', ['number' => $car->number_of_seats]) }}
        </p>
    @endif
</div>
