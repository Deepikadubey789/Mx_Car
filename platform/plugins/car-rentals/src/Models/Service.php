<?php

namespace Botble\CarRentals\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\CarRentals\Enums\ServicePriceTypeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Service extends BaseModel
{
    protected $table = 'cr_services';

    protected $fillable = [
        'name',
        'description',
        'content',
        'price',
        'price_type',
        'logo',
        'image',
        'status',
        'currency_id',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'price_type' => ServicePriceTypeEnum::class,
    ];

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id')->withDefault();
    }

    protected function priceText(): Attribute
    {
        return Attribute::get(function () {
            return format_price($this->price, $this->currency_id);
        });
    }
}
