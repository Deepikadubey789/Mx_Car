<div class="box-feature-car">
    <div class="list-feature-car">
        <div class="item-feature-car w-md-25">
            <div class="item-feature-car-inner">
                <div class="feature-image">
                    {!! BaseHelper::renderIcon($car->mileage_icon, attributes: ['class' => 'icon-mileage']) !!}
                </div>
                <div class="feature-info">
                    <p class="text-md-medium neutral-1000">{{ $car->mileage_display }}</p>
                </div>
            </div>
        </div>

        @if($horsepower = $car->horsepower)
            <div class="item-feature-car w-md-25">
                <div class="item-feature-car-inner">
                    <div class="feature-image">
                        <x-core::icon name="ti ti-engine" class="icon-horsepower" />
                    </div>
                    <div class="feature-info">
                        <p class="text-md-medium neutral-1000">{{ __(':number HP', ['number' => $horsepower]) }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($fuel = $car->fuel)
            <div class="item-feature-car w-md-25">
                <div class="item-feature-car-inner">
                    <div class="feature-image">
                        {!! BaseHelper::renderIcon($car->fuel_icon, attributes: ['class' => 'icon-fuel']) !!}
                    </div>

                    <div class="feature-info">
                        <p class="text-md-medium neutral-1000">{!! BaseHelper::clean($fuel->name) !!}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($transmission = $car->transmission)
            <div class="item-feature-car w-md-25">
                <div class="item-feature-car-inner">
                    <div class="feature-image">
                        {!! BaseHelper::renderIcon($car->transmission_icon, attributes: ['class' => 'icon-transmission']) !!}
                    </div>

                    <div class="feature-info">
                        <p class="text-md-medium neutral-1000">{!! BaseHelper::clean($transmission->name) !!}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($numberSeats = $car->number_of_seats)
            <div class="item-feature-car w-md-25">
                <div class="item-feature-car-inner">
                    <div class="feature-image">
                        {!! BaseHelper::renderIcon($car->seats_icon, attributes: ['class' => 'icon-seats']) !!}
                    </div>
                    <div class="feature-info">
                        <p class="text-md-medium neutral-1000">{{ __(':number Seats', ['number' => $numberSeats]) }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($type = $car->type)
            <div class="item-feature-car w-md-25">
                <div class="item-feature-car-inner">
                    @if($iconType = $type->icon)
                        <div class="feature-image">
                            {!! BaseHelper::renderIcon($iconType) !!}
                        </div>
                    @endif

                    <div class="feature-info">
                        <p class="text-md-medium neutral-1000">{!! BaseHelper::clean($type->name) !!}</p>
                    </div>
                </div>
            </div>
        @endif

        @if ($numberDoors = $car->number_of_doors)
            <div class="item-feature-car w-md-25">
                <div class="item-feature-car-inner">
                    <div class="feature-image">
                        <x-core::icon name="ti ti-door" class="icon-doors" />
                    </div>

                    <div class="feature-info">
                        <p class="text-md-medium neutral-1000">{{ __(':number Doors', ['number' => $numberDoors]) }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
