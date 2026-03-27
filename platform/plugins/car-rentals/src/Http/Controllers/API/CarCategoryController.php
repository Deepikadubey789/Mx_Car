<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\CarRentals\Models\CarCategory;
use Illuminate\Http\Request;

class CarCategoryController extends BaseApiController
{
    /**
     * List car categories
     *
     * @group Car Rentals
     */
    public function index(Request $request)
    {
        $query = CarCategory::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->oldest('name');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%");
        }

        $perPage = min($request->integer('per_page', 50), 100);
        $categories = $query->paginate($perPage);

        return $this
            ->httpResponse()
            ->setData($categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'description' => $category->description,
                    'cars_count' => $category->cars()->count(),
                ];
            }))
            ->toApiResponse();
    }

    /**
     * Get all car categories (simplified)
     *
     * @group Car Rentals
     */
    public function all()
    {
        $categories = CarCategory::query()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->oldest('name')
            ->get(['id', 'name']);

        return $this
            ->httpResponse()
            ->setData($categories)
            ->toApiResponse();
    }

    /**
     * Show car category details
     *
     * @group Car Rentals
     */
    public function show(int $id)
    {
        $category = CarCategory::query()
            ->where('id', $id)
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->first();

        if (! $category) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car category not found')
                ->toApiResponse();
        }

        return $this
            ->httpResponse()
            ->setData([
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'cars_count' => $category->cars()->count(),
            ])
            ->toApiResponse();
    }
}
