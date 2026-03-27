<?php

namespace Botble\CarRentals\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\BookingCar;
use Botble\CarRentals\Models\Car;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CarAvailabilityCalendarController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans('plugins/car-rentals::car-rentals.name'));
    }

    public function index()
    {
        $this->pageTitle(trans('plugins/car-rentals::car-rentals.availability_calendar.title'));

        Assets::addScriptsDirectly([
            'vendor/core/plugins/car-rentals/libraries/full-calendar/index.global.min.js',
            'vendor/core/plugins/car-rentals/js/car-availability-calendar.js',
        ]);

        Assets::usingVueJS();

        $cars = Car::query()
            ->active()
            ->with(['make', 'vendor'])
            ->orderBy('name')
            ->get();

        return view('plugins/car-rentals::cars.availability-calendar', compact('cars'));
    }

    public function getCarAvailability(Request $request): JsonResponse
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
            'car_id' => ['nullable', 'integer', 'exists:cr_cars,id'],
        ]);

        $startDate = $request->date('start');
        $endDate = $request->date('end');
        $carId = $request->input('car_id');

        $query = BookingCar::query()
            ->with(['booking', 'car'])
            ->whereHas('booking', function ($query): void {
                $query->whereNotIn('status', [BookingStatusEnum::CANCELLED]);
            })
            ->where(function ($query) use ($startDate, $endDate): void {
                $query->whereBetween('rental_start_date', [$startDate, $endDate])
                    ->orWhereBetween('rental_end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate): void {
                        $query->where('rental_start_date', '<=', $startDate)
                            ->where('rental_end_date', '>=', $endDate);
                    });
            });

        if ($carId) {
            $query->where('car_id', $carId);
        }

        $bookings = $query->get();

        $events = $bookings->map(function ($bookingCar) {
            $booking = $bookingCar->booking;
            $car = $bookingCar->car;

            return [
                'id' => $bookingCar->id,
                'title' => $car->name . ' - ' . $booking->customer_name,
                'start' => $bookingCar->rental_start_date->format('Y-m-d'),
                'end' => $bookingCar->rental_end_date->addDay()->format('Y-m-d'), // FullCalendar end is exclusive
                'backgroundColor' => $this->getBookingColor($booking->status),
                'borderColor' => $this->getBookingColor($booking->status),
                'extendedProps' => [
                    'booking_id' => $booking->id,
                    'car_id' => $car->id,
                    'car_name' => $car->name,
                    'customer_name' => $booking->customer_name,
                    'customer_email' => $booking->customer_email,
                    'customer_phone' => $booking->customer_phone,
                    'status' => $booking->status->label(),
                    'amount' => format_price($booking->amount),
                    'booking_number' => $booking->booking_number,
                    'rental_start_date' => $bookingCar->rental_start_date->format('M d, Y'),
                    'rental_end_date' => $bookingCar->rental_end_date->format('M d, Y'),
                    'pickup_address' => $bookingCar->pickupAddressText,
                    'return_address' => $bookingCar->returnAddressText,
                    'detail_url' => route('car-rentals.bookings.edit', $booking->id),
                ],
            ];
        });

        return response()->json($events);
    }

    public function getCarAvailabilityStatus(Request $request): JsonResponse
    {
        $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date'],
        ]);

        $startDate = $request->date('start');
        $endDate = $request->date('end');

        $cars = Car::query()
            ->active()
            ->with(['make', 'vendor'])
            ->get();

        $availabilityData = [];

        foreach ($cars as $car) {
            $isAvailable = $car->isAvailableAt([
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]);

            $availabilityData[] = [
                'id' => $car->id,
                'name' => $car->name,
                'make' => $car->make->name ?? '',
                'vendor' => $car->vendor->name ?? 'Admin',
                'is_available' => $isAvailable,
                'rental_rate' => format_price($car->rental_rate),
                'image' => $car->image,
                'edit_url' => route('car-rentals.cars.edit', $car->id),
            ];
        }

        return response()->json($availabilityData);
    }

    public function getBookingDetails(Request $request): JsonResponse
    {
        $request->validate([
            'booking_id' => ['required', 'integer', 'exists:cr_bookings,id'],
        ]);

        $booking = Booking::query()
            ->with(['car.car', 'customer', 'payment', 'invoice', 'services'])
            ->findOrFail($request->input('booking_id'));

        $html = view('plugins/car-rentals::bookings.information', [
            'booking' => $booking,
            'displayBookingStatus' => true,
        ])->render();

        return response()->json([
            'success' => true,
            'data' => $html,
            'edit_url' => route('car-rentals.bookings.edit', $booking->id),
        ]);
    }

    private function getBookingColor(BookingStatusEnum $status): string
    {
        return match ($status) {
            BookingStatusEnum::PENDING => '#ffc107',
            BookingStatusEnum::PROCESSING => '#17a2b8',
            BookingStatusEnum::COMPLETED => '#28a745',
            BookingStatusEnum::CANCELLED => '#dc3545',
            default => '#6c757d',
        };
    }
}
