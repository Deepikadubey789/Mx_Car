<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Insurance extends BaseModel
{
    protected $table = 'cr_insurances';

    protected $fillable = [
        'name',
        'description',
        'price',
        'vendor_id',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'vendor_id');
    }
}