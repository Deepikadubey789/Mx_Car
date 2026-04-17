<?php

namespace Botble\CarRentals\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FormAbstract;
use Botble\CarRentals\Models\WhatsAppConfig;
use Botble\CarRentals\Http\Requests\WhatsAppConfigRequest;

class WhatsAppConfigForm extends FormAbstract
{
    public function setup(): void
    {
        $config = WhatsAppConfig::first() ?? new WhatsAppConfig();
        
        $this
            ->setupModel($config)
            ->setValidatorClass(WhatsAppConfigRequest::class)
            ->withCustomFields()
            ->add('enabled', 'checkbox', [
                'label' => 'Enable WhatsApp Integration',
                'value' => '1',
                'checked' => $config->enabled ?? false,
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ])
            ->add('phone_number_id', 'text', [
                'label' => 'WhatsApp Phone Number ID',
                'placeholder' => '1234567890123456',
                'attr' => [
                    'class' => 'form-control',
                    'required' => 'required',
                ],
            ])
            ->add('business_account_id', 'text', [
                'label' => 'Business Account ID',
                'placeholder' => 'WABA_ACCOUNT_ID',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('api_access_token', 'password', [
                'label' => 'API Access Token',
                'placeholder' => 'Bearer token from Meta Business Account',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('webhook_verify_token', 'text', [
                'label' => 'Webhook Verify Token',
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('api_version', 'text', [
                'label' => 'API Version',
                'value' => $config->api_version ?: 'v18.0',
                'attr' => [
                    'class' => 'form-control',
                ],
            ]);
    }

    public function getFormOptions(): array
    {
        return array_merge(parent::getFormOptions(), [
            'method' => 'POST',
            'url' => route('car-rentals.settings.whatsapp.update'),
            'form_attrs' => [
                'enctype' => 'multipart/form-data',
            ],
        ]);
    }
}
