<?php

namespace Botble\CarRentals\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\CarRentals\Events\NewTripMessage;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\TripMessage;
use Illuminate\Http\Request;

class TripMessageController extends BaseController
{
    public function index(Booking $booking, BaseHttpResponse $response)
    {
        $messages = $booking->tripMessages()->with('sender')->oldest('id')->get();
        return $response->setData([
            'html' => view('plugins/car-rentals::partials.trip-messaging-list', compact('messages', 'booking'))->render()
        ]);
    }

    public function store(Booking $booking, Request $request, BaseHttpResponse $response)
    {
        $request->validate(['message' => 'required|string']);

        $message = new TripMessage();
        $message->booking_id = $booking->id;
        $message->sender_id = auth()->id();
        $message->sender_type = get_class(auth()->user());
        $message->message = $request->input('message');
        $message->type = 'user_message';
        $message->save();

        event(new NewTripMessage($message, ''));

        return $this->index($booking, $response);
    }

    public function deescalate(Booking $booking, BaseHttpResponse $response)
    {
        $booking->is_escalated = false;
        $booking->save();

        $message = new TripMessage();
        $message->booking_id = $booking->id;
        $message->sender_id = auth()->id();
        $message->sender_type = get_class(auth()->user());
        $message->message = 'Support has resolved the escalation.';
        $message->type = 'system_event';
        $message->save();

        event(new NewTripMessage($message, ''));

        return $this->index($booking, $response)->setMessage('Escalation resolved.');
    }
}
