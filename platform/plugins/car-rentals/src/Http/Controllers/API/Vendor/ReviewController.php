<?php

namespace Botble\CarRentals\Http\Controllers\API\Vendor;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Http\Resources\ReviewResource;
use Botble\CarRentals\Models\CarReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends BaseApiController
{
    /**
     * List vendor reviews
     *
     * @group Car Rentals - Vendor
     */
    public function index(Request $request)
    {
        $vendor = Auth::guard('sanctum')->user();

        $query = CarReview::query()
            ->whereHas('car', function ($q) use ($vendor): void {
                $q->where('vendor_id', $vendor->id);
            })
            ->with(['customer', 'car'])->latest();

        if ($request->has('star')) {
            $query->where('star', $request->input('star'));
        }

        if ($request->has('car_id')) {
            $query->where('car_id', $request->input('car_id'));
        }

        $perPage = min($request->integer('per_page', 10), 50);
        $reviews = $query->paginate($perPage);

        return $this
            ->httpResponse()
            ->setData(ReviewResource::collection($reviews))
            ->toApiResponse();
    }

    /**
     * Reply to a review
     *
     * @group Car Rentals - Vendor
     */
    public function reply(int $id, Request $request)
    {
        $vendor = Auth::guard('sanctum')->user();

        $review = CarReview::query()
            ->where('id', $id)
            ->whereHas('car', function ($q) use ($vendor): void {
                $q->where('vendor_id', $vendor->id);
            })
            ->first();

        if (! $review) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Review not found')
                ->toApiResponse();
        }

        $request->validate([
            'reply' => ['required', 'string', 'max:1000'],
        ]);

        try {
            $review->update([
                'vendor_reply' => $request->input('reply'),
                'vendor_replied_at' => now(),
            ]);

            $review->load(['customer', 'car']);

            return $this
                ->httpResponse()
                ->setData(new ReviewResource($review))
                ->setMessage('Reply added successfully')
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
