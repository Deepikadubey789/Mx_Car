<?php

namespace Botble\CarRentals\Http\Controllers\Admin;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\CarRentals\Forms\WhatsAppConfigForm;
use Botble\CarRentals\Http\Requests\WhatsAppConfigRequest;
use Botble\CarRentals\Models\WhatsAppConfig;

class WhatsAppConfigController extends BaseController
{
    public function edit()
    {
        $this->pageTitle('WhatsApp Configuration');

        return WhatsAppConfigForm::create()->renderForm();
    }

    public function update(WhatsAppConfigRequest $request): BaseHttpResponse
    {
        $validated = $request->validated();
        
        // Get existing config or create new one
        $config = WhatsAppConfig::first() ?? new WhatsAppConfig();
        
        // Update with validated data
        $config->fill($validated);
        $config->save();

        return $this->httpResponse()
            ->setPreviousUrl(route('car-rentals.settings.whatsapp'))
            ->setMessage('WhatsApp configuration updated successfully!');
    }
}
