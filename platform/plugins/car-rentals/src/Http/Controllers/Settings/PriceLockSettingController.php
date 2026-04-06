<?php

namespace Botble\CarRentals\Http\Controllers\Settings;

use Botble\CarRentals\Forms\Settings\PriceLockSettingForm;
use Botble\CarRentals\Http\Requests\Settings\PriceLockSettingRequest;

class PriceLockSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/car-rentals::settings.price_lock.title'));

        return PriceLockSettingForm::create()->renderForm();
    }

    public function update(PriceLockSettingRequest $request)
    {
        return $this->performUpdate($request->validated());
    }
}
