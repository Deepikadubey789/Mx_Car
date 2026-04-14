<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerKycVerification extends BaseModel
{
    protected $table = 'cr_customer_kyc_verifications';

    protected $fillable = [
        'customer_id',
        'status',
        'provider',
        'provider_reference',
        'ocr_confidence_score',
        'face_match_score',
        'risk_score',
        'license_valid',
        'license_expiry_date',
        'license_number',
        'ocr_payload',
        'provider_payload',
        'decision_reasons',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $casts = [
        'license_valid' => 'boolean',
        'license_expiry_date' => 'date',
        'ocr_payload' => 'array',
        'provider_payload' => 'array',
        'decision_reasons' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(CustomerKycDocument::class, 'verification_id');
    }
}
