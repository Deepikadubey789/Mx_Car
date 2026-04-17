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
        $this
            ->setupModel(new WhatsAppConfig)
            ->setValidatorClass(WhatsAppConfigRequest::class)
            ->withCustomFields()
            ->add('enabled', 'checkbox', [
                'label' => trans('car_rentals::forms.whatsapp_enabled'),
                'value' => '1',
                'attr' => [
                    'class' => 'form-check-input',
                ],
                'help_block' => trans('car_rentals::forms.whatsapp_enabled_help'),
            ])
            ->add('phone_number_id', 'text', [
                'label' => trans('car_rentals::forms.whatsapp_phone_number_id'),
                'placeholder' => '1234567890123456',
                'attr' => [
                    'class' => 'form-control',
                    'required' => 'required',
                ],
                'help_block' => trans('car_rentals::forms.whatsapp_phone_number_id_help'),
            ])
            ->add('business_account_id', 'text', [
                'label' => trans('car_rentals::forms.whatsapp_business_account_id'),
                'placeholder' => 'WABA_ACCOUNT_ID',
                'attr' => [
                    'class' => 'form-control',
                ],
                'help_block' => trans('car_rentals::forms.whatsapp_business_account_id_help'),
            ])
            ->add('api_access_token', 'password', [
                'label' => trans('car_rentals::forms.whatsapp_api_access_token'),
                'placeholder' => 'Bearer token from Meta Business Account',
                'attr' => [
                    'class' => 'form-control',
                ],
                'help_block' => trans('car_rentals::forms.whatsapp_api_access_token_help'),
            ])
            ->add('webhook_verify_token', 'text', [
                'label' => trans('car_rentals::forms.whatsapp_webhook_verify_token'),
                'attr' => [
                    'class' => 'form-control',
                ],
                'help_block' => trans('car_rentals::forms.whatsapp_webhook_verify_token_help'),
            ])
            ->add('api_version', 'text', [
                'label' => trans('car_rentals::forms.whatsapp_api_version'),
                'value' => 'v18.0',
                'attr' => [
                    'class' => 'form-control',
                ],
                'help_block' => trans('car_rentals::forms.whatsapp_api_version_help'),
            ]);
    }

    public function getFormOptions()
    {
        return parent::getFormOptions();
    }
}
