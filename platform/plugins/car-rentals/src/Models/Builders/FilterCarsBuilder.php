<?php

namespace Botble\CarRentals\Models\Builders;

use Botble\Base\Models\BaseQueryBuilder;
use Botble\CarRentals\Enums\ModerationStatusEnum;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\Location\Facades\Location;
use Botble\Location\Models\City;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class FilterCarsBuilder extends BaseQueryBuilder
{
    public function filterCars(array $filters = []): self
    {
        if (! is_in_admin()) {
            $enabledPostApproval = CarRentalsHelper::isEnabledPostApproval();

            if ($enabledPostApproval) {
                $this->where('moderation_status', ModerationStatusEnum::APPROVED);
            }
        }

        $filters = [
            'rental_rate_from' => null,
            'rental_rate_to' => null,
            'horsepower_from' => null,
            'horsepower_to' => null,
            'year_from' => null,
            'year_to' => null,
            'mileage_from' => null,
            'mileage_to' => null,
            'start_date' => null,
            'end_date' => null,
            'location' => null,
            'pickup_location' => null,
            'country_id' => null,
            'state_id' => null,
            'city_id' => null,
            'car_categories' => [],
            'car_types' => [],
            'car_transmissions' => [],
            'car_fuel_types' => [],
            'car_review_scores' => [],
            'car_booking_locations' => [],
            'car_amenities' => [],
            'car_makes' => [],
            'rental_types' => [],
            'number_of_seats' => [],
            'number_of_doors' => [],
            'page' => null,
            'per_page' => null,
            ...$filters,
        ];

        $carCategories = Arr::get($filters, 'car_categories', []);

        $carCategories = $carCategories ? array_map('intval', array_filter($carCategories)) : [];

        if ($carCategories) {
            $this->whereHas('categories', function (Builder $query) use ($carCategories) {
                return $query->whereIn('cr_car_categories.id', $carCategories);
            });
        }

        $carColors = Arr::get($filters, 'car_colors', []);

        $carColors = $carColors ? array_map('intval', array_filter($carColors)) : [];

        if ($carColors) {
            $this->whereHas('colors', function (Builder $query) use ($carColors) {
                return $query->whereIn('cr_car_colors.id', $carColors);
            });
        }

        $carTypes = Arr::get($filters, 'car_types', []);

        $carTypes = $carTypes ? array_map('intval', array_filter($carTypes)) : [];

        if ($carTypes) {
            $this->whereIn('vehicle_type_id', $carTypes);
        }

        $carTransmissions = Arr::get($filters, 'car_transmissions', []);

        $carTransmissions = $carTransmissions ? array_map('intval', array_filter($carTransmissions)) : [];

        if ($carTransmissions) {
            $this->whereIn('transmission_id', $carTransmissions);
        }

        $carStatus = Arr::get($filters, 'adv_type');

        if (in_array($carStatus, ['used_car', 'new_car'])) {
            $this->where('is_used', $carStatus === 'used_car');
        }

        $carFuelTypes = Arr::get($filters, 'car_fuel_types', []);

        $carFuelTypes = $carFuelTypes ? array_map('intval', array_filter($carFuelTypes)) : [];

        if ($carFuelTypes) {
            $this->whereIn('fuel_type_id', $carFuelTypes);
        }

        // Removed car_booking_locations filter as pick_address_id field is being removed

        $carReviewScores = Arr::get($filters, 'car_review_scores', []);

        $carReviewScores = $carReviewScores ? array_map('intval', array_filter($carReviewScores)) : [];

        if ($carReviewScores) {
            $this->whereHas('reviews', function (Builder $query) use ($carReviewScores) {
                return $query->whereIn('cr_car_reviews.star', $carReviewScores);
            });
        }

        $rentalRateFrom = Arr::get($filters, 'rental_rate_from');
        $rentalRateTo = Arr::get($filters, 'rental_rate_to');

        // Convert prices from current currency to default currency for filtering
        $currentCurrency = get_application_currency();
        if ($currentCurrency && ! $currentCurrency->is_default && $currentCurrency->exchange_rate > 0) {
            if ($rentalRateFrom && $rentalRateFrom > 0) {
                $rentalRateFrom = $rentalRateFrom / $currentCurrency->exchange_rate;
            }
            if ($rentalRateTo && $rentalRateTo > 0) {
                $rentalRateTo = $rentalRateTo / $currentCurrency->exchange_rate;
            }
        }

        if ($rentalRateFrom && $rentalRateFrom > 0) {
            $this->where(function ($query) use ($rentalRateFrom): void {
                $query->whereNull('rental_rate')
                    ->orWhere('rental_rate', '>=', $rentalRateFrom);
            });
        }

        if ($rentalRateTo && $rentalRateTo > 0) {
            $this->where(function ($query) use ($rentalRateTo): void {
                $query->whereNull('rental_rate')
                    ->orWhere('rental_rate', '<=', $rentalRateTo);
            });
        }

        $horsepowerFrom = Arr::get($filters, 'horsepower_from');

        if ($horsepowerFrom && $horsepowerFrom > 0) {
            $this->where(function ($query) use ($horsepowerFrom): void {
                $query->whereNull('horsepower')
                    ->orWhere('horsepower', '>=', $horsepowerFrom);
            });
        }

        $horsepowerTo = Arr::get($filters, 'horsepower_to');

        if ($horsepowerTo && $horsepowerTo > 0) {
            $this->where(function ($query) use ($horsepowerTo): void {
                $query->whereNull('horsepower')
                    ->orWhere('horsepower', '<=', $horsepowerTo);
            });
        }

        $carAmenities = Arr::get($filters, 'car_amenities', []);

        $carAmenities = $carAmenities ? array_map('intval', array_filter($carAmenities)) : [];

        if ($carAmenities) {
            $this->whereHas('amenities', function (Builder $query) use ($carAmenities) {
                return $query->whereIn('cr_car_amenities.id', $carAmenities);
            });
        }

        $carMakes = Arr::get($filters, 'car_makes', []);

        $carMakes = $carMakes ? array_map('intval', array_filter($carMakes)) : [];

        if ($carMakes) {
            $this->whereIn('make_id', $carMakes);
        }

        $rentalTypes = Arr::get($filters, 'rental_types', []);

        $rentalTypes = $rentalTypes ? array_filter($rentalTypes) : [];

        if ($rentalTypes) {
            $this->whereIn('rental_type', $rentalTypes);
        }

        $yearFrom = Arr::get($filters, 'year_from');

        if ($yearFrom && $yearFrom > 0) {
            $this->where(function ($query) use ($yearFrom): void {
                $query->whereNull('year')
                    ->orWhere('year', '>=', $yearFrom);
            });
        }

        $yearTo = Arr::get($filters, 'year_to');

        if ($yearTo && $yearTo > 0) {
            $this->where(function ($query) use ($yearTo): void {
                $query->whereNull('year')
                    ->orWhere('year', '<=', $yearTo);
            });
        }

        $mileageFrom = Arr::get($filters, 'mileage_from');

        if ($mileageFrom && $mileageFrom > 0) {
            $this->where(function ($query) use ($mileageFrom): void {
                $query->whereNull('mileage')
                    ->orWhere('mileage', '>=', $mileageFrom);
            });
        }

        $mileageTo = Arr::get($filters, 'mileage_to');

        if ($mileageTo && $mileageTo > 0) {
            $this->where(function ($query) use ($mileageTo): void {
                $query->whereNull('mileage')
                    ->orWhere('mileage', '<=', $mileageTo);
            });
        }

        $numberOfSeats = Arr::get($filters, 'number_of_seats', []);

        $numberOfSeats = $numberOfSeats ? array_map('intval', array_filter($numberOfSeats)) : [];

        if ($numberOfSeats) {
            $this->whereIn('number_of_seats', $numberOfSeats);
        }

        $numberOfDoors = Arr::get($filters, 'number_of_doors', []);

        $numberOfDoors = $numberOfDoors ? array_map('intval', array_filter($numberOfDoors)) : [];

        if ($numberOfDoors) {
            $this->whereIn('number_of_doors', $numberOfDoors);
        }

        // Removed pickup_location filter as pickupAddress relationship is being removed

        // Filter by direct location fields (country_id, state_id, city_id)
        $countryId = (int) Arr::get($filters, 'country_id');
        $stateId = (int) Arr::get($filters, 'state_id');
        $cityId = (int) Arr::get($filters, 'city_id');

        if (is_plugin_active('location') && ($countryId || $stateId || $cityId)) {
            if ($cityId) {
                // When city is selected, also match by city's state and country
                $this->where(function ($query) use ($cityId): void {
                    $query->where('cr_cars.city_id', $cityId);

                    // Also find cars in the same state/country if city is not set
                    $city = City::find($cityId);
                    if ($city) {
                        $query->orWhere(function ($q) use ($city): void {
                            $q->whereNull('cr_cars.city_id')
                                ->where('cr_cars.state_id', $city->state_id);
                        });
                        $query->orWhere(function ($q) use ($city): void {
                            $q->whereNull('cr_cars.city_id')
                                ->whereNull('cr_cars.state_id')
                                ->where('cr_cars.country_id', $city->country_id);
                        });
                    }
                });
            } elseif ($stateId) {
                $this->where('cr_cars.state_id', $stateId);
            } elseif ($countryId) {
                $this->where('cr_cars.country_id', $countryId);
            }
        }

        // Legacy location filtering (for backward compatibility)
        // Skip if we already have city_id set (new location search)
        if (
            is_plugin_active('location')
            && Arr::get($filters, 'location')
            && ! $cityId  // Only use legacy search if no city_id is provided
        ) {
            $model = $this;
            if ($model instanceof BaseQueryBuilder) {
                $className = get_class($model->getModel());
            } else {
                $className = get_class($model);
            }

            $location = Arr::get($filters, 'location');

            if (Location::isSupported($className)) {
                $locationData = explode(',', $location);

                if (count($locationData) > 1) {
                    $model
                        ->leftJoin('cities as c', 'c.id', '=', 'cr_cars.city_id')
                        ->leftJoin('states', 'states.id', '=', 'cr_cars.state_id')
                        ->where('c.name', 'LIKE', '%' . $location . '%')
                        ->orWhere('states.name', 'LIKE', '%' . $location . '%');
                } else {
                    $model
                        ->leftJoin('cities as c', 'c.id', '=', 'cr_cars.city_id')
                        ->leftJoin('states', 'states.id', '=', 'cr_cars.state_id')
                        ->leftJoin('countries', 'countries.id', '=', 'cr_cars.country_id')
                        ->where('c.name', 'LIKE', '%' . $location . '%')
                        ->orWhere('states.name', 'LIKE', '%' . $location . '%')
                        ->orWhere('countries.name', 'LIKE', '%' . $location . '%');
                }
            }
        }

        if ($filters['start_date'] || $filters['end_date']) {
            $this->whereAvailableAt($filters);
        }

        // Host badge filter
        $hostBadge = Arr::get($filters, 'host_badge');
        if ($hostBadge && in_array($hostBadge, ['rising_star', 'top_host', 'all_star'])) {
            $this->whereHas('vendor', function (Builder $query) use ($hostBadge) {
                $query->whereHas('qualityScore', function ($q) use ($hostBadge) {
                    $q->where(function ($inner) use ($hostBadge) {
                        $inner->where(function ($a) use ($hostBadge) {
                            $a->where('badge_override', true)
                              ->where('override_badge', $hostBadge);
                        })->orWhere(function ($b) use ($hostBadge) {
                            $b->where('badge_override', false)
                              ->where('badge_tier', $hostBadge);
                        })->orWhere(function ($c) use ($hostBadge) {
                            $c->whereNull('badge_override')
                              ->where('badge_tier', $hostBadge);
                        });
                    });
                });
            });
        }

        return $this;
    }

    public function sortCars(string $orderBy): self
    {
        if (! is_in_admin()) {
            $enabledPostApproval = CarRentalsHelper::isEnabledPostApproval();

            if ($enabledPostApproval) {
                $this->where('moderation_status', ModerationStatusEnum::APPROVED);
            }
        }

        $this->oldest('cr_cars.order');
        // Badge-based ranking — All-Star upar, phir Top Host, phir Rising Star
        $this->leftJoin('cr_vendor_quality_scores as vqs', 'vqs.vendor_id', '=', 'cr_cars.author_id')
            ->orderByRaw("
                CASE
                    WHEN vqs.badge_override = 1 AND vqs.override_badge = 'all_star' THEN 1
                    WHEN vqs.badge_override = 1 AND vqs.override_badge = 'top_host' THEN 2
                    WHEN vqs.badge_override = 1 AND vqs.override_badge = 'rising_star' THEN 3
                    WHEN vqs.badge_tier = 'all_star'    THEN 1
                    WHEN vqs.badge_tier = 'top_host'    THEN 2
                    WHEN vqs.badge_tier = 'rising_star' THEN 3
                    ELSE 4
                END ASC
            ");
            
        switch ($orderBy) {
            case 'most_popular':
                $this->oldest('cr_cars.created_at');

                break;
            case 'top_rated':
                $this->leftJoin('cr_car_reviews', 'cr_cars.id', '=', 'cr_car_reviews.car_id')
                    ->groupBy('cr_cars.id')->latest(DB::raw('AVG(cr_car_reviews.star)'));

                break;
            case 'most_viewed':
                $this->leftJoin('cr_car_views', 'cr_cars.id', '=', 'cr_car_views.car_id')
                    ->groupBy('cr_cars.id')->latest(DB::raw('AVG(cr_car_views.views)'));

                break;
            default:
                $this->latest('cr_cars.created_at');
        }

        return $this;
    }
}
