<?php

namespace Botble\CarRentals\Http\Controllers\Settings;

use Botble\CarRentals\Facades\Currency;
use Botble\CarRentals\Forms\Settings\CurrencySettingForm;
use Botble\CarRentals\Http\Requests\Settings\CurrencySettingRequest;
use Botble\CarRentals\Services\StoreCurrenciesService;
use Illuminate\Support\Facades\Cache;

class CurrencySettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/car-rentals::currency.currencies'));

        $form = CurrencySettingForm::create();

        return view('plugins/car-rentals::settings.currency', compact('form'));
    }

    public function update(CurrencySettingRequest $request, StoreCurrenciesService $service)
    {
        $this->saveSettings($request->except([
            'currencies',
            'currencies_data',
            'deleted_currencies',
        ]));

        $currencies = json_decode($request->validated('currencies'), true) ?: [];

        if (! $currencies) {
            return $this
                ->httpResponse()
                ->setNextUrl(route('car-rentals.settings.currencies'))
                ->setError()
                ->setMessage(trans('plugins/car-rentals::currency.require_at_least_one_currency'));
        }

        $deletedCurrencies = json_decode($request->input('deleted_currencies', []), true) ?: [];

        $service->execute($currencies, $deletedCurrencies);

        return $this
            ->httpResponse()
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function updateCurrenciesFromExchangeApi()
    {
        try {
            $service = app(Currency::class)->getExchangeRateService();
            $service->getCurrentExchangeRate();

            return $this
                ->httpResponse()
                ->setMessage(trans('plugins/car-rentals::currency.update_currency_rates_success'));
        } catch (\Exception $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($e->getMessage());
        }
    }

    public function clearCacheCurrencyRates()
    {
        Cache::forget('car_rentals_currency_exchange_rate');

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/car-rentals::currency.clear_cache_rates_success'));
    }
}
