@php
    $formId = $formId ?? 'cars-filter-form';
@endphp

@if(CarRentalsHelper::isEnabledFilterCarsBy('locations') && is_plugin_active('location'))
    @php
        $selectedLocation = BaseHelper::stringify(request()->input('location', ''));
        $selectedCityId = BaseHelper::stringify(request()->input('city_id', ''));
    @endphp
    <div class="filter-widget mb-4" style="overflow: unset !important;">
        <div class="filter-widget-header">
            <div class="filter-icon">
                <x-core::icon name="ti ti-map-pin" />
            </div>
            <h6 class="filter-title">{{ __('Location') }}</h6>
        </div>
        <div class="filter-widget-content" data-bb-toggle="search-suggestion">
            <div class="position-relative">
                <input
                    type="text"
                    class="form-control location-autocomplete submit-form-filter"
                    id="location-filter-input"
                    placeholder="{{ __('Search for location...') }}"
                    value="{{ $selectedLocation }}"
                    name="location"
                    form="{{ $formId }}"
                    data-url="{{ route('public.ajax.cities') }}"
                    autocomplete="off"
                    style="padding-left: 35px !important;"
                />
                <span class="position-absolute top-50 start-0 translate-middle-y" style="z-index: 10;">
                    <img src="{{ Theme::asset()->url('images/icons/location.svg') }}" alt="Location" width="20" height="20" />
                </span>
                <input type="hidden" name="city_id" id="city_id_filter_hidden" form="{{ $formId }}" value="{{ $selectedCityId }}">
                <div class="location-suggestions" data-bb-toggle="data-suggestion"></div>
            </div>
        </div>
    </div>
@endif

{{-- ADDED: Keyword / Car Name Search Filter --}}
<div class="filter-widget mb-4">
    <div class="filter-widget-header">
        <div class="filter-icon">
            <x-core::icon name="ti ti-search" />
        </div>
        <h6 class="filter-title">{{ __('What') }}</h6>
    </div>
    <div class="filter-widget-content">
        <div class="input-group">
            <input 
                type="text" 
                name="keyword" 
                class="form-control" 
                placeholder="{{ __('Car name or brand') }}" 
                value="{{ BaseHelper::clean(request()->input('keyword')) }}"
                form="{{ $formId }}"
            >
            <button class="btn btn-primary" type="submit" form="{{ $formId }}" style="border-radius: 0 6px 6px 0;">
                <x-core::icon name="ti ti-search" />
            </button>
        </div>
    </div>
</div>

{{-- Vehicle Condition Filter --}}
@if(CarRentalsHelper::isEnabledFilterCarsBy('vehicle_condition'))
    <div class="filter-widget mb-4">
        <div class="filter-widget-header">
            <div class="filter-icon">
                <x-core::icon name="ti ti-car" />
            </div>
            <h6 class="filter-title">{{ __('Vehicle Condition') }}</h6>
        </div>
        <div class="filter-widget-content">
            <select
                name="adv_type"
                form="{{ $formId }}"
                class="form-select submit-form-filter"
            >
                @php
                    $advType = request()->input('adv_type', 'all');
                    $advType = is_string($advType) ? $advType : 'all';
                @endphp
                @foreach($advancedTypes as $type => $label)
                    <option @selected($advType === $type) value="{{ $type }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
@endif

{{-- Rental Type Filter (only show when rental mode is enabled) --}}
@if(!empty($rentalTypes) && CarRentalsHelper::isEnabledFilterCarsBy('rental_types') && CarRentalsHelper::isRentalBookingEnabled())
    <div class="filter-widget mb-4">
        <div class="filter-widget-header">
            <div class="filter-icon">
                <x-core::icon name="ti ti-clock" />
            </div>
            <h6 class="filter-title">{{ __('Rental Period') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="filter-options-list">
                @foreach($rentalTypes as $type => $label)
                    <div class="filter-option">
                        <div class="custom-filter-check">
                            <input
                                type="checkbox"
                                class="form-check-input submit-form-filter d-none"
                                value="{{ $type }}"
                                name="rental_types[]"
                                id="check-rental-type-{{ $type }}"
                                form="{{ $formId }}"
                                @checked(in_array($type, (array) request()->input('rental_types', [])))
                            >
                            <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center w-100 p-2 rounded border cursor-pointer" for="check-rental-type-{{ $type }}">
                                <span class="filter-option-text">{{ $label }}</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Price Filter --}}
