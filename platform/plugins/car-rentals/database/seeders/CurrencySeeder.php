<?php

namespace Botble\CarRentals\Database\Seeders;

use Botble\CarRentals\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    public function run(): void
    {
        Currency::query()->truncate();

        $currencies = [
            [
                'title' => 'USD',
                'symbol' => '$',
                'is_prefix_symbol' => true,
                'decimals' => 2,
                'order' => 0,
                'is_default' => true,
                'exchange_rate' => 1,
            ],
            [
                'title' => 'EUR',
                'symbol' => '€',
                'is_prefix_symbol' => true,
                'decimals' => 2,
                'order' => 1,
                'is_default' => false,
                'exchange_rate' => 0.84,
            ],
            [
                'title' => 'VND',
                'symbol' => '₫',
                'is_prefix_symbol' => false,
                'decimals' => 0,
                'order' => 2,
                'is_default' => false,
                'exchange_rate' => 23000,
            ],
            [
                'title' => 'NGN',
                'symbol' => '₦',
                'is_prefix_symbol' => true,
                'decimals' => 2,
                'order' => 3,
                'is_default' => false,
                'exchange_rate' => 410.5,
            ],
        ];

        foreach ($currencies as $currency) {
            Currency::query()->create($currency);
        }
    }
}
