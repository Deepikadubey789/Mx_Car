@php
    [$carCategories, $carColors, $carTypes, $carTransmissions, $carFuelTypes, $carReviewScores, $carMaxRentalRate, $carAmenities, $advancedTypes, $carMaxHorsepower, $carMakes, $seatOptions, $doorOptions, $carMinYear, $carMaxYear, $carMaxMileage, $rentalTypes, $allLocations] = CarListHelper::dataForFilter(request()->input());

   $layout = BaseHelper::stringify(request()->query('layout'));

    if (!in_array($layout, ['list', 'grid'])) {
        $layout = $defaultLayout ?? 'grid';
    }

    $col = BaseHelper::stringify(request()->query('col'));

    if (empty($col)) {
        $col = (int) ($layoutCol ?? 4);
    }

    if(empty($enableFilter)) {
        $enableFilter = BaseHelper::stringify(request()->query('filter'));

        if (empty($enableFilter)) {
            $enableFilter = 'no';
        }
    }
@endphp

<div class="content-left order-lg-first d-none d-lg-block">
    <div class="filter-section filter-section--desktop">
        @include(Theme::getThemeNamespace('views.car-rentals.car-list.partials.filter-form'), [
            'layout' => $layout,
            'col' => $col,
            'enableFilter' => $enableFilter,
            'cars' => $cars,
            'formId' => 'cars-filter-form',
        ])
    </div>
</div>

<div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="mobileFiltersOffcanvas" aria-labelledby="mobileFiltersOffcanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileFiltersOffcanvasLabel">{{ __('Filters') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('Close') }}"></button>
    </div>
    <div class="offcanvas-body">
        <div class="filter-section filter-section--desktop">
            @include(Theme::getThemeNamespace('views.car-rentals.car-list.partials.filter-form'), [
                'layout' => $layout,
                'col' => $col,
                'enableFilter' => $enableFilter,
                'cars' => $cars,
                'formId' => 'cars-filter-form-mobile',
            ])
        </div>
    </div>
</div>
