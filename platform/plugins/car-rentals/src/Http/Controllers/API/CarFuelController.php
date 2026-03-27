<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\CarRentals\Models\CarFuel;
use Illuminate\Http\Request;

class CarFuelController extends BaseApiController
{
    /**
     * List car fuel types
     *
     * @group Car Rentals
     */
    public function index(Request $request)
    {
        $query = CarFuel::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->oldest('name');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $perPage = min($request->integer('per_page', 50), 100);
        $fuels = $query->paginate($perPage);

        return $this
            ->httpResponse()
            ->setData($fuels->map(function ($fuel) {
                return [
                    'id' => $fuel->id,
                    'name' => $fuel->name,
                    'cars_count' => $fuel->cars()->count(),
                ];
            }))
            ->toApiResponse();
    }

    /**
     * Get all car fuel types (simplified)
     *
     * @group Car Rentals
     */
    public function all()
    {
        $fuels = CarFuel::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->oldest('name')
            ->get(['id', 'name']);

        return $this
            ->httpResponse()
            ->setData($fuels)
            ->toApiResponse();
    }

    /**
     * Show car fuel type details
     *
     * @group Car Rentals
     */
    public function show(int $id)
    {
        $fuel = CarFuel::query()
            ->where('id', $id)
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->first();

        if (! $fuel) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car fuel type not found')
                ->toApiResponse();
        }

        return $this
            ->httpResponse()
            ->setData([
                'id' => $fuel->id,
                'name' => $fuel->name,
                'cars_count' => $fuel->cars()->count(),
            ])
            ->toApiResponse();
    }
}
