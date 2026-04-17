<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;

class VendorQualityScore extends BaseModel
{
    protected $table = 'cr_vendor_quality_scores';

    protected $fillable = [
        'vendor_id',
        'rating_score',
        'completion_rate',
        'cancellation_score',
        'response_score',
        'acceptance_rate',
        'total_score',
        'badge_tier',
        'badge_override',
        'override_badge',
        'total_bookings',
        'completed_bookings',
        'cancelled_bookings',
        'avg_rating',
        'avg_response_hours',
        'last_calculated_at',
    ];

    protected $casts = [
        'badge_override'     => 'boolean',
        'last_calculated_at' => 'datetime',
    ];

    public function vendor()
    {
        return $this->belongsTo(Customer::class, 'vendor_id');
    }

    public function getEffectiveBadgeAttribute(): ?string
    {
        return $this->badge_override ? $this->override_badge : $this->badge_tier;
    }

    public function getBadgeLabelAttribute(): string
    {
        return match($this->effective_badge) {
            'all_star'    => 'All-Star Host',
            'top_host'    => 'Top Host',
            'rising_star' => 'Rising Star',
            default       => '',
        };
    }

    public function getBadgeColorAttribute(): string
    {
        return match($this->effective_badge) {
            'all_star'    => 'success',
            'top_host'    => 'primary',
            'rising_star' => 'warning',
            default       => 'secondary',
        };
    }
}