<?php

namespace Botble\CarRentals\Http\Controllers\Settings;

use Botble\CarRentals\Forms\Settings\KycSettingForm;
use Botble\CarRentals\Http\Requests\Settings\KycSettingRequest;

class KycSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/car-rentals::settings.kyc.title'));

        return KycSettingForm::create()->renderForm();
    }

    public function update(KycSettingRequest $request)
    {
        return $this->performUpdate($request->validated());
    }
}
