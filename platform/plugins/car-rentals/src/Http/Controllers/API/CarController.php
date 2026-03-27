<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\CarRentals\Enums\CarStatusEnum;
use Botble\CarRentals\Enums\ModerationStatusEnum;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Http\Resources\CarDetailResource;
use Botble\CarRentals\Http\Resources\CarResource;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarAmenity;
use Botble\CarRentals\Models\CarCategory;
use Botble\CarRentals\Models\CarFuel;
use Botble\CarRentals\Models\CarMake;
use Botble\CarRentals\Models\CarTransmission;
use Botble\CarRentals\Models\CarType;
use Illuminate\Http\Request;

class CarController extends BaseApiController
{
    /**
     * List cars
     *
     * @group Car Rentals
     */
    public function index(Request $request)
    {
        $query = Car::query()
            ->where('status', CarStatusEnum::AVAILABLE)
            ->with(['make', 'type', 'transmission', 'fuel', 'amenities', 'city', 'state', 'country', 'reviews']);

        if (CarRentalsHelper::isEnabledPostApproval()) {
            $query->where('moderation_status', ModerationStatusEnum::APPROVED);
        }

        // Filters
        if ($request->has('make_id')) {
            $query->where('make_id', $request->input('make_id'));
        }

        if ($request->has('type_id')) {
            $query->where('vehicle_type_id', $request->input('type_id'));
        }

        if ($request->has('transmission_id')) {
            $query->where('transmission_id', $request->input('transmission_id'));
        }

        if ($request->has('fuel_id')) {
            $query->where('fuel_type_id', $request->input('fuel_id'));
        }

        if ($request->has('min_price')) {
            $query->where('rental_rate', '>=', $request->input('min_price'));
        }

        if ($request->has('max_price')) {
            $query->where('rental_rate', '<=', $request->input('max_price'));
        }

        if ($request->has('seats')) {
            $query->where('number_of_seats', '>=', $request->input('seats'));
        }

        if ($request->has('year_from')) {
            $query->where('year', '>=', $request->input('year_from'));
        }

        if ($request->has('year_to')) {
            $query->where('year', '<=', $request->input('year_to'));
        }

        if ($request->has('location')) {
            $location = $request->input('location');
            $query->where(function ($q) use ($location): void {
                $q->where('location', 'LIKE', "%{$location}%")
                    ->orWhere('city_id', $location)
                    ->orWhere('state_id', $location);
            });
        }

        if ($request->has('amenities')) {
            $amenities = is_array($request->input('amenities'))
                ? $request->input('amenities')
                : explode(',', $request->input('amenities'));

            $query->whereHas('amenities', function ($q) use ($amenities): void {
                $q->whereIn('cr_car_amenities.id', $amenities);
            });
        }

        // Search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('make', function ($q) use ($search): void {
                        $q->where('name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $allowedSorts = ['created_at', 'name', 'rental_rate', 'year'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        }

        $perPage = min($request->integer('per_page', 12), 50);
        $cars = $query->paginate($perPage);

        return $this
            ->httpResponse()
            ->setData(CarResource::collection($cars))
            ->toApiResponse();
    }

    /**
     * Search cars
     *
     * @group Car Rentals
     */
    public function search(Request $request)
    {
        $request->validate([
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['required', 'date', 'after:pickup_date'],
            'location' => ['nullable', 'string'],
        ]);

        $pickupDate = CarRentalsHelper::dateFromRequest($request->input('pickup_date'));
        $returnDate = CarRentalsHelper::dateFromRequest($request->input('return_date'));

        if (! $pickupDate || ! $returnDate) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Invalid date format')
                ->toApiResponse();
        }

        $query = Car::query()
            ->where('status', CarStatusEnum::AVAILABLE)
            ->with(['make', 'type', 'transmission', 'fuel', 'amenities', 'city', 'state', 'country', 'reviews']);

        if (CarRentalsHelper::isEnabledPostApproval()) {
            $query->where('moderation_status', ModerationStatusEnum::APPROVED);
        }

        // Filter by location
        if ($request->has('location')) {
            $location = $request->input('location');
            $query->where(function ($q) use ($location): void {
                $q->where('location', 'LIKE', "%{$location}%")
                    ->orWhere('city_id', $location)
                    ->orWhere('state_id', $location);
            });
        }

        $availableCars = [];
        $dateFormat = CarRentalsHelper::getDateFormat();
        $condition = [
            'start_date' => $pickupDate->format($dateFormat),
            'end_date' => $returnDate->format($dateFormat),
        ];

        foreach ($query->get() as $car) {
            if ($car->isAvailableAt($condition)) {
                $availableCars[] = $car;
            }
        }

        $perPage = min($request->integer('per_page', 12), 50);
        $page = $request->integer('page', 1);
        $offset = ($page - 1) * $perPage;

        $paginatedCars = array_slice($availableCars, $offset, $perPage);
        $total = count($availableCars);

        return $this
            ->httpResponse()
            ->setData([
                'data' => CarResource::collection(collect($paginatedCars)),
                'meta' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $total,
                    'last_page' => ceil($total / $perPage),
                ],
            ])
            ->toApiResponse();
    }

    /**
     * Get car details
     *
     * @group Car Rentals
     */
    public function show(int $id)
    {
        $car = Car::query()
            ->where('id', $id)
            ->where('status', CarStatusEnum::AVAILABLE)
            ->with(['make', 'type', 'transmission', 'fuel', 'amenities', 'city', 'state', 'country', 'reviews.customer', 'tags'])
            ->first();

        if (! $car) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car not found')
                ->toApiResponse();
        }

        if (CarRentalsHelper::isEnabledPostApproval() && $car->moderation_status !== ModerationStatusEnum::APPROVED) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car not found')
                ->toApiResponse();
        }

        return $this
            ->httpResponse()
            ->setData(new CarDetailResource($car))
            ->toApiResponse();
    }

    /**
     * Check car availability
     *
     * @group Car Rentals
     */
    public function checkAvailability(int $id, Request $request)
    {
        $request->validate([
            'pickup_date' => ['required', 'date', 'after_or_equal:today'],
            'return_date' => ['required', 'date', 'after:pickup_date'],
        ]);

        $car = Car::find($id);
        if (! $car || $car->status !== CarStatusEnum::AVAILABLE) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car not found')
                ->toApiResponse();
        }

        $pickupDate = CarRentalsHelper::dateFromRequest($request->input('pickup_date'));
        $returnDate = CarRentalsHelper::dateFromRequest($request->input('return_date'));

        if (! $pickupDate || ! $returnDate) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Invalid date format')
                ->toApiResponse();
        }

        $dateFormat = CarRentalsHelper::getDateFormat();
        $condition = [
            'start_date' => $pickupDate->format($dateFormat),
            'end_date' => $returnDate->format($dateFormat),
        ];

        $isAvailable = $car->isAvailableAt($condition);

        return $this
            ->httpResponse()
            ->setData([
                'available' => $isAvailable,
                'car_id' => $car->id,
                'pickup_date' => $pickupDate->format('Y-m-d'),
                'return_date' => $returnDate->format('Y-m-d'),
            ])
            ->toApiResponse();
    }

    /**
     * Get car filters
     *
     * @group Car Rentals
     */
    public function getFilters()
    {
        $makes = CarMake::where('status', BaseStatusEnum::PUBLISHED)->get(['id', 'name']);
        $types = CarType::where('status', BaseStatusEnum::PUBLISHED)->get(['id', 'name']);
        $transmissions = CarTransmission::where('status', BaseStatusEnum::PUBLISHED)->get(['id', 'name']);
        $fuels = CarFuel::where('status', BaseStatusEnum::PUBLISHED)->get(['id', 'name']);
        $amenities = CarAmenity::where('status', BaseStatusEnum::PUBLISHED)->get(['id', 'name']);
        $categories = CarCategory::where('status', BaseStatusEnum::PUBLISHED)->get(['id', 'name']);

        $priceRange = Car::where('status', CarStatusEnum::AVAILABLE)
            ->selectRaw('MIN(rental_rate) as min_price, MAX(rental_rate) as max_price')
            ->first();

        $yearRange = Car::where('status', CarStatusEnum::AVAILABLE)
            ->selectRaw('MIN(year) as min_year, MAX(year) as max_year')
            ->first();

        return $this
            ->httpResponse()
            ->setData([
                'makes' => $makes,
                'types' => $types,
                'transmissions' => $transmissions,
                'fuels' => $fuels,
                'amenities' => $amenities,
                'categories' => $categories,
                'price_range' => [
                    'min' => $priceRange->min_price ?? 0,
                    'max' => $priceRange->max_price ?? 1000,
                ],
                'year_range' => [
                    'min' => $yearRange->min_year ?? 2000,
                    'max' => $yearRange->max_year ?? date('Y'),
                ],
            ])
            ->toApiResponse();
    }

    /**
     * Find car by slug
     *
     * @group Car Rentals
     */
    public function findBySlug(string $slug)
    {
        $car = Car::query()
            ->where('slug', $slug)
            ->where('status', CarStatusEnum::AVAILABLE)
            ->with(['make', 'type', 'transmission', 'fuel', 'amenities', 'city', 'state', 'country', 'reviews.customer', 'tags'])
            ->first();

        if (! $car) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car not found')
                ->toApiResponse();
        }

        if (CarRentalsHelper::isEnabledPostApproval() && $car->moderation_status !== ModerationStatusEnum::APPROVED) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car not found')
                ->toApiResponse();
        }

        return $this
            ->httpResponse()
            ->setData(new CarDetailResource($car))
            ->toApiResponse();
    }

    /**
     * Get similar cars
     *
     * @group Car Rentals
     */
    public function getSimilarCars(int $id, Request $request)
    {
        $car = Car::find($id);
        if (! $car) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car not found')
                ->toApiResponse();
        }

        $query = Car::query()
            ->where('id', '!=', $id)
            ->where('status', CarStatusEnum::AVAILABLE)
            ->with(['make', 'type', 'transmission', 'fuel', 'amenities', 'city', 'state', 'country', 'reviews']);

        if (CarRentalsHelper::isEnabledPostApproval()) {
            $query->where('moderation_status', ModerationStatusEnum::APPROVED);
        }

        // Find similar cars based on make, type, or price range
        $query->where(function ($q) use ($car): void {
            $q->where('make_id', $car->make_id)
                ->orWhere('vehicle_type_id', $car->vehicle_type_id)
                ->orWhereBetween('price', [$car->price * 0.8, $car->price * 1.2]);
        });

        $limit = min($request->integer('limit', 6), 20);
        $cars = $query->inRandomOrder()->limit($limit)->get();

        return $this
            ->httpResponse()
            ->setData(CarResource::collection($cars))
            ->toApiResponse();
    }
}
