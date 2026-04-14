<?php

namespace Botble\CarRentals\Forms\Settings;

use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\MultiChecklistFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\MultiCheckListField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Http\Requests\Settings\KycSettingRequest;
use Botble\CarRentals\Models\CarCategory;
use Botble\Setting\Forms\SettingForm;

class KycSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();
        $restrictedCategoryIds = $this->parseRestrictedCategoryIds(
            CarRentalsHelper::getSetting('eligibility_restricted_category_ids', [])
        );
        $categories = CarCategory::query()
            ->wherePublished()
            ->oldest('name')
            ->pluck('name', 'id')
            ->all();

        $this
            ->setSectionTitle(trans('plugins/car-rentals::settings.kyc.title'))
            ->setSectionDescription(trans('plugins/car-rentals::settings.kyc.description'))
            ->setFormOptions([
                'class' => 'main-setting-form',
            ])
            ->setValidatorClass(KycSettingRequest::class)
            ->add(
                'deposit_risk_kyc_pending_multiplier',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.kyc.forms.deposit_risk_kyc_pending_multiplier'))
                    ->helperText(trans('plugins/car-rentals::settings.kyc.forms.deposit_risk_kyc_pending_multiplier_helper'))
                    ->value(CarRentalsHelper::getSetting('deposit_risk_kyc_pending_multiplier', 1.1))
                    ->min(0.1)
                    ->step(0.01)
            )
            ->add(
                'kyc_face_match_threshold',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.kyc.forms.kyc_face_match_threshold'))
                    ->helperText(trans('plugins/car-rentals::settings.kyc.forms.kyc_face_match_threshold_helper'))
                    ->value(CarRentalsHelper::getSetting('kyc_face_match_threshold', 0.8))
                    ->min(0)
                    ->max(1)
                    ->step(0.01)
            )
            ->add(
                'kyc_ocr_confidence_threshold',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.kyc.forms.kyc_ocr_confidence_threshold'))
                    ->helperText(trans('plugins/car-rentals::settings.kyc.forms.kyc_ocr_confidence_threshold_helper'))
                    ->value(CarRentalsHelper::getSetting('kyc_ocr_confidence_threshold', 0.8))
                    ->min(0)
                    ->max(1)
                    ->step(0.01)
            )
            ->add(
                'kyc_manual_review_risk_threshold',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.kyc.forms.kyc_manual_review_risk_threshold'))
                    ->helperText(trans('plugins/car-rentals::settings.kyc.forms.kyc_manual_review_risk_threshold_helper'))
                    ->value(CarRentalsHelper::getSetting('kyc_manual_review_risk_threshold', 0.5))
                    ->min(0)
                    ->max(2)
                    ->step(0.01)
            )
            ->add(
                'eligibility_restricted_category_ids[]',
                MultiCheckListField::class,
                MultiChecklistFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.kyc.forms.eligibility_restricted_category_ids'))
                    ->helperText(trans('plugins/car-rentals::settings.kyc.forms.eligibility_restricted_category_ids_helper'))
                    ->choices($categories)
                    ->selected($restrictedCategoryIds)
            )
            ->add(
                'kyc_provider',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.kyc.forms.kyc_provider'))
                    ->helperText(trans('plugins/car-rentals::settings.kyc.forms.kyc_provider_helper'))
                    ->choices([
                        'stripe' => trans('plugins/car-rentals::settings.kyc.forms.kyc_provider_stripe'),
                        'mock' => trans('plugins/car-rentals::settings.kyc.forms.kyc_provider_mock'),
                    ])
                    ->selected((string) CarRentalsHelper::getSetting('kyc_provider', 'stripe'))
            )
            ->add(
                'kyc_stripe_enabled',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.kyc.forms.kyc_stripe_enabled'))
                    ->helperText(trans('plugins/car-rentals::settings.kyc.forms.kyc_stripe_enabled_helper'))
                    ->value((bool) CarRentalsHelper::getSetting('kyc_stripe_enabled', true))
            )
            ->add(
                'kyc_stripe_secret_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.kyc.forms.kyc_stripe_secret_key'))
                    ->helperText(trans('plugins/car-rentals::settings.kyc.forms.kyc_stripe_secret_key_helper'))
                    ->value((string) CarRentalsHelper::getSetting('kyc_stripe_secret_key', ''))
                    ->maxLength(255)
            )
            ->add(
                'kyc_stripe_publishable_key',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.kyc.forms.kyc_stripe_publishable_key'))
                    ->helperText(trans('plugins/car-rentals::settings.kyc.forms.kyc_stripe_publishable_key_helper'))
                    ->value((string) CarRentalsHelper::getSetting('kyc_stripe_publishable_key', ''))
                    ->maxLength(255)
            )
            ->add(
                'kyc_stripe_webhook_secret',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.kyc.forms.kyc_stripe_webhook_secret'))
                    ->helperText(trans('plugins/car-rentals::settings.kyc.forms.kyc_stripe_webhook_secret_helper'))
                    ->value((string) CarRentalsHelper::getSetting('kyc_stripe_webhook_secret', ''))
                    ->maxLength(255)
            )
            ->add(
                'fallback_to_mock_on_provider_error',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.kyc.forms.fallback_to_mock_on_provider_error'))
                    ->helperText(trans('plugins/car-rentals::settings.kyc.forms.fallback_to_mock_on_provider_error_helper'))
                    ->value((bool) CarRentalsHelper::getSetting('fallback_to_mock_on_provider_error', true))
            )
            ->add(
                'kyc_allow_non_biometric_fallback',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.kyc.forms.kyc_allow_non_biometric_fallback'))
                    ->helperText(trans('plugins/car-rentals::settings.kyc.forms.kyc_allow_non_biometric_fallback_helper'))
                    ->value((bool) CarRentalsHelper::getSetting('kyc_allow_non_biometric_fallback', false))
            )
            ->add(
                'kyc_payload_retention_days',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.kyc.forms.kyc_payload_retention_days'))
                    ->helperText(trans('plugins/car-rentals::settings.kyc.forms.kyc_payload_retention_days_helper'))
                    ->value((int) CarRentalsHelper::getSetting('kyc_payload_retention_days', 90))
                    ->min(1)
                    ->step(1)
            );
    }

    protected function parseRestrictedCategoryIds(mixed $value): array
    {
        if (is_array($value)) {
            return array_values(array_unique(array_map('intval', array_filter($value, 'is_numeric'))));
        }

        if (! is_string($value) || $value === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        if (! is_array($decoded)) {
            return [];
        }

        return array_values(array_unique(array_map('intval', array_filter($decoded, 'is_numeric'))));
    }
}
