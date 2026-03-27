<?php

namespace Botble\CarRentals\Facades;

use Botble\CarRentals\Supports\CurrencySupport;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void setApplicationCurrency(\Botble\CarRentals\Models\Currency $currency)
 * @method static \Botble\CarRentals\Models\Currency|null getApplicationCurrency()
 * @method static \Botble\CarRentals\Models\Currency|null getDefaultCurrency()
 * @method static \Illuminate\Support\Collection currencies()
 * @method static string|null detectedCurrencyCode()
 * @method static array countryCurrencies()
 * @method static array currencyCodes()
 * @method static \Botble\CarRentals\Models\Currency setCurrencyExchangeRate(\Botble\CarRentals\Models\Currency $currency)
 * @method static \Botble\CarRentals\Services\ExchangeRates\ExchangeRateInterface getExchangeRateService()
 * @method static float convertFromDefaultCurrency(float $amount, \Botble\CarRentals\Models\Currency $currency)
 * @method static float convertToDefaultCurrency(float $amount, \Botble\CarRentals\Models\Currency $currency)
 * @method static string formatPrice(float $amount, ?\Botble\CarRentals\Models\Currency $currency = null, bool $withCurrencySymbol = true, bool $useDefaultCurrency = false)
 *
 * @see \Botble\CarRentals\Supports\CurrencySupport
 */
class Currency extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CurrencySupport::class;
    }
}
