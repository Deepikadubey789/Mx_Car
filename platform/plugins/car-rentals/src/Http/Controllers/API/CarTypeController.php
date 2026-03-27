<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\CarRentals\Models\CarType;
use Illuminate\Http\Request;

class CarTypeController extends BaseApiController
{
    /**
     * List car types
     *
     * @group Car Rentals
     */
    public function index(Request $request)
    {
        $query = CarType::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->oldest('name');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $perPage = min($request->integer('per_page', 50), 100);
        $types = $query->paginate($perPage);

        return $this
            ->httpResponse()
            ->setData($types->map(function ($type) {
                return [
                    'id' => $type->id,
                    'name' => $type->name,
                    'icon' => $type->icon,
                    'cars_count' => $type->cars()->count(),
                ];
            }))
            ->toApiResponse();
    }

    /**
     * Get all car types (simplified)
     *
     * @group Car Rentals
     */
    public function all()
    {
        $types = CarType::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->oldest('name')
            ->get(['id', 'name', 'icon']);

        return $this
            ->httpResponse()
            ->setData($types)
            ->toApiResponse();
    }

    /**
     * Show car type details
     *
     * @group Car Rentals
     */
    public function show(int $id)
    {
        $type = CarType::query()
            ->where('id', $id)
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->first();

        if (! $type) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car type not found')
                ->toApiResponse();
        }

        return $this
            ->httpResponse()
            ->setData([
                'id' => $type->id,
                'name' => $type->name,
                'icon' => $type->icon,
                'description' => $type->description,
                'cars_count' => $type->cars()->count(),
            ])
            ->toApiResponse();
    }
}
