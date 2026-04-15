<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemandPricingRecommendation extends BaseModel
{
    protected $table = 'cr_demand_pricing_recommendations';

    protected $fillable = [
        'car_id',
        'recommendation_date',
        'recommended_value',
        'value_type',
        'demand_score',
        'confidence_score',
        'reason_codes',
        'status',
        'applied_by',
        'generated_at',
        'expires_at',
        'applied_at',
    ];

    protected $casts = [
        'recommendation_date' => 'date',
        'recommended_value' => 'float',
        'demand_score' => 'float',
        'confidence_score' => 'float',
        'reason_codes' => 'array',
        'applied_by' => 'integer',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
        'applied_at' => 'datetime',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeForCar(Builder $query, int $carId): Builder
    {
        return $query->where('car_id', $carId);
    }
}
