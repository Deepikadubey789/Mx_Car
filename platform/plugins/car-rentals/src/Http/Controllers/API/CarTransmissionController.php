<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\CarRentals\Models\CarTransmission;
use Illuminate\Http\Request;

class CarTransmissionController extends BaseApiController
{
    /**
     * List car transmissions
     *
     * @group Car Rentals
     */
    public function index(Request $request)
    {
        $query = CarTransmission::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->oldest('name');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $perPage = min($request->integer('per_page', 50), 100);
        $transmissions = $query->paginate($perPage);

        return $this
            ->httpResponse()
            ->setData($transmissions->map(function ($transmission) {
                return [
                    'id' => $transmission->id,
                    'name' => $transmission->name,
                    'cars_count' => $transmission->cars()->count(),
                ];
            }))
            ->toApiResponse();
    }

    /**
     * Get all car transmissions (simplified)
     *
     * @group Car Rentals
     */
    public function all()
    {
        $transmissions = CarTransmission::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->oldest('name')
            ->get(['id', 'name']);

        return $this
            ->httpResponse()
            ->setData($transmissions)
            ->toApiResponse();
    }

    /**
     * Show car transmission details
     *
     * @group Car Rentals
     */
    public function show(int $id)
    {
        $transmission = CarTransmission::query()
            ->where('id', $id)
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->first();

        if (! $transmission) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car transmission not found')
                ->toApiResponse();
        }

        return $this
            ->httpResponse()
            ->setData([
                'id' => $transmission->id,
                'name' => $transmission->name,
                'cars_count' => $transmission->cars()->count(),
            ])
            ->toApiResponse();
    }
}
