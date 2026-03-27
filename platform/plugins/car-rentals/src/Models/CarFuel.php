<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\CarRentals\Models\Concerns\HasActiveCarsRelation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarFuel extends BaseModel
{
    use HasActiveCarsRelation;

    protected $table = 'cr_car_fuels';

    protected $fillable = [
        'name',
        'icon',
        'status',
        'order',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class, 'id', 'fuel_type_id');
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class, 'fuel_type_id', 'id');
    }
}
