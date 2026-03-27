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
            'star' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'max:1000'],
        ]);

        // Check if customer has booked this car
        $hasBooking = $customer->bookings()
            ->whereHas('car', function ($query) use ($request): void {
                $query->where('car_id', $request->input('car_id'));
            })
            ->whereIn('status', ['completed', 'confirmed'])
            ->exists();

        if (! $hasBooking) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('You can only review cars you have booked')
                ->toApiResponse();
        }

        // Check if customer has already reviewed this car
        $existingReview = CarReview::query()
            ->where('car_id', $request->input('car_id'))
            ->where('customer_id', $customer->id)
            ->first();

        if ($existingReview) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('You have already reviewed this car')
                ->toApiResponse();
        }

        try {
            $review = CarReview::create([
                'car_id' => $request->input('car_id'),
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'star' => $request->input('star'),
                'comment' => $request->input('comment'),
            ]);

            $review->load(['customer', 'car']);

            return $this
                ->httpResponse()
                ->setData(new ReviewResource($review))
                ->setMessage('Review created successfully')
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
