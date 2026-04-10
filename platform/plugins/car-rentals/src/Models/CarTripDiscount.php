<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarTripDiscount extends BaseModel
{
    protected $table = 'cr_car_trip_discounts';

    protected $fillable = [
        'car_id',
        'car_pricing_policy_id',
        'min_days',
        'max_days',
        'discount_type',
        'discount_value',
        'priority',
        'active',
        'description',
    ];

    protected $casts = [
        'min_days' => 'integer',
        'max_days' => 'integer',
        'discount_value' => 'float',
        'priority' => 'integer',
        'active' => 'boolean',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function pricingPolicy(): BelongsTo
    {
        return $this->belongsTo(CarPricingPolicy::class, 'car_pricing_policy_id');
    }
}