<?php

namespace Botble\CarRentals\Http\Controllers\Vendor;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\CarRentals\Events\NewTripMessage;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\TripMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TripMessageController extends BaseController
{
    public function index(Booking $booking, BaseHttpResponse $response)
    {
        if ($booking->vendor_id != auth('customer')->id()) {
            abort(403);
        }

        $messages = $booking->tripMessages()->with('sender')->oldest('id')->get();
        return $response->setData([
            'html' => view('plugins/car-rentals::partials.trip-messaging-list', compact('messages', 'booking'))->render()
        ]);
    }

    public function store(Booking $booking, Request $request, BaseHttpResponse $response)
    {
        if ($booking->vendor_id != auth('customer')->id()) {
            abort(403);
        }

        $request->validate(['message' => 'required|string']);

        $message = new TripMessage();
        $message->booking_id = $booking->id;
        $message->sender_id = auth('customer')->id();
        $message->sender_type = get_class(auth('customer')->user());
        $message->message = $request->input('message');
        $message->type = 'user_message';
        $message->save();

        event(new NewTripMessage($message, ''));

        return $this->index($booking, $response);
    }

    public function escalate(Booking $booking, BaseHttpResponse $response)
    {
        if ($booking->vendor_id != auth('customer')->id()) {
            abort(403);
        }

        $booking->is_escalated = true;
        $booking->save();

        $message = new TripMessage();
        $message->booking_id = $booking->id;
        $message->sender_id = auth('customer')->id();
        $message->sender_type = get_class(auth('customer')->user());
        $message->message = 'Trip escalated to Support Team by Host.';
        $message->type = 'escalation';
        $message->save();

        DB::table('admin_notifications')->insert([
            'title' => 'Trip Escalated #' . $booking->booking_number,
            'description' => ($booking->vendor?->name ?: 'Host') . ' escalated booking #' . $booking->booking_number,
            'action_label' => 'View Booking',
            'action_url' => route('car-rentals.bookings.edit', $booking->id),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        event(new NewTripMessage($message, ''));

        return $this->index($booking, $response)->setMessage('Trip escalated to support.');
    }
}
