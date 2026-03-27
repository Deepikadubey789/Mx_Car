<?php

namespace Botble\CarRentals\Enums;

use Botble\Base\Facades\Html;
use Botble\Base\Supports\Enum;
use Illuminate\Support\HtmlString;

/**
 * @method static DistanceUnitEnum MILES()
 * @method static DistanceUnitEnum KILOMETERS()
 */
class DistanceUnitEnum extends Enum
{
    public const MILES = 'miles';

    public const KILOMETERS = 'kilometers';

    public static $langPath = 'plugins/car-rentals::enums.distance-units';

    public function toHtml(): HtmlString|string|null
    {
        $color = match ($this->value) {
            self::MILES => 'info',
            self::KILOMETERS => 'success',
            default => 'secondary',
        };

        return Html::tag('span', $this->label(), ['class' => 'label-' . $color])->toHtml();
    }
}
