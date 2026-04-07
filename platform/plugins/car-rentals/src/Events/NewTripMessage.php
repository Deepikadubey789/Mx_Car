<?php

namespace Botble\CarRentals\Events;

use Botble\CarRentals\Models\TripMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewTripMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $html;

    public function __construct(TripMessage $message, string $html)
    {
        $this->message = $message;
        $this->html = $html;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('trip-messaging.' . $this->message->booking_id);
    }

    public function broadcastAs()
    {
        return 'NewTripMessage';
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'booking_id' => $this->message->booking_id,
            'html' => $this->html,
        ];
    }
}
