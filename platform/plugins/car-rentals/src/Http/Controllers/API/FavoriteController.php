<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Http\Resources\CarResource;
use Botble\CarRentals\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends BaseApiController
{
    /**
     * List user's favorite cars
     *
     * @group Car Rentals - Favorites
     */
    public function index(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $query = $user->favoriteCars()
            ->with(['make', 'type', 'transmission', 'fuel', 'amenities', 'city', 'state', 'country']);

        $perPage = min($request->integer('per_page', 10), 50);
        $cars = $query->paginate($perPage);

        return $this
            ->httpResponse()
            ->setData(CarResource::collection($cars))
            ->toApiResponse();
    }

    /**
     * Add car to favorites
     *
     * @group Car Rentals - Favorites
     */
    public function store(string $carId)
    {
        $user = Auth::guard('sanctum')->user();

        $car = Car::query()->find($carId);

        if (! $car) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car not found')
                ->toApiResponse();
        }

        // Check if already in favorites
        if ($user->favoriteCars()->where('car_id', $carId)->exists()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(409)
                ->setMessage('Car already in favorites')
                ->toApiResponse();
        }

        $user->favoriteCars()->attach($carId, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return $this
            ->httpResponse()
            ->setMessage('Car added to favorites')
            ->setData(['is_favorite' => true])
            ->toApiResponse();
    }

    /**
     * Remove car from favorites
     *
     * @group Car Rentals - Favorites
     */
    public function destroy(string $carId)
    {
        $user = Auth::guard('sanctum')->user();

        $car = Car::query()->find($carId);

        if (! $car) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car not found')
                ->toApiResponse();
        }

        $user->favoriteCars()->detach($carId);

        return $this
            ->httpResponse()
            ->setMessage('Car removed from favorites')
            ->setData(['is_favorite' => false])
            ->toApiResponse();
    }
}
