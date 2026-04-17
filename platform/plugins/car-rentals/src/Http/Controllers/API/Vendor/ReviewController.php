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

    public function rateCustomer(Request $request)
    {
        $vendor = Auth::guard('sanctum')->user();

        $request->validate([
            'booking_id' => 'required|exists:cr_bookings,id',
            'customer_id' => 'required|exists:cr_customers,id',
            'star' => 'required|integer|min:1|max:5',
            'content' => 'required|string|min:10|max:1000',
        ]);

        // Verify the booking actually belongs to this vendor
        $booking = \Botble\CarRentals\Models\Booking::query()
            ->where('id', $request->input('booking_id'))
            ->where('vendor_id', $vendor->id)
            ->where('customer_id', $request->input('customer_id'))
            ->first();

        if (!$booking) {
            return $this->httpResponse()
                ->setError()
                ->setCode(403)
                ->setMessage(__('You do not have permission to review this trip.'))
                ->toApiResponse();
        }

        // Prevent duplicate reviews
        $existingReview = \Botble\CarRentals\Models\CustomerReview::query()
            ->where('booking_id', $booking->id)
            ->where('vendor_id', $vendor->id)
            ->exists();

        if ($existingReview) {
            return $this->httpResponse()
                ->setError()
                ->setMessage(__('You have already reviewed this guest for this trip.'))
                ->toApiResponse();
        }

        try {
            // Create the two-sided review
            $review = \Botble\CarRentals\Models\CustomerReview::query()->create([
                'vendor_id' => $vendor->id,
                'customer_id' => $request->input('customer_id'),
                'booking_id' => $booking->id,
                'star' => $request->input('star'),
                'content' => $request->input('content'),
                'status' => \Botble\Base\Enums\BaseStatusEnum::PUBLISHED,
            ]);

            return $this->httpResponse()
                ->setData($review)
                ->setMessage(__('Guest review submitted successfully.'))
                ->toApiResponse();
                
        } catch (\Exception $e) {
            return $this->httpResponse()
                ->setError()
                ->setMessage($e->getMessage())
                ->toApiResponse();
        }
    }
}
