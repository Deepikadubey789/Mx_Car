<?php

namespace Botble\CarRentals\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Services\TripTimelineBuilder;
use Illuminate\Http\JsonResponse;

class BookingTripTimelineController extends BaseController
{
    public function show(Booking $booking, TripTimelineBuilder $builder): JsonResponse
    {
        return response()->json([
            'data' => $builder->build($booking),
        ]);
    }
}
