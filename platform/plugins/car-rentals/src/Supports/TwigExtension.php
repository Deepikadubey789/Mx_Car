<?php

namespace Botble\CarRentals\Supports;

use Twig\Extension\AbstractExtension;
use Twig\Extension\ExtensionInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TwigExtension extends AbstractExtension implements ExtensionInterface
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('price_format', 'format_price'),
            new TwigFilter('urlencode', 'urlencode'),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_car_rentals_setting', 'get_car_rentals_setting'),
        ];
    }
}
