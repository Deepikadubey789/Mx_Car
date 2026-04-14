<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;
use Botble\Base\Enums\BaseStatusEnum;

class GuestProtectionPlan extends BaseModel
{
    protected $table = 'cr_guest_protection_plans';

    protected $fillable = [
        'name',
        'description',
        'daily_fee',
        'deductible_amount',
        'liability_limit',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];
}