<?php

namespace Botble\CarRentals\Models;

use Botble\ACL\Models\User;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingSupportAction extends BaseModel
{
    protected $table = 'cr_booking_support_actions';

    protected $fillable = [
        'booking_id',
        'admin_id',
        'action',
        'note',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
