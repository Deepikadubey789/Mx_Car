<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class TripMessage extends BaseModel
{
    protected $table = 'cr_trip_messages';

    protected $fillable = [
        'booking_id',
        'sender_id',
        'sender_type',
        'message',
        'type',
    ];

    protected $casts = [
        'message' => SafeContent::class,
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function sender(): MorphTo
    {
        return $this->morphTo();
    }
}
