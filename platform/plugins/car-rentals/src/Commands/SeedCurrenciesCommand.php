<?php

namespace Botble\CarRentals\Commands;

use Botble\CarRentals\Models\Currency;
use Illuminate\Console\Command;

class SeedCurrenciesCommand extends Command
{
    protected $signature = 'car-rentals:seed-currencies';

    protected $description = 'Seed default currencies';

    public function handle(): int
    {
        $this->components->info('Seeding default currencies...');

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

        Currency::query()->truncate();

        foreach ($currencies as $currency) {
            Currency::query()->create($currency);
        }

        $this->components->info('Currencies seeded successfully.');

        return self::SUCCESS;
    }
}
