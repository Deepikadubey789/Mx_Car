<?php

namespace Botble\CarRentals\Enums;

use Botble\Base\Supports\Enum;

class CarDateValueTypeEnum extends Enum
{
    public const FIXED = 'fixed';

    public const AMOUNT_ADJUST = 'amount_adjust';

    public const PERCENTAGE_ADJUST = 'percentage_adjust';

    public static $langPath = 'plugins/car-rentals::car-rentals.pricing_calendar.value_types';

    public function toHtml(): string
    {
        return match ($this->value) {
            self::FIXED => '<span class="badge bg-blue">' . self::FIXED()->label() . '</span>',
            self::AMOUNT_ADJUST => '<span class="badge bg-green">' . self::AMOUNT_ADJUST()->label() . '</span>',
            self::PERCENTAGE_ADJUST => '<span class="badge bg-orange">' . self::PERCENTAGE_ADJUST()->label() . '</span>',
            default => parent::toHtml(),
        };
    }
}
