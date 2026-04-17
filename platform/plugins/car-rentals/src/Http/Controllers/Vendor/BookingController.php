<?php

namespace Botble\CarRentals\Http\Controllers\Vendor;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\CarRentals\Enums\BookingStatusEnum;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Tables\Vendor\BookingTable;
use Illuminate\Http\Response;
use Botble\CarRentals\Models\CustomerReview;
use Illuminate\Http\Request;

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
            'customer.receivedReviews.vendor',
            'customer',
            'car.car',
            'services',
            'payment',
            'currency',
            'invoice',
            'claims',
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

        $booking->update(['status' => BookingStatusEnum::CANCELLED, 'cancelled_by' => 'host', 'cancelled_at' => now()]);

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/car-rentals::booking.booking_cancelled_successfully'));
    }

    public function rateCustomer(Booking $booking, Request $request): BaseHttpResponse
    {
        // Ensure this vendor owns this booking
        abort_if($booking->vendor_id != auth('customer')->id(), 403);

        // Ensure the booking is completed
        if ($booking->status->getValue() !== \Botble\CarRentals\Enums\BookingStatusEnum::COMPLETED) {
            return $this->httpResponse()->setError()->setMessage('You can only rate the customer after the booking is completed.');
        }

        // Validate the request
        $request->validate([
            'star' => 'required|numeric|min:1|max:5',
            'content' => 'nullable|string|max:1000',
        ]);

        // Check if review already exists
        $exists = CustomerReview::where('booking_id', $booking->id)->exists();
        if ($exists) {
            return $this->httpResponse()->setError()->setMessage('You have already rated this customer for this booking.');
        }

        // Save the review
        CustomerReview::create([
            'vendor_id' => auth('customer')->id(),
            'customer_id' => $booking->customer_id,
            'booking_id' => $booking->id,
            'star' => $request->input('star'),
            'content' => $request->input('content'),
            'status' => 'published'
        ]);

        return $this->httpResponse()->setMessage('Customer rated successfully!');
    }

    public function deleteCustomerReview(Booking $booking): BaseHttpResponse
    {
        // Ensure this vendor owns this booking
        abort_if($booking->vendor_id != auth('customer')->id(), 403);

        // Find the review
        $review = CustomerReview::where('booking_id', $booking->id)
            ->where('vendor_id', auth('customer')->id())
            ->first();

        if (! $review) {
            return $this->httpResponse()->setError()->setMessage(__('Review not found!'));
        }

        // Delete the review
        $review->delete();

        return $this->httpResponse()->setMessage(__('Review deleted successfully!'));
    }

    public function sendKeyInstructions(Request $request, Booking $booking): \Illuminate\Http\RedirectResponse
    {
        abort_if($booking->vendor_id != auth('customer')->id(), 403);

        $request->validate([
            'key_instructions' => 'required|string|min:10',
        ]);

        $booking->update([
            'key_instructions' => $request->key_instructions,
            'key_instructions_sent_at' => now(),
        ]);

        \Illuminate\Support\Facades\Mail::send(
            'plugins/car-rentals::emails.key-instructions',
            ['booking' => $booking, 'instructions' => $request->key_instructions],
            function ($mail) use ($booking) {
                $mail->to($booking->customer_email, $booking->customer_name)
                    ->subject('Your Car Pickup Instructions - Booking #' . $booking->booking_number);
            }
        );

        return redirect()->back()->with('success_msg', 'Key instructions sent to ' . $booking->customer_email . ' successfully!');
    }
}
