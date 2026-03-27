<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CarAmenityCategory extends BaseModel
{
    protected $table = 'cr_car_amenity_categories';

    protected $fillable = [
        'name',
        'icon',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public function amenities(): HasMany
    {
        return $this->hasMany(CarAmenity::class, 'category_id');
    }

    public function activeAmenities(): HasMany
    {
        return $this->amenities()->where('status', BaseStatusEnum::PUBLISHED);
    }
}
