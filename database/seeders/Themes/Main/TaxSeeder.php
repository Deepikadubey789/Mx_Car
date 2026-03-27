<?php

namespace Database\Seeders\Themes\Main;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Supports\BaseSeeder;
use Botble\CarRentals\Models\Tax;

class TaxSeeder extends BaseSeeder
{
    public function run(): void
    {
        Tax::query()->truncate();

        $taxes = [
            'Import Duty',
            'Value Added Tax (VAT)',
            'Currency Conversion',
            'Brokerage',
            'Storage',
            'Administrative',
            'Handling',
            'Insurance',
            'Rural Delivery',
            'Return Shipping',
            'Environmental',
            'Excise',
        ];

        foreach ($taxes as $index => $taxName) {
            Tax::query()->create([
                'name' => $taxName,
                'percentage' => round(rand(10, 500) / 100, 2),
                'status' => BaseStatusEnum::PUBLISHED,
                'priority' => $index + 1,
            ]);
        }
    }
}
