<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\CarRentals\Models\Concerns\HasActiveCarsRelation;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarMake extends BaseModel
{
    use HasActiveCarsRelation;

    protected $table = 'cr_car_makes';

    protected $fillable = [
        'name',
        'logo',
        'status',
        'order',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class, 'make_id', 'id');
    }
}
