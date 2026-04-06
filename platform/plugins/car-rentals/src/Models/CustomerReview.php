<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerReview extends BaseModel
{
    protected $table = 'cr_customer_reviews';

    protected $fillable = [
        'vendor_id',
        'customer_id',
        'booking_id',
        'star',
        'content',
        'status',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'vendor_id');
    }
}