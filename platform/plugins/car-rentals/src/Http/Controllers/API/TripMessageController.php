<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Events\NewTripMessage;
use Botble\CarRentals\Models\Booking;
use Botble\CarRentals\Models\TripMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TripMessageController extends BaseApiController
{
    private function findBooking($id, Request $request): ?Booking
    {
        $customer = Auth::guard('sanctum')->user();
        $query = Booking::query();

        if ($customer) {
            $query->where('customer_id', $customer->id)
                  ->where(function ($q) use ($id) {
                      $q->where('id', $id)
                        ->orWhere('transaction_id', $id)
                        ->orWhere('code', $id);
                  });
        } else {
            return null; // Guest cannot access messages securely
        }

        return $query->first();
    }

    public function index($bookingId, Request $request)
    {
        $booking = $this->findBooking($bookingId, $request);
        if (!$booking) {
            return $this->httpResponse()->setError()->setCode(404)->setMessage('Booking not found')->toApiResponse();
        }

        $messages = $booking->tripMessages()->with('sender')->oldest('id')->get();
        
        $formattedMessages = $messages->map(function ($msg) {
            return [
                'id' => $msg->id,
                'message' => $msg->message,
                'type' => $msg->type,
                'sender_id' => $msg->sender_id,
                'sender_type' => $msg->sender_type,
                'sender_name' => $msg->sender ? $msg->sender->name : 'System',
                'sender_avatar' => $msg->sender && method_exists($msg->sender, 'avatar_url') ? $msg->sender->avatar_url : null,
                'created_at' => $msg->created_at,
                'is_mine' => $msg->sender_id == auth('sanctum')->id() && str_contains($msg->sender_type, 'Customer'),
            ];
        });

        return $this->httpResponse()->setData($formattedMessages)->toApiResponse();
    }

    public function store($bookingId, Request $request)
    {
        $booking = $this->findBooking($bookingId, $request);
        if (!$booking) {
            return $this->httpResponse()->setError()->setCode(404)->setMessage('Booking not found')->toApiResponse();
        }

        $request->validate(['message' => 'required|string']);

        $message = new TripMessage();
        $message->booking_id = $booking->id;
        $message->sender_id = auth('sanctum')->id();
        $message->sender_type = get_class(auth('sanctum')->user());
        $message->message = $request->input('message');
        $message->type = 'user_message';
        $message->save();

        event(new NewTripMessage($message, ''));

        return $this->index($bookingId, $request);
    }

    public function escalate($bookingId, Request $request)
    {
        $booking = $this->findBooking($bookingId, $request);
        if (!$booking) {
            return $this->httpResponse()->setError()->setCode(404)->setMessage('Booking not found')->toApiResponse();
        }

        if ($booking->is_escalated) {
            return $this->httpResponse()->setError()->setMessage('Trip is already escalated.')->toApiResponse();
        }

        $booking->is_escalated = true;
        $booking->save();

        $message = new TripMessage();
        $message->booking_id = $booking->id;
        $message->sender_id = auth('sanctum')->id();
        $message->sender_type = get_class(auth('sanctum')->user());
        $message->message = 'Trip escalated to Support Team by Renter.';
        $message->type = 'escalation';
        $message->save();

        DB::table('admin_notifications')->insert([
            'title' => 'Trip Escalated #' . $booking->booking_number,
            'description' => ($booking->customer_name ?: 'Renter') . ' escalated booking #' . $booking->booking_number,
            'action_label' => 'View Booking',
            'action_url' => route('car-rentals.bookings.edit', $booking->id),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        event(new NewTripMessage($message, ''));

        return $this->index($bookingId, $request)->setMessage('Trip escalated to support.');
    }
}