@if($carMaxRentalRate && CarRentalsHelper::isEnabledFilterCarsBy('prices'))
    <div class="filter-widget mb-4">
        <div class="filter-widget-header">
            <div class="filter-icon">
                <x-core::icon name="ti ti-currency-dollar" />
            </div>
            <h6 class="filter-title">{{ __('Price Range') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="price-slider-wrapper">
                <div id="slider-range"
                     data-current-range="{{ request()->query('rental_rate_to') > 0 ? BaseHelper::stringify(request()->query('rental_rate_to')) : 0 }}"
                     data-max-rental-rate-range="{{ $carMaxRentalRate }}"
                     data-currency="{{ get_application_currency()?->title }}"
                     data-currency-rate="{{ get_application_currency()?->exchange_rate }}"
                >
                </div>
                <div class="price-range-display d-flex justify-content-between align-items-center mt-3">
                    <div class="price-min">
                        <small class="text-muted">{{ __('Min') }}</small>
                        <div class="fw-semibold rental-rate-from">{{ format_price(0) }}</div>
                    </div>
                    <div class="price-separator">
                        <x-core::icon name="ti ti-minus" class="text-muted" />
                    </div>
                    <div class="price-max">
                        <small class="text-muted">{{ __('Max') }}</small>
                        <div class="fw-semibold rental-rate-to">{{ format_price($carMaxRentalRate) }}</div>
                    </div>
                </div>
                <input class="input-disabled form-control submit-form-filter value-money"
                       name="rental_rate_from" type="hidden"
                       form="{{ $formId }}"
                       value="{{ request()->query('rental_rate_from') > 0 ? BaseHelper::stringify(request()->query('rental_rate_from')) : 0 }}">
                <input class="input-disabled form-control submit-form-filter value-money"
                       name="rental_rate_to" type="hidden"
                       form="{{ $formId }}"
                       value="{{ BaseHelper::stringify(request()->query('rental_rate_to', $carMaxRentalRate)) }}"
                       data-default-value="{{ $carMaxRentalRate }}">
            </div>
        </div>
    </div>
@endif

{{-- Horsepower Filter --}}
@if($carMaxHorsepower && CarRentalsHelper::isEnabledFilterCarsBy('horsepower'))
    <div class="filter-widget mb-4">
        <div class="filter-widget-header">
            <div class="filter-icon">
                <x-core::icon name="ti ti-engine" />
            </div>
            <h6 class="filter-title">{{ __('Horsepower') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="horsepower-slider-wrapper">
                <div id="horsepower-slider-range"
                     data-current-range="{{ request()->query('horsepower_to') > 0 ? BaseHelper::stringify(request()->query('horsepower_to')) : 0 }}"
                     data-max-horsepower-range="{{ $carMaxHorsepower }}"
                >
                </div>
                <div class="horsepower-range-display d-flex justify-content-between align-items-center mt-3">
                    <div class="hp-min">
                        <small class="text-muted">{{ __('Min') }}</small>
                        <div class="fw-semibold horsepower-from">0 HP</div>
                    </div>
                    <div class="hp-separator">
                        <x-core::icon name="ti ti-minus" class="text-muted" />
                    </div>
                    <div class="hp-max">
                        <small class="text-muted">{{ __('Max') }}</small>
                        <div class="fw-semibold horsepower-to">{{ $carMaxHorsepower }} HP</div>
                    </div>
                </div>
                <input class="input-disabled form-control submit-form-filter"
                       name="horsepower_from" type="hidden"
                       form="{{ $formId }}"
                       value="{{ request()->query('horsepower_from') > 0 ? BaseHelper::stringify(request()->query('horsepower_from')) : 0 }}">
                <input class="input-disabled form-control submit-form-filter"
                       name="horsepower_to" type="hidden"
                       form="{{ $formId }}"
                       value="{{ BaseHelper::stringify(request()->query('horsepower_to', $carMaxHorsepower)) }}"
                       data-default-value="{{ $carMaxHorsepower }}">
            </div>
        </div>
    </div>
@endif

{{-- Year Range Filter --}}
@if(isset($carMinYear) && isset($carMaxYear) && $carMaxYear > $carMinYear && CarRentalsHelper::isEnabledFilterCarsBy('year'))
    <div class="filter-widget mb-4">
        <div class="filter-widget-header">
            <div class="filter-icon">
                <x-core::icon name="ti ti-calendar" />
            </div>
            <h6 class="filter-title">{{ __('Year Range') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="year-slider-wrapper">
                <div id="year-slider-range"
                     data-current-range-from="{{ request()->query('year_from', $carMinYear) }}"
                     data-current-range-to="{{ request()->query('year_to', $carMaxYear) }}"
                     data-min-year="{{ $carMinYear }}"
                     data-max-year="{{ $carMaxYear }}"
                >
                </div>
                <div class="year-range-display d-flex justify-content-between align-items-center mt-3">
                    <div class="year-min">
                        <small class="text-muted">{{ __('From') }}</small>
                        <div class="fw-semibold year-from">{{ request()->query('year_from', $carMinYear) }}</div>
                    </div>
                    <div class="year-separator">
                        <x-core::icon name="ti ti-minus" class="text-muted" />
                    </div>
                    <div class="year-max">
                        <small class="text-muted">{{ __('To') }}</small>
                        <div class="fw-semibold year-to">{{ request()->query('year_to', $carMaxYear) }}</div>
                    </div>
                </div>
                <input class="input-disabled form-control submit-form-filter"
                       name="year_from" type="hidden"
                       form="{{ $formId }}"
                       value="{{ request()->query('year_from', $carMinYear) }}">
                <input class="input-disabled form-control submit-form-filter"
                       name="year_to" type="hidden"
                       form="{{ $formId }}"
                       value="{{ request()->query('year_to', $carMaxYear) }}">
            </div>
        </div>
    </div>
@endif

{{-- Mileage Range Filter --}}
@if(isset($carMaxMileage) && $carMaxMileage > 0 && CarRentalsHelper::isEnabledFilterCarsBy('mileage'))
    <div class="filter-widget mb-4">
        <div class="filter-widget-header">
            <div class="filter-icon">
                <x-core::icon name="ti ti-route" />
            </div>
            <h6 class="filter-title">{{ __('Mileage Range') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="mileage-slider-wrapper">
                <div id="mileage-slider-range"
                     data-current-range="{{ request()->query('mileage_to') > 0 ? BaseHelper::stringify(request()->query('mileage_to')) : 0 }}"
                     data-max-mileage-range="{{ $carMaxMileage }}"
                >
                </div>
                <div class="mileage-range-display d-flex justify-content-between align-items-center mt-3">
                    <div class="mileage-min">
                        <small class="text-muted">{{ __('Min') }}</small>
                        <div class="fw-semibold mileage-from">0 {{ __('miles') }}</div>
                    </div>
                    <div class="mileage-separator">
                        <x-core::icon name="ti ti-minus" class="text-muted" />
                    </div>
                    <div class="mileage-max">
                        <small class="text-muted">{{ __('Max') }}</small>
                        <div class="fw-semibold mileage-to">{{ number_format($carMaxMileage) }} {{ __('miles') }}</div>
                    </div>
                </div>
                <input class="input-disabled form-control submit-form-filter"
                       name="mileage_from" type="hidden"
                       form="{{ $formId }}"
                       value="{{ request()->query('mileage_from') > 0 ? BaseHelper::stringify(request()->query('mileage_from')) : 0 }}">
                <input class="input-disabled form-control submit-form-filter"
                       name="mileage_to" type="hidden"
                       form="{{ $formId }}"
                       value="{{ BaseHelper::stringify(request()->query('mileage_to', $carMaxMileage)) }}"
                       data-default-value="{{ $carMaxMileage }}">
            </div>
        </div>
    </div>
@endif

{{-- Car Make/Brand Filter --}}
@if(isset($carMakes) && $carMakes->isNotEmpty() && CarRentalsHelper::isEnabledFilterCarsBy('makes'))
    <div class="filter-widget mb-4">
        <div class="filter-widget-header">
            <div class="filter-icon">
                <x-core::icon name="ti ti-brand-toyota" />
            </div>
            <h6 class="filter-title">{{ __('Brand') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="filter-options-list">
                @foreach($carMakes->take(5) as $carMake)
                    <div class="filter-option">
                        <div class="custom-filter-check">
                            <input
                                type="checkbox"
                                class="form-check-input submit-form-filter d-none"
                                value="{{ $carMake->id }}"
                                name="car_makes[]"
                                id="check-car-make-{{ $carMake->id }}"
                                form="{{ $formId }}"
                                @checked(in_array($carMake->id, (array) request()->input('car_makes', [])))
                            >
                            <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center w-100 p-2 rounded border cursor-pointer" for="check-car-make-{{ $carMake->id }}">
                                <span class="filter-option-text">{{ $carMake->name }}</span>
                                <span class="filter-option-count badge bg-light text-dark">{{ $carMake->cars_count ?: 0 }}</span>
                            </label>
                        </div>
                    </div>
                @endforeach

                @if($carMakes->count() > 5)
                    <div class="filter-options-extra" style="display: none;">
                        @foreach($carMakes->skip(5) as $carMake)
                            <div class="filter-option">
                                <div class="custom-filter-check">
                                    <input
                                        type="checkbox"
                                        class="form-check-input submit-form-filter d-none"
                                        value="{{ $carMake->id }}"
                                        name="car_makes[]"
                                        id="check-car-make-{{ $carMake->id }}"
                                        form="{{ $formId }}"
                                        @checked(in_array($carMake->id, (array) request()->input('car_makes', [])))
                                    >
                                    <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center w-100 p-2 rounded border cursor-pointer" for="check-car-make-{{ $carMake->id }}">
                                        <span class="filter-option-text">{{ $carMake->name }}</span>
                                        <span class="filter-option-count badge bg-light text-dark">{{ $carMake->cars_count ?: 0 }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-link btn-sm p-0 mt-2 filter-toggle-btn" data-target=".filter-options-extra">
                        <span class="show-more-text">
                            <x-core::icon name="ti ti-chevron-down" class="me-1" />
                            {{ __('Show more') }}
                        </span>
                        <span class="show-less-text d-none">
                            <x-core::icon name="ti ti-chevron-up" class="me-1" />
                            {{ __('Show less') }}
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>
@endif

{{-- Car Categories Filter --}}
@if($carCategories->isNotEmpty() && CarRentalsHelper::isEnabledFilterCarsBy('categories'))
    <div class="filter-widget mb-4">
        <div class="filter-widget-header">
            <div class="filter-icon">
                <x-core::icon name="ti ti-category" />
            </div>
            <h6 class="filter-title">{{ __('Categories') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="filter-options-list">
                @foreach($carCategories->take(5) as $carCategory)
                    <div class="filter-option">
                        <div class="custom-filter-check">
                            <input
                                type="checkbox"
                                class="form-check-input submit-form-filter d-none"
                                value="{{ $carCategory->id }}"
                                name="car_categories[]"
                                id="check-car-category-{{ $carCategory->id }}"
                                form="{{ $formId }}"
                                @checked(in_array($carCategory->id, (array) request()->input('car_categories', [])))
                            >
                            <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center w-100 p-2 rounded border cursor-pointer" for="check-car-category-{{ $carCategory->id }}">
                                <span class="filter-option-text">{{ $carCategory->name }}</span>
                                <span class="filter-option-count badge bg-light text-dark">{{ $carCategory->cars_count ?: 0 }}</span>
                            </label>
                        </div>
                    </div>
                @endforeach

                @if($carCategories->count() > 5)
                    <div class="filter-options-extra" style="display: none;">
                        @foreach($carCategories->skip(5) as $carCategory)
                            <div class="filter-option">
                                <div class="custom-filter-check">
                                    <input
                                        type="checkbox"
                                        class="form-check-input submit-form-filter d-none"
                                        value="{{ $carCategory->id }}"
                                        name="car_categories[]"
                                        id="check-car-category-{{ $carCategory->id }}"
                                        form="{{ $formId }}"
                                        @checked(in_array($carCategory->id, (array) request()->input('car_categories', [])))
                                    >
                                    <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center w-100 p-2 rounded border cursor-pointer" for="check-car-category-{{ $carCategory->id }}">
                                        <span class="filter-option-text">{{ $carCategory->name }}</span>
                                        <span class="filter-option-count badge bg-light text-dark">{{ $carCategory->cars_count ?: 0 }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-link btn-sm p-0 mt-2 filter-toggle-btn" data-target=".filter-options-extra">
                        <span class="show-more-text">
                            <x-core::icon name="ti ti-chevron-down" class="me-1" />
                            {{ __('Show more') }}
                        </span>
                        <span class="show-less-text d-none">
                            <x-core::icon name="ti ti-chevron-up" class="me-1" />
                            {{ __('Show less') }}
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>
@endif

{{-- Car Colors Filter --}}
@if($carColors->isNotEmpty() && CarRentalsHelper::isEnabledFilterCarsBy('colors'))
    <div class="filter-widget mb-4">
        <div class="filter-widget-header">
            <div class="filter-icon">
                <x-core::icon name="ti ti-palette" />
            </div>
            <h6 class="filter-title">{{ __('Colors') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="filter-options-list">
                @foreach($carColors->take(5) as $carColor)
                    <div class="filter-option">
                        <div class="custom-filter-check">
                            <input
                                type="checkbox"
                                class="form-check-input submit-form-filter d-none"
                                value="{{ $carColor->id }}"
                                name="car_colors[]"
                                id="check-car-color-{{ $carColor->id }}"
                                form="{{ $formId }}"
                                @checked(in_array($carColor->id, (array) request()->input('car_colors', [])))
                            >
                            <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center w-100 p-2 rounded border cursor-pointer" for="check-car-color-{{ $carColor->id }}">
                                <span class="filter-option-text">{{ $carColor->name }}</span>
                                <span class="filter-option-count badge bg-light text-dark">{{ $carColor->cars_count ?: 0 }}</span>
                            </label>
                        </div>
                    </div>
                @endforeach

                @if($carColors->count() > 5)
                    <div class="filter-options-extra" style="display: none;">
                        @foreach($carColors->skip(5) as $carColor)
                            <div class="filter-option">
                                <div class="custom-filter-check">
                                    <input
                                        type="checkbox"
                                        class="form-check-input submit-form-filter d-none"
                                        value="{{ $carColor->id }}"
                                        name="car_colors[]"
                                        id="check-car-color-{{ $carColor->id }}"
                                        form="{{ $formId }}"
                                        @checked(in_array($carColor->id, (array) request()->input('car_colors', [])))
                                    >
                                    <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center w-100 p-2 rounded border cursor-pointer" for="check-car-color-{{ $carColor->id }}">
                                        <span class="filter-option-text">{{ $carColor->name }}</span>
                                        <span class="filter-option-count badge bg-light text-dark">{{ $carColor->cars_count ?: 0 }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-link btn-sm p-0 mt-2 filter-toggle-btn" data-target=".filter-options-extra">
                        <span class="show-more-text">
                            <x-core::icon name="ti ti-chevron-down" class="me-1" />
                            {{ __('Show more') }}
                        </span>
                        <span class="show-less-text d-none">
                            <x-core::icon name="ti ti-chevron-up" class="me-1" />
                            {{ __('Show less') }}
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>
@endif

{{-- Number of Seats Filter --}}
@if(!empty($seatOptions) && CarRentalsHelper::isEnabledFilterCarsBy('seats'))
    <div class="filter-widget mb-4">
        <div class="filter-widget-header">
            <div class="filter-icon">
                <x-core::icon name="ti ti-armchair" />
            </div>
            <h6 class="filter-title">{{ __('Number of Seats') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="filter-options-list">
                @foreach($seatOptions as $seats => $count)
                    <div class="filter-option">
                        <div class="custom-filter-check">
                            <input
                                type="checkbox"
                                class="form-check-input submit-form-filter d-none"
                                value="{{ $seats }}"
                                name="number_of_seats[]"
                                id="check-seats-{{ $seats }}"
                                form="{{ $formId }}"
                                @checked(in_array($seats, (array) request()->input('number_of_seats', [])))
                            >
                            <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center w-100 p-2 rounded border cursor-pointer" for="check-seats-{{ $seats }}">
                                <span class="filter-option-text">{{ $seats }} {{ __('Seats') }}</span>
                                <span class="filter-option-count badge bg-light text-dark">{{ $count }}</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Number of Doors Filter --}}
@if(!empty($doorOptions) && CarRentalsHelper::isEnabledFilterCarsBy('doors'))
    <div class="filter-widget mb-4">
        <div class="filter-widget-header">
            <div class="filter-icon">
                <x-core::icon name="ti ti-door" />
            </div>
            <h6 class="filter-title">{{ __('Number of Doors') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="filter-options-list">
                @foreach($doorOptions as $doors => $count)
                    <div class="filter-option">
                        <div class="custom-filter-check">
                            <input
                                type="checkbox"
                                class="form-check-input submit-form-filter d-none"
                                value="{{ $doors }}"
                                name="number_of_doors[]"
                                id="check-doors-{{ $doors }}"
                                form="{{ $formId }}"
                                @checked(in_array($doors, (array) request()->input('number_of_doors', [])))
                            >
                            <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center w-100 p-2 rounded border cursor-pointer" for="check-doors-{{ $doors }}">
                                <span class="filter-option-text">{{ $doors }} {{ __('Doors') }}</span>
                                <span class="filter-option-count badge bg-light text-dark">{{ $count }}</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Car Types Filter --}}
@if($carTypes->isNotEmpty() && CarRentalsHelper::isEnabledFilterCarsBy('types'))
    <div class="filter-widget mb-4">
        <div class="filter-widget-header">
            <div class="filter-icon">
                <x-core::icon name="ti ti-car-suv" />
            </div>
            <h6 class="filter-title">{{ __('Vehicle Types') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="filter-options-list">
                @foreach($carTypes->take(5) as $carType)
                    <div class="filter-option">
                        <div class="custom-filter-check">
                            <input
                                type="checkbox"
                                class="form-check-input submit-form-filter d-none"
                                value="{{ $carType->id }}"
                                name="car_types[]"
                                id="check-car-type-{{ $carType->id }}"
                                form="{{ $formId }}"
                                @checked(in_array($carType->id, (array) request()->input('car_types', [])))
                            >
                            <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center w-100 p-2 rounded border cursor-pointer" for="check-car-type-{{ $carType->id }}">
                                <span class="filter-option-text">{{ $carType->name }}</span>
                                <span class="filter-option-count badge bg-light text-dark">{{ $carType->cars_count ?: 0 }}</span>
                            </label>
                        </div>
                    </div>
                @endforeach

                @if($carTypes->count() > 5)
                    <div class="filter-options-extra" style="display: none;">
                        @foreach($carTypes->skip(5) as $carType)
                            <div class="filter-option">
                                <div class="custom-filter-check">
                                    <input
                                        type="checkbox"
                                        class="form-check-input submit-form-filter"
                                        value="{{ $carType->id }}"
                                        name="car_types[]"
                                        id="check-car-type-{{ $carType->id }}"
                                        form="{{ $formId }}"
                                        @checked(in_array($carType->id, (array) request()->input('car_types', [])))
                                    >
                                    <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center w-100 p-2 rounded border cursor-pointer" for="check-car-type-{{ $carType->id }}">
                                        <span class="filter-option-text">{{ $carType->name }}</span>
                                        <span class="filter-option-count badge bg-light text-dark">{{ $carType->cars_count ?: 0 }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-link btn-sm p-0 mt-2 filter-toggle-btn" data-target=".filter-options-extra">
                        <span class="show-more-text">
                            <x-core::icon name="ti ti-chevron-down" class="me-1" />
                            {{ __('Show more') }}
                        </span>
                        <span class="show-less-text d-none">
                            <x-core::icon name="ti ti-chevron-up" class="me-1" />
                            {{ __('Show less') }}
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>
@endif

{{-- Car Transmissions Filter --}}
@if($carTransmissions->isNotEmpty() && CarRentalsHelper::isEnabledFilterCarsBy('transmissions'))
    <div class="filter-widget mb-4">
        <div class="filter-widget-header">
            <div class="filter-icon">
                <x-core::icon name="ti ti-manual-gearbox" />
            </div>
            <h6 class="filter-title">{{ __('Transmission') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="filter-options-list">
                @foreach($carTransmissions as $carTransmission)
                    <div class="filter-option">
                        <div class="custom-filter-check">
                            <input
                                type="checkbox"
                                class="form-check-input submit-form-filter d-none"
                                value="{{ $carTransmission->id }}"
                                name="car_transmissions[]"
                                id="check-car-transmission-{{ $carTransmission->id }}"
                                form="{{ $formId }}"
                                @checked(in_array($carTransmission->id, (array) request()->input('car_transmissions', [])))
                            >
                            <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center w-100 p-2 rounded border cursor-pointer" for="check-car-transmission-{{ $carTransmission->id }}">
                                <span class="filter-option-text">{{ $carTransmission->name }}</span>
                                <span class="filter-option-count badge bg-light text-dark">{{ $carTransmission->cars_count ?: 0 }}</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Car Amenities Filter --}}
@if($carAmenities->isNotEmpty() && CarRentalsHelper::isEnabledFilterCarsBy('amenities'))
    <div class="filter-widget mb-4">
        <div class="filter-widget-header">
            <div class="filter-icon">
                <x-core::icon name="ti ti-star" />
            </div>
            <h6 class="filter-title">{{ __('Amenities') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="filter-options-list">
                @foreach($carAmenities->take(5) as $carAmenity)
                    <div class="filter-option">
                        <div class="custom-filter-check">
                            <input
                                type="checkbox"
                                class="form-check-input submit-form-filter d-none"
                                value="{{ $carAmenity->id }}"
                                name="car_amenities[]"
                                id="check-car-amenity-{{ $carAmenity->id }}"
                                form="{{ $formId }}"
                                @checked(in_array($carAmenity->id, (array) request()->input('car_amenities', [])))
                            >
                            <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center w-100 p-2 rounded border cursor-pointer" for="check-car-amenity-{{ $carAmenity->id }}">
                                <span class="filter-option-text">{{ $carAmenity->name }}</span>
                                <span class="filter-option-count badge bg-light text-dark">{{ $carAmenity->cars_count ?: 0 }}</span>
                            </label>
                        </div>
                    </div>
                @endforeach

                @if($carAmenities->count() > 5)
                    <div class="filter-options-extra" style="display: none;">
                        @foreach($carAmenities->skip(5) as $carAmenity)
                            <div class="filter-option">
                                <div class="custom-filter-check">
                                    <input
                                        type="checkbox"
                                        class="form-check-input submit-form-filter d-none"
                                        value="{{ $carAmenity->id }}"
                                        name="car_amenities[]"
                                        id="check-car-amenity-{{ $carAmenity->id }}"
                                        form="{{ $formId }}"
                                        @checked(in_array($carAmenity->id, (array) request()->input('car_amenities', [])))
                                    >
                                    <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center w-100 p-2 rounded border cursor-pointer" for="check-car-amenity-{{ $carAmenity->id }}">
                                        <span class="filter-option-text">{{ $carAmenity->name }}</span>
                                        <span class="filter-option-count badge bg-light text-dark">{{ $carAmenity->cars_count ?: 0 }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" class="btn btn-link btn-sm p-0 mt-2 filter-toggle-btn" data-target=".filter-options-extra">
                        <span class="show-more-text">
                            <x-core::icon name="ti ti-chevron-down" class="me-1" />
                            {{ __('Show more') }}
                        </span>
                        <span class="show-less-text d-none">
                            <x-core::icon name="ti ti-chevron-up" class="me-1" />
                            {{ __('Show less') }}
                        </span>
                    </button>
                @endif
            </div>
        </div>
    </div>
@endif

{{-- Fuel Types Filter --}}
@if($carFuelTypes->isNotEmpty() && CarRentalsHelper::isEnabledFilterCarsBy('fuels'))
    <div class="filter-widget mb-4">
        <div class="filter-widget-header">
            <div class="filter-icon">
                <x-core::icon name="ti ti-gas-station" />
            </div>
            <h6 class="filter-title">{{ __('Fuel Type') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="filter-options-list">
                @foreach($carFuelTypes as $carFuelType)
                    <div class="filter-option">
                        <div class="custom-filter-check">
                            <input
                                type="checkbox"
                                class="form-check-input submit-form-filter d-none"
                                value="{{ $carFuelType->id }}"
                                name="car_fuel_types[]"
                                id="check-car-fuel-type-{{ $carFuelType->id }}"
                                form="{{ $formId }}"
                                @checked(in_array($carFuelType->id, (array) request()->input('car_fuel_types', [])))
                            >
                            <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center w-100 p-2 rounded border cursor-pointer" for="check-car-fuel-type-{{ $carFuelType->id }}">
                                <span class="filter-option-text">{{ $carFuelType->name }}</span>
                                <span class="filter-option-count badge bg-light text-dark">{{ $carFuelType->cars_count ?: 0 }}</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Review Score Filter --}}
@if($carReviewScores->isNotEmpty() && CarRentalsHelper::isEnabledFilterCarsBy('review_scores'))
    <div class="filter-widget mb-4">
        <div class="filter-widget-header">
            <div class="filter-icon">
                <x-core::icon name="ti ti-star-filled" />
            </div>
            <h6 class="filter-title">{{ __('Rating') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="filter-options-list">
                @foreach($carReviewScores as $carReviewScore)
                    <div class="filter-option">
                        <div class="custom-filter-check">
                            <input
                                type="checkbox"
                                class="form-check-input submit-form-filter d-none"
                                value="{{ $carReviewScore->star }}"
                                name="car_review_scores[]"
                                id="check-car-review-score-{{ $carReviewScore->star }}"
                                form="{{ $formId }}"
                                @checked(in_array($carReviewScore->star, (array) request()->input('car_review_scores', [])))
                            >
                            <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center w-100 p-2 rounded border cursor-pointer" for="check-car-review-score-{{ $carReviewScore->star }}">
                                <div class="d-flex align-items-center">
                                    @include(Theme::getThemeNamespace('views.car-rentals.car-list.partials.review-scores'), ['score' => $carReviewScore->star])
                                </div>
                                <span class="filter-option-count badge bg-light text-dark">{{ $carReviewScore->cars_count ?: 0 }}</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif