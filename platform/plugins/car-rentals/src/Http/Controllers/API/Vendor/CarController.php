<?php

namespace Botble\CarRentals\Http\Controllers\API\Vendor;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Enums\CarStatusEnum;
use Botble\CarRentals\Http\Resources\CarDetailResource;
use Botble\CarRentals\Http\Resources\CarResource;
use Botble\CarRentals\Models\Car;
use Botble\Media\Facades\RvMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarController extends BaseApiController
{
    /**
     * List vendor cars
     *
     * @group Car Rentals - Vendor
     */
    public function index(Request $request)
    {
        $vendor = Auth::guard('sanctum')->user();

        $query = Car::query()
            ->where('vendor_id', $vendor->id)
            ->with(['make', 'type', 'transmission', 'fuel', 'amenities', 'city', 'state', 'country'])->latest();

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('license_plate', 'LIKE', "%{$search}%");
            });
        }

        $perPage = min($request->integer('per_page', 12), 50);
        $cars = $query->paginate($perPage);

        return $this
            ->httpResponse()
            ->setData(CarResource::collection($cars))
            ->toApiResponse();
    }

    /**
     * Create a new car
     *
     * @group Car Rentals - Vendor
     */
    public function store(Request $request)
    {
        $vendor = Auth::guard('sanctum')->user();

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'license_plate' => ['required', 'string', 'max:20', 'unique:cr_cars'],
            'make_id' => ['required', 'exists:cr_car_makes,id'],
            'vehicle_type_id' => ['required', 'exists:cr_car_types,id'],
            'transmission_id' => ['required', 'exists:cr_car_transmissions,id'],
            'fuel_type_id' => ['required', 'exists:cr_car_fuels,id'],
            'number_of_seats' => ['required', 'integer', 'min:1', 'max:50'],
            'number_of_doors' => ['required', 'integer', 'min:2', 'max:10'],
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => ['nullable', 'numeric', 'min:0'],
            'horsepower' => ['nullable', 'numeric', 'min:0'],
            'rental_rate' => ['required', 'numeric', 'min:0'],
            'rental_type' => ['required', 'in:per_day,per_hour'],
            'location' => ['nullable', 'string'],
            'insurance_info' => ['nullable', 'string'],
            'vin' => ['nullable', 'string', 'max:17'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['exists:cr_car_amenities,id'],
        ]);

        try {
            $carData = $request->except(['images', 'amenities']);
            $carData['vendor_id'] = $vendor->id;
            $carData['author_id'] = $vendor->id;
            $carData['author_type'] = get_class($vendor);
            $carData['status'] = CarStatusEnum::AVAILABLE;

            // Handle image uploads
            $images = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $result = RvMedia::handleUpload($image, 0, 'cars');
                    if (! $result['error']) {
                        $images[] = $result['data']->url;
                    }
                }
            }
            $carData['images'] = $images;

            $car = Car::create($carData);

            // Attach amenities
            if ($request->has('amenities')) {
                $car->amenities()->sync($request->input('amenities'));
            }

            $car->load(['make', 'type', 'transmission', 'fuel', 'amenities']);

            return $this
                ->httpResponse()
                ->setData(new CarDetailResource($car))
                ->setMessage('Car created successfully')
                ->toApiResponse();

        } catch (\Exception $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($e->getMessage())
                ->toApiResponse();
        }
    }

    /**
     * Get car details
     *
     * @group Car Rentals - Vendor
     */
    public function show(int $id)
    {
        $vendor = Auth::guard('sanctum')->user();

        $car = Car::query()
            ->where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->with(['make', 'type', 'transmission', 'fuel', 'amenities', 'city', 'state', 'country', 'reviews.customer'])
            ->first();

        if (! $car) {
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
     * Update car
     *
     * @group Car Rentals - Vendor
     */
    public function update(int $id, Request $request)
    {
        $vendor = Auth::guard('sanctum')->user();

        $car = Car::query()
            ->where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->first();

        if (! $car) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car not found')
                ->toApiResponse();
        }

        $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'license_plate' => 'sometimes|required|string|max:20|unique:cr_cars,license_plate,' . $car->id,
            'make_id' => ['sometimes', 'required', 'exists:cr_car_makes,id'],
            'vehicle_type_id' => ['sometimes', 'required', 'exists:cr_car_types,id'],
            'transmission_id' => ['sometimes', 'required', 'exists:cr_car_transmissions,id'],
            'fuel_type_id' => ['sometimes', 'required', 'exists:cr_car_fuels,id'],
            'number_of_seats' => ['sometimes', 'required', 'integer', 'min:1', 'max:50'],
            'number_of_doors' => ['sometimes', 'required', 'integer', 'min:2', 'max:10'],
            'year' => 'sometimes|required|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => ['nullable', 'numeric', 'min:0'],
            'horsepower' => ['nullable', 'numeric', 'min:0'],
            'rental_rate' => ['sometimes', 'required', 'numeric', 'min:0'],
            'rental_type' => ['sometimes', 'required', 'in:per_day,per_hour'],
            'location' => ['nullable', 'string'],
            'insurance_info' => ['nullable', 'string'],
            'vin' => ['nullable', 'string', 'max:17'],
            'status' => ['sometimes', 'required', 'in:available,unavailable,maintenance'],
            'amenities' => ['nullable', 'array'],
            'amenities.*' => ['exists:cr_car_amenities,id'],
        ]);

        try {
            $carData = $request->except(['amenities']);
            $car->update($carData);

            // Update amenities
            if ($request->has('amenities')) {
                $car->amenities()->sync($request->input('amenities'));
            }

            $car->load(['make', 'type', 'transmission', 'fuel', 'amenities']);

            return $this
                ->httpResponse()
                ->setData(new CarDetailResource($car))
                ->setMessage('Car updated successfully')
                ->toApiResponse();

        } catch (\Exception $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($e->getMessage())
                ->toApiResponse();
        }
    }

    /**
     * Delete car
     *
     * @group Car Rentals - Vendor
     */
    public function destroy(int $id)
    {
        $vendor = Auth::guard('sanctum')->user();

        $car = Car::query()
            ->where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->first();

        if (! $car) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car not found')
                ->toApiResponse();
        }

        // Check for active bookings
        $activeBookings = $car->bookings()
            ->whereIn('status', ['pending', 'confirmed', 'in_progress'])
            ->count();

        if ($activeBookings > 0) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Cannot delete car with active bookings')
                ->toApiResponse();
        }

        try {
            // Delete car images
            if ($car->images) {
                foreach ($car->images as $image) {
                    RvMedia::deleteFile($image);
                }
            }

            $car->delete();

            return $this
                ->httpResponse()
                ->setMessage('Car deleted successfully')
                ->toApiResponse();

        } catch (\Exception $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($e->getMessage())
                ->toApiResponse();
        }
    }

    /**
     * Upload car images
     *
     * @group Car Rentals - Vendor
     */
    public function uploadImages(int $id, Request $request)
    {
        $vendor = Auth::guard('sanctum')->user();

        $car = Car::query()
            ->where('id', $id)
            ->where('vendor_id', $vendor->id)
            ->first();

        if (! $car) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car not found')
                ->toApiResponse();
        }

        $request->validate([
            'images' => ['required', 'array', 'max:10'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        try {
            $currentImages = $car->images ?? [];
            $newImages = [];

            foreach ($request->file('images') as $image) {
                $result = RvMedia::handleUpload($image, 0, 'cars');
                if (! $result['error']) {
                    $newImages[] = $result['data']->url;
                }
            }

            $allImages = array_merge($currentImages, $newImages);
            $car->update(['images' => $allImages]);

            return $this
                ->httpResponse()
                ->setData(['images' => $allImages])
                ->setMessage('Images uploaded successfully')
                ->toApiResponse();

        } catch (\Exception $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($e->getMessage())
                ->toApiResponse();
        }
    }
}
