<?php

namespace Botble\CarRentals\Http\Controllers\Vendor;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Tables\Vendor\BookingTable;
use Illuminate\Http\Response;

class BookingController extends BaseController
{
    public function index(BookingTable $table)
    {
        $this->pageTitle(trans('plugins/car-rentals::booking.name'));

        return $table->renderTable();
    }

    public function show(Booking $booking)
    {
        abort_if($booking->vendor_id != auth('customer')->id(), 403);

        $booking->loadMissing([
            'customer',
            'car.car',
            'services',
            'payment',
            'currency',
            'invoice',
        ]);

        $this->pageTitle(trans('plugins/car-rentals::booking.booking_details') . ' ' . $booking->booking_number);

        return view('plugins/car-rentals::themes.vendor-dashboard.bookings.show', compact('booking'));
    }

    public function print(Booking $booking): Response
    {
        abort_if($booking->vendor_id != auth('customer')->id(), 403);

        $booking->loadMissing([
            'customer',
            'car.car',
            'services',
            'payment',
            'currency',
        ]);

        return response()->view('plugins/car-rentals::bookings.print', compact('booking'));
    }

    public function approve(Booking $booking): BaseHttpResponse
    {
        abort_if($booking->vendor_id != auth('customer')->id(), 403);

        if (! $booking->canBeApproved()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/car-rentals::booking.cannot_approve_booking'));
        }

        $booking->update(['status' => BookingStatusEnum::PROCESSING]);

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/car-rentals::booking.booking_approved_successfully'));
    }

    public function cancel(Booking $booking): BaseHttpResponse
    {
        abort_if($booking->vendor_id != auth('customer')->id(), 403);

        if (! $booking->canBeCancelled()) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/car-rentals::booking.cannot_cancel_booking'));
        }

        $booking->update(['status' => BookingStatusEnum::CANCELLED]);

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/car-rentals::booking.booking_cancelled_successfully'));
    }
}
