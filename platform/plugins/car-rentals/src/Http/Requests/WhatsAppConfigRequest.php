<?php

namespace Botble\CarRentals\Http\Requests;

use Botble\Support\Http\Requests\Request;

class WhatsAppConfigRequest extends Request
{
    public function rules(): array
    {
        return [
            'phone_number_id' => 'required|string|min:10|max:50',
            'business_account_id' => 'nullable|string|max:50',
            'api_access_token' => 'nullable|string|min:10',
            'webhook_verify_token' => 'required|string|min:10|max:255',
            'api_version' => 'required|string|min:3|max:30',
            'enabled' => 'nullable|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'phone_number_id' => trans('car_rentals::forms.whatsapp_phone_number_id'),
            'business_account_id' => trans('car_rentals::forms.whatsapp_business_account_id'),
            'api_access_token' => trans('car_rentals::forms.whatsapp_api_access_token'),
            'webhook_verify_token' => trans('car_rentals::forms.whatsapp_webhook_verify_token'),
            'api_version' => trans('car_rentals::forms.whatsapp_api_version'),
            'enabled' => trans('car_rentals::forms.whatsapp_enabled'),
        ];
    }
}
