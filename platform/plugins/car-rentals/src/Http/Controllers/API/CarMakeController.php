<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\CarRentals\Enums\CarStatusEnum;
use Botble\CarRentals\Http\Resources\CarResource;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarMake;
use Illuminate\Http\Request;

class CarMakeController extends BaseApiController
{
    /**
     * List car makes
     *
     * @group Car Rentals
     */
    public function index(Request $request)
    {
        $query = CarMake::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->oldest('name');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $perPage = min($request->integer('per_page', 50), 100);
        $makes = $query->paginate($perPage);

        return $this
            ->httpResponse()
            ->setData($makes->map(function ($make) {
                return [
                    'id' => $make->id,
                    'name' => $make->name,
                    'logo' => $make->logo,
                    'website' => $make->website,
                    'cars_count' => $make->cars()->count(),
                ];
            }))
            ->toApiResponse();
    }

    /**
     * Get all car makes (simplified)
     *
     * @group Car Rentals
     */
    public function all()
    {
        $makes = CarMake::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->oldest('name')
            ->get(['id', 'name', 'logo']);

        return $this
            ->httpResponse()
            ->setData($makes)
            ->toApiResponse();
    }

    /**
     * Get car make filters
     *
     * @group Car Rentals
     */
    public function getFilters()
    {
        $makes = CarMake::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->withCount(['cars' => function ($query): void {
                $query->where('status', CarStatusEnum::AVAILABLE);
            }])
            ->having('cars_count', '>', 0)
            ->oldest('name')
            ->get(['id', 'name', 'logo']);

        return $this
            ->httpResponse()
            ->setData($makes)
            ->toApiResponse();
    }

    /**
     * Find car make by slug
     *
     * @group Car Rentals
     */
    public function findBySlug(string $slug)
    {
        $make = CarMake::query()
            ->where('slug', $slug)
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->withCount(['cars' => function ($query): void {
                $query->where('status', CarStatusEnum::AVAILABLE);
            }])
            ->first();

        if (! $make) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car make not found')
                ->toApiResponse();
        }

        return $this
            ->httpResponse()
            ->setData([
                'id' => $make->id,
                'name' => $make->name,
                'slug' => $make->slug,
                'logo' => $make->logo,
                'website' => $make->website,
                'description' => $make->description,
                'cars_count' => $make->cars_count,
            ])
            ->toApiResponse();
    }

    /**
     * Show car make details
     *
     * @group Car Rentals
     */
    public function show(int $id)
    {
        $make = CarMake::query()
            ->where('id', $id)
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->withCount(['cars' => function ($query): void {
                $query->where('status', CarStatusEnum::AVAILABLE);
            }])
            ->first();

        if (! $make) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car make not found')
                ->toApiResponse();
        }

        return $this
            ->httpResponse()
            ->setData([
                'id' => $make->id,
                'name' => $make->name,
                'slug' => $make->slug,
                'logo' => $make->logo,
                'website' => $make->website,
                'description' => $make->description,
                'cars_count' => $make->cars_count,
            ])
            ->toApiResponse();
    }

    /**
     * Get cars by make
     *
     * @group Car Rentals
     */
    public function getCars(int $id, Request $request)
    {
        $make = CarMake::query()
            ->where('id', $id)
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->first();

        if (! $make) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car make not found')
                ->toApiResponse();
        }

        $query = Car::query()
            ->where('make_id', $id)
            ->where('status', CarStatusEnum::AVAILABLE)
            ->with(['make', 'type', 'transmission', 'fuel', 'amenities', 'city', 'state', 'country', 'reviews']);

        $perPage = min($request->integer('per_page', 12), 50);
        $cars = $query->latest()->paginate($perPage);

        return $this
            ->httpResponse()
            ->setData(CarResource::collection($cars))
            ->toApiResponse();
    }
}
