<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerKycDocument extends BaseModel
{
    protected $table = 'cr_customer_kyc_documents';

    protected $fillable = [
        'verification_id',
        'customer_id',
        'document_type',
        'file_path',
        'checksum',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function verification(): BelongsTo
    {
        return $this->belongsTo(CustomerKycVerification::class, 'verification_id');
    }
}
