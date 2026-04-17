<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Http\Resources\ReviewResource;
use Botble\CarRentals\Models\Car;
use Botble\CarRentals\Models\CarReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends BaseApiController
{
    /**
     * List reviews
     *
     * @group Car Rentals
     */
    public function index(Request $request)
    {
        $query = CarReview::query()
            ->with(['customer', 'car'])->latest();

        if ($request->has('car_id')) {
            $query->where('car_id', $request->input('car_id'));
        }

        if ($request->has('star')) {
            $query->where('star', $request->input('star'));
        }

        $perPage = min($request->integer('per_page', 10), 50);
        $reviews = $query->paginate($perPage);

        return $this
            ->httpResponse()
            ->setData(ReviewResource::collection($reviews))
            ->toApiResponse();
    }

    /**
     * Get car reviews
     *
     * @group Car Rentals
     */
    public function getCarReviews(int $id, Request $request)
    {
        $car = Car::find($id);
        if (! $car) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(404)
                ->setMessage('Car not found')
                ->toApiResponse();
        }

        $query = CarReview::query()
            ->where('car_id', $id)
            ->with(['customer'])->latest();

        if ($request->has('star')) {
            $query->where('star', $request->input('star'));
        }

        $perPage = min($request->integer('per_page', 10), 50);
        $reviews = $query->paginate($perPage);

        return $this
            ->httpResponse()
            ->setData(ReviewResource::collection($reviews))
            ->toApiResponse();
    }

    /**
     * Create a review
     *
     * @group Car Rentals
     */
    public function store(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        $request->validate([
            'car_id' => ['required', 'exists:cr_cars,id'],
            // --- NEW: Require the specific trip ID ---
            'booking_id' => ['required', 'exists:cr_bookings,id'],
            'star' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        // --- FIX: Check if the customer actually owns THIS specific booking and it's valid ---
        $booking = $customer->bookings()
            ->where('id', $request->input('booking_id'))
            ->whereHas('car', function ($query) use ($request): void {
                $query->where('car_id', $request->input('car_id'));
            })
            ->whereIn('status', ['completed', 'confirmed'])
            ->first();

        if (! $booking) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('You can only review cars for trips that are completed or confirmed.'))
                ->toApiResponse();
        }

        // --- FIX: Check if customer has already reviewed THIS SPECIFIC TRIP ---
        $existingReview = CarReview::query()
            ->where('booking_id', $request->input('booking_id')) // Allow multiple reviews for the same car, but only ONE per trip!
            ->where('customer_id', $customer->id)
            ->first();

        if ($existingReview) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('You have already reviewed this specific trip.'))
                ->toApiResponse();
        }

        try {
            $review = CarReview::create([
                'car_id' => $request->input('car_id'),
                // --- NEW: Save the booking ID to the database ---
                'booking_id' => $request->input('booking_id'),
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'star' => $request->input('star'),
                'comment' => $request->input('comment'),
                'status' => BaseStatusEnum::PUBLISHED,
            ]);

            $review->load(['customer', 'car']);

            return $this
                ->httpResponse()
                ->setData(new ReviewResource($review))
                ->setMessage(__('Review created successfully'))
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
