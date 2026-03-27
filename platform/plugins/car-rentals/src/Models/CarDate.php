<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Models\BaseModel;
use Botble\CarRentals\Enums\CarDateValueTypeEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CarDate extends BaseModel
{
    protected $table = 'cr_car_dates';

    protected $fillable = [
        'car_id',
        'start_date',
        'end_date',
        'value',
        'value_type',
        'active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'value' => 'float',
        'value_type' => CarDateValueTypeEnum::class,
        'active' => 'boolean',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'car_id');
    }
}
