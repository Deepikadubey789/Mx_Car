<?php

namespace Botble\CarRentals\Commands;

use Botble\CarRentals\Facades\Currency;
use Illuminate\Console\Command;
use Throwable;

class UpdateExchangeRatesCommand extends Command
{
    protected $signature = 'car-rentals:update-exchange-rates';

    protected $description = 'Update currency exchange rates from API provider';

    public function handle(): int
    {
        if (! get_car_rentals_setting('use_exchange_rate_from_api')) {
            $this->error('Exchange rate API is not enabled. Please enable it in settings.');

            return self::FAILURE;
        }

        try {
            $this->info('Updating exchange rates...');

            $service = Currency::getExchangeRateService();
            $currencies = $service->getCurrentExchangeRate();

            $this->info('Exchange rates updated successfully.');
            $this->table(
                ['Currency', 'Exchange Rate'],
                $currencies->map(fn ($currency) => [$currency->title, $currency->exchange_rate])
            );

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->error('Failed to update exchange rates: ' . $e->getMessage());

            return self::FAILURE;
        }
    }
}
