<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarPricingPolicy extends BaseModel
{
    protected $table = 'cr_car_pricing_policies';

    protected $fillable = [
        'car_id',
        'weekly_discount_type',
        'weekly_discount_value',
        'monthly_discount_type',
        'monthly_discount_value',
        'included_distance_per_day',
        'included_distance_per_trip',
        'extra_distance_unit_price',
        'distance_unit',
        'distance_overage_billing_mode',
        'allow_best_discount_only',
        'max_discount_cap_percent',
        'active',
        'demand_recommendations_enabled',
        'demand_min_price',
        'demand_max_price',
        'demand_max_daily_change_percent',
        'demand_last_generated_at',
        'demand_auto_apply_enabled',
        'demand_auto_apply_min_confidence',
        'demand_auto_apply_max_daily_change_percent',
        'demand_auto_apply_paused_until',
    ];

    protected $casts = [
        'weekly_discount_value' => 'float',
        'monthly_discount_value' => 'float',
        'included_distance_per_day' => 'integer',
        'included_distance_per_trip' => 'integer',
        'extra_distance_unit_price' => 'float',
        'allow_best_discount_only' => 'boolean',
        'max_discount_cap_percent' => 'float',
        'active' => 'boolean',
        'demand_recommendations_enabled' => 'boolean',
        'demand_min_price' => 'float',
        'demand_max_price' => 'float',
        'demand_max_daily_change_percent' => 'float',
        'demand_last_generated_at' => 'datetime',
        'demand_auto_apply_enabled' => 'boolean',
        'demand_auto_apply_min_confidence' => 'float',
        'demand_auto_apply_max_daily_change_percent' => 'float',
        'demand_auto_apply_paused_until' => 'datetime',
        'demand_auto_apply_last_applied_at' => 'datetime',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function tripDiscounts(): HasMany
    {
        return $this->hasMany(CarTripDiscount::class, 'car_pricing_policy_id');
    }
}