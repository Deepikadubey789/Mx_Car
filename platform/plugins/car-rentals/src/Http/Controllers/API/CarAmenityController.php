<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\CarRentals\Models\CarAmenity;
use Illuminate\Http\Request;

class CarAmenityController extends BaseApiController
{
    /**
     * List car amenities
     *
     * @group Car Rentals
     */
    public function index(Request $request)
    {
        $query = CarAmenity::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->oldest('name');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $perPage = min($request->integer('per_page', 50), 100);
        $amenities = $query->paginate($perPage);

        return $this
            ->httpResponse()
            ->setData($amenities->map(function ($amenity) {
                return [
                    'id' => $amenity->id,
                    'name' => $amenity->name,
                    'icon' => $amenity->icon,
                    'cars_count' => $amenity->cars()->count(),
                ];
            }))
            ->toApiResponse();
    }

    /**
     * Get all car amenities (simplified)
     *
     * @group Car Rentals
     */
    public function all()
    {
        $amenities = CarAmenity::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->oldest('name')
            ->get(['id', 'name', 'icon']);

        return $this
            ->httpResponse()
            ->setData($amenities)
            ->toApiResponse();
    }

    /**
     * Show car amenity details
     *
     * @group Car Rentals
     */
    public function show(int $id)
    {
        $amenity = CarAmenity::query()
            ->where('id', $id)
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->first();

        if (! $amenity) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car amenity not found')
                ->toApiResponse();
        }

        return $this
            ->httpResponse()
            ->setData([
                'id' => $amenity->id,
                'name' => $amenity->name,
                'icon' => $amenity->icon,
                'cars_count' => $amenity->cars()->count(),
            ])
            ->toApiResponse();
    }
}
