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

<div class="content-left order-lg-first d-none d-lg-block car-filters-modern">
    <div class="car-filters-shell">
        <div class="car-filters-shell__intro">
            <p class="car-filters-shell__eyebrow mb-0">{{ __('Smart filters') }}</p>
            <h5 class="car-filters-shell__title mb-1">{{ __('Refine your car search') }}</h5>
            <p class="car-filters-shell__text mb-0">{{ __('Adjust budget, performance, and amenities in one place.') }}</p>
        </div>

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
</div>

<div class="offcanvas offcanvas-start d-lg-none car-filters-offcanvas" tabindex="-1" id="mobileFiltersOffcanvas" aria-labelledby="mobileFiltersOffcanvasLabel">
    <div class="offcanvas-header">
        <div>
            <h5 class="offcanvas-title" id="mobileFiltersOffcanvasLabel">{{ __('Filters') }}</h5>
            <p class="offcanvas-subtitle mb-0">{{ __('Tune your shortlist in seconds') }}</p>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="{{ __('Close') }}"></button>
    </div>
    <div class="offcanvas-body pb-0">
        <div class="car-filters-shell car-filters-shell--mobile">
            <div class="car-filters-shell__intro">
                <p class="car-filters-shell__eyebrow mb-0">{{ __('Mobile filters') }}</p>
                <h5 class="car-filters-shell__title mb-1">{{ __('Refine quickly') }}</h5>
                <p class="car-filters-shell__text mb-0">{{ __('Apply and preview results without leaving the page.') }}</p>
            </div>

            <div class="filter-section filter-section--desktop filter-section--mobile">
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
    <div class="offcanvas-footer d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary flex-fill btn-clear-mobile-filters">
            {{ __('Reset') }}
        </button>
        <button type="button" class="btn btn-primary flex-fill btn-apply-mobile-filters">
            {{ __('Apply Filters') }}
        </button>
    </div>
</div>
