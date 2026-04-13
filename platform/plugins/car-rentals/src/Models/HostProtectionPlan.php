<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;
use Botble\Base\Enums\BaseStatusEnum;

class HostProtectionPlan extends BaseModel
{
    protected $table = 'cr_host_protection_plans';

    protected $fillable = [
        'name',
        'description',
        'revenue_share_percentage',
        'deductible_amount',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];
}