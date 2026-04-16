<?php

namespace Botble\CarRentals\Supports;

use Botble\Base\Facades\BaseHelper;
use Botble\CarRentals\Enums\CarStatusEnum;
use Botble\CarRentals\Facades\CarRentalsHelper as CarRentalsHelperFacade;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarAmenity;
use Botble\CarRentals\Models\CarCategory;
use Botble\CarRentals\Models\CarColor;
use Botble\CarRentals\Models\CarFuel;
use Botble\CarRentals\Models\CarMake;
use Botble\CarRentals\Models\CarReview;
use Botble\CarRentals\Models\CarTransmission;
use Botble\CarRentals\Models\CarType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CarListHelper
{
    public function dataForFilter(): array
    {
        $collectionEmpty = new Collection();

        return [
            CarRentalsHelperFacade::isEnabledFilterCarsBy('categories') ? $this->carCategoriesForFilter() : $collectionEmpty,
            CarRentalsHelperFacade::isEnabledFilterCarsBy('colors') ? $this->carColorsForFilter() : $collectionEmpty,
            CarRentalsHelperFacade::isEnabledFilterCarsBy('types') ? $this->carTypesForFilter() : $collectionEmpty,
            CarRentalsHelperFacade::isEnabledFilterCarsBy('transmissions') ? $this->carTransmissionsForFilter() : $collectionEmpty,
            CarRentalsHelperFacade::isEnabledFilterCarsBy('fuels') ? $this->carFuelTypesForFilter() : $collectionEmpty,
            CarRentalsHelperFacade::isEnabledFilterCarsBy('review_scores') ? $this->carReviewScoresForFilter() : $collectionEmpty,
            CarRentalsHelperFacade::isEnabledFilterCarsBy('prices') ? $this->getCarMaxRentalRate() : null,
            $this->carAmenitiesForFilter(),
            $this->getAdvancedTypes(),
            CarRentalsHelperFacade::isEnabledFilterCarsBy('horsepower') ? $this->getCarMaxHorsepower() : null,
            $this->carMakesForFilter(),
            $this->getSeatOptions(),
            $this->getDoorOptions(),
            $this->getCarMinYear(),
            $this->getCarMaxYear(),
            $this->getCarMaxMileage(),
            CarRentalsHelperFacade::isRentalBookingEnabled() ? $this->getRentalTypes() : [],
            CarRentalsHelperFacade::isEnabledFilterCarsBy('locations') ? $this->getAllLocations() : [],
        ];
    }

    public function getCarFilters(array|Request $inputs): array
    {
        if ($inputs instanceof Request) {
            $inputs = $inputs->input();
        }

        $combinedLocation = BaseHelper::stringify(Arr::get($inputs, 'combined_location'));
        $location = BaseHelper::stringify(Arr::get($inputs, 'location'));
        if ($combinedLocation) {
            if (str_starts_with($combinedLocation, 'location:')) {
                $location = str_replace('location:', '', $combinedLocation);
            }
        }

        $startDate = BaseHelper::stringify(Arr::get($inputs, 'start_date'));
        $endDate = BaseHelper::stringify(Arr::get($inputs, 'end_date'));

        $startDate = $startDate ? str_replace("\0", '', $startDate) : '';
        $endDate = $endDate ? str_replace("\0", '', $endDate) : '';

        $params = [
            'rental_rate_from' => (int) BaseHelper::stringify(Arr::get($inputs, 'rental_rate_from')),
            'rental_rate_to' => (int) BaseHelper::stringify(Arr::get($inputs, 'rental_rate_to')),
            'horsepower_from' => (int) BaseHelper::stringify(Arr::get($inputs, 'horsepower_from')),
            'horsepower_to' => (int) BaseHelper::stringify(Arr::get($inputs, 'horsepower_to')),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'location' => $location,
            'pickup_location' => BaseHelper::stringify(Arr::get($inputs, 'pickup_location')),
            'country_id' => (int) BaseHelper::stringify(Arr::get($inputs, 'country_id')),
            'state_id' => (int) BaseHelper::stringify(Arr::get($inputs, 'state_id')),
            'city_id' => (int) BaseHelper::stringify(Arr::get($inputs, 'city_id')),
            'car_categories' => (array) Arr::get($inputs, 'car_categories', []),
            'car_types' => (array) Arr::get($inputs, 'car_types', []),
            'car_colors' => (array) Arr::get($inputs, 'car_colors', []),
            'car_transmissions' => (array) Arr::get($inputs, 'car_transmissions', []),
            'car_fuel_types' => (array) Arr::get($inputs, 'car_fuel_types', []),
            'car_review_scores' => (array) Arr::get($inputs, 'car_review_scores', []),
            'car_amenities' => (array) Arr::get($inputs, 'car_amenities', []),
            'car_makes' => (array) Arr::get($inputs, 'car_makes', []),
            'number_of_seats' => (array) Arr::get($inputs, 'number_of_seats', []),
            'number_of_doors' => (array) Arr::get($inputs, 'number_of_doors', []),
            'year_from' => (int) Arr::get($inputs, 'year_from', 0),
            'year_to' => (int) Arr::get($inputs, 'year_to', 0),
            'mileage_from' => (int) Arr::get($inputs, 'mileage_from', 0),
            'mileage_to' => (int) Arr::get($inputs, 'mileage_to', 0),
            'rental_types' => (array) Arr::get($inputs, 'rental_types', []),
            'car_make' => Arr::get($inputs, 'car_make'),
            'host_badge' => Arr::get($inputs, 'host_badge', ''),
            'page' => (int) Arr::get($inputs, 'page', 1),
            'per_page' => (int) Arr::get($inputs, 'per_page', CarRentalsHelperFacade::getCarsPerPage()),
            'sort_by' => Arr::get($inputs, 'sort_by', ''),
            'adv_type' => Arr::get($inputs, 'adv_type', ''),
        ];

        $dateFormat = CarRentalsHelperFacade::getDateFormat();

        $validator = Validator::make($params, [
            'rental_rate_from' => ['nullable', 'numeric'],
            'rental_rate_to' => ['nullable', 'numeric'],
            'horsepower_from' => ['nullable', 'numeric'],
            'horsepower_to' => ['nullable', 'numeric'],
            'start_date' => ['nullable', 'string', 'date', 'date_format:' . $dateFormat, 'after_or_equal:today'],
            'end_date' => ['nullable', 'string', 'date', 'date_format:' . $dateFormat, 'after_or_equal:start_date'],
            'location' => ['nullable', 'string'],
            'pickup_location' => ['nullable', 'string'],
            'car_categories' => ['nullable', 'array'],
            'car_colors' => ['nullable', 'array'],
            'car_types' => ['nullable', 'array'],
            'car_transmissions' => ['nullable', 'array'],
            'car_fuel_types' => ['nullable', 'array'],
            'car_review_scores' => ['nullable', 'array'],
            'car_amenities' => ['nullable', 'array'],
            'car_makes' => ['nullable', 'array'],
            'number_of_seats' => ['nullable', 'array'],
            'number_of_doors' => ['nullable', 'array'],
            'year_from' => ['nullable', 'integer', 'min:1900'],
            'year_to' => ['nullable', 'integer', 'min:1900'],
            'mileage_from' => ['nullable', 'integer', 'min:0'],
            'mileage_to' => ['nullable', 'integer', 'min:0'],
            'rental_types' => ['nullable', 'array'],
            'car_make' => ['nullable', 'exists:cr_cars,make_id'],
            'page' => ['nullable', 'integer', 'min:0'],
            'per_page' => ['nullable', 'integer', 'min:0'],
            'sort_by' => ['nullable', Rule::in(array_keys($this->getSortByParams()))],
            'adv_type' => ['nullable', Rule::in(array_keys($this->getAdvancedTypes()))],
        ]);

        return $validator->valid();
    }

    public function carCategoriesForFilter(): Collection
    {
        return Cache::remember('car_list_categories_filter', Carbon::now()->addHour(), function (): Collection {
            return CarCategory::query()
                ->wherePublished()
                ->withCount('activeCars as cars_count')
                ->latest('cars_count')
                ->latest()
                ->get()
                ->where('cars_count', '>', 0);
        });
    }

    public function carColorsForFilter(): Collection
    {
        return Cache::remember('car_list_colors_filter', Carbon::now()->addHour(), function (): Collection {
            return CarColor::query()
                ->wherePublished()
                ->withCount('activeCars as cars_count')
                ->latest('cars_count')
                ->latest()
                ->get()
                ->where('cars_count', '>', 0);
        });
    }

    public function carTypesForFilter(): Collection
    {
        return Cache::remember('car_list_types_filter', Carbon::now()->addHour(), function (): Collection {
            return CarType::query()
                ->wherePublished()
                ->withCount('activeCars as cars_count')
                ->latest('cars_count')->latest()
                ->get();
        });
    }

    public function carTransmissionsForFilter(): Collection
    {
        return Cache::remember('car_list_transmissions_filter', Carbon::now()->addHour(), function (): Collection {
            return CarTransmission::query()
                ->wherePublished()
                ->withCount('activeCars as cars_count')
                ->latest('cars_count')
                ->latest()
                ->get();
        });
    }

    public function carFuelTypesForFilter(): Collection
    {
        return Cache::remember('car_list_fuels_filter', Carbon::now()->addHour(), function (): Collection {
            return CarFuel::query()
                ->wherePublished()
                ->withCount('activeCars as cars_count')
                ->latest('cars_count')
                ->latest()
                ->get()
                ->where('cars_count', '>', 0);
        });
    }

    public function carReviewScoresForFilter(): Collection
    {
        return Cache::remember('car_list_review_scores_filter', Carbon::now()->addHour(), function (): Collection {
            return CarReview::query()
                ->select('star', DB::raw('count(*) as cars_count'))
                ->groupBy('star')->latest('star')
                ->get();
        });
    }

    public function getCarMaxRentalRate(): int
    {
        return Cache::remember('car_list_car_max_rental_rate', Carbon::now()->addHour(), function (): int {
            $rentalRate = Car::query()
                ->where('status', CarStatusEnum::AVAILABLE)
                ->max('rental_rate');

            return $rentalRate ? (int) ceil((float) $rentalRate) : 0;
        });
    }

    public function getCarMaxHorsepower(): int
    {
        return Cache::remember('car_list_car_max_horsepower', Carbon::now()->addHour(), function (): int {
            $horsepower = Car::query()
                ->where('status', CarStatusEnum::AVAILABLE)
                ->whereNotNull('horsepower')
                ->max('horsepower');

            return $horsepower ? (int) ceil((float) $horsepower) : 0;
        });
    }

    public function getPerPageParams(): array
    {
        return [20, 30, 50];
    }

    public function getSortByParams(): array
    {
        return apply_filters('car_list_sort_by_params', [
            'recently_added' => __('Recently added'),
            'most_popular' => __('Most popular'),
            'top_rated' => __('Top rated'),
            'most_viewed' => __('Most Viewed'),
        ]);
    }

    public function getAdvancedTypes(): array
    {
        return [
            'all' => __('All'),
            'new_car' => __('New'),
            'used_car' => __('Used'),
        ];
    }

    public function carAmenitiesForFilter(): Collection
    {
        return Cache::remember('car_list_amenities_filter', Carbon::now()->addHour(), function (): Collection {
            return CarAmenity::query()
                ->wherePublished()
                ->withCount('activeCars as cars_count')
                ->latest('cars_count')->latest()
                ->get()
                ->where('cars_count', '>', 0);
        });
    }

    public function carMakesForFilter(): Collection
    {
        return Cache::remember('car_list_makes_filter', Carbon::now()->addHour(), function (): Collection {
            return CarMake::query()
                ->wherePublished()
                ->withCount('activeCars as cars_count')
                ->latest('cars_count')->latest()
                ->get()
                ->where('cars_count', '>', 0);
        });
    }

    public function getSeatOptions(): array
    {
        return Cache::remember('car_list_seat_options', Carbon::now()->addHour(), function (): array {
            return Car::query()
                ->where('status', CarStatusEnum::AVAILABLE)
                ->whereNotNull('number_of_seats')
                ->selectRaw('number_of_seats, COUNT(*) as count')
                ->groupBy('number_of_seats')
                ->oldest('number_of_seats')
                ->pluck('count', 'number_of_seats')
                ->toArray();
        });
    }

    public function getDoorOptions(): array
    {
        return Cache::remember('car_list_door_options', Carbon::now()->addHour(), function (): array {
            return Car::query()
                ->where('status', CarStatusEnum::AVAILABLE)
                ->whereNotNull('number_of_doors')
                ->selectRaw('number_of_doors, COUNT(*) as count')
                ->groupBy('number_of_doors')
                ->oldest('number_of_doors')
                ->pluck('count', 'number_of_doors')
                ->toArray();
        });
    }

    public function getCarMinYear(): int
    {
        return Cache::remember('car_list_car_min_year', Carbon::now()->addHour(), function (): int {
            $year = Car::query()
                ->where('status', CarStatusEnum::AVAILABLE)
                ->whereNotNull('year')
                ->min('year');

            return $year ? (int) $year : (int) date('Y') - 20;
        });
    }

    public function getCarMaxYear(): int
    {
        return Cache::remember('car_list_car_max_year', Carbon::now()->addHour(), function (): int {
            $year = Car::query()
                ->where('status', CarStatusEnum::AVAILABLE)
                ->whereNotNull('year')
                ->max('year');

            return $year ? (int) $year : (int) date('Y');
        });
    }

    public function getCarMaxMileage(): int
    {
        return Cache::remember('car_list_car_max_mileage', Carbon::now()->addHour(), function (): int {
            $mileage = Car::query()
                ->where('status', CarStatusEnum::AVAILABLE)
                ->whereNotNull('mileage')
                ->max('mileage');

            return $mileage ? (int) ceil((float) $mileage) : 0;
        });
    }

    public function getRentalTypes(): array
    {
        return [
            'per_hour' => __('Per Hour'),
            'per_day' => __('Per Day'),
            'per_week' => __('Per Week'),
            'per_month' => __('Per Month'),
        ];
    }

    public function getAllLocations(): array
    {
        return CarRentalsHelperFacade::getLocations();
    }
}
