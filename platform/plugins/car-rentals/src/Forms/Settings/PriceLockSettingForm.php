<?php

namespace Botble\CarRentals\Forms\Settings;

use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Http\Requests\Settings\PriceLockSettingRequest;
use Botble\Setting\Forms\SettingForm;

class PriceLockSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setSectionTitle(trans('plugins/car-rentals::settings.price_lock.title'))
            ->setSectionDescription(trans('plugins/car-rentals::settings.price_lock.description'))
            ->setFormOptions([
                'class' => 'main-setting-form',
            ])
            ->setValidatorClass(PriceLockSettingRequest::class)
            ->add(
                'price_lock_duration_minutes',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.price_lock_duration_minutes'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.price_lock_duration_minutes_helper'))
                    ->value(CarRentalsHelper::getSetting('price_lock_duration_minutes', 1))
                    ->min(1)
                    ->max(120)
            )
            ->add(
                'deposit_percentage',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.deposit_percentage'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.deposit_percentage_helper'))
                    ->value(CarRentalsHelper::getSetting('deposit_percentage', 20))
                    ->min(0)
                    ->max(100)
            )
            ->add(
                'deposit_type',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.deposit_type'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.deposit_type_helper'))
                    ->choices([
                        'percentage' => trans('plugins/car-rentals::settings.price_lock.forms.deposit_type_percentage'),
                        'fixed' => trans('plugins/car-rentals::settings.price_lock.forms.deposit_type_fixed'),
                    ])
                    ->selected(CarRentalsHelper::getSetting('deposit_type', 'percentage'))
            )
            ->add(
                'deposit_fixed_amount',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.deposit_fixed_amount'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.deposit_fixed_amount_helper'))
                    ->value(CarRentalsHelper::getSetting('deposit_fixed_amount', 0))
                    ->min(0)
            )
            ->add(
                'fee_name',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.fee_name'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.fee_name_helper'))
                    ->value(CarRentalsHelper::getSetting('fee_name', 'Service fee'))
                    ->maxLength(120)
            )
            ->add(
                'fee_value',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.fee_value'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.fee_value_helper'))
                    ->value(CarRentalsHelper::getSetting('fee_value', 0))
                    ->min(0)
            )
            ->add(
                'deposit_risk_enabled',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_enabled'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_enabled_helper'))
                    ->value((bool) CarRentalsHelper::getSetting('deposit_risk_enabled', false))
            )
            ->add(
                'deposit_risk_default_multiplier',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_default_multiplier'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_default_multiplier_helper'))
                    ->value(CarRentalsHelper::getSetting('deposit_risk_default_multiplier', 1))
                    ->min(0.1)
                    ->step(0.01)
            )
            ->add(
                'deposit_risk_guest_multiplier',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_guest_multiplier'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_guest_multiplier_helper'))
                    ->value(CarRentalsHelper::getSetting('deposit_risk_guest_multiplier', 1.15))
                    ->min(0.1)
                    ->step(0.01)
            )
            ->add(
                'deposit_risk_unverified_multiplier',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_unverified_multiplier'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_unverified_multiplier_helper'))
                    ->value(CarRentalsHelper::getSetting('deposit_risk_unverified_multiplier', 1.2))
                    ->min(0.1)
                    ->step(0.01)
            )
            ->add(
                'deposit_risk_low_history_threshold',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_low_history_threshold'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_low_history_threshold_helper'))
                    ->value(CarRentalsHelper::getSetting('deposit_risk_low_history_threshold', 3))
                    ->min(0)
                    ->step(1)
            )
            ->add(
                'deposit_risk_low_history_multiplier',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_low_history_multiplier'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_low_history_multiplier_helper'))
                    ->value(CarRentalsHelper::getSetting('deposit_risk_low_history_multiplier', 1.15))
                    ->min(0.1)
                    ->step(0.01)
            )
            ->add(
                'deposit_risk_low_rating_threshold',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_low_rating_threshold'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_low_rating_threshold_helper'))
                    ->value(CarRentalsHelper::getSetting('deposit_risk_low_rating_threshold', 3.5))
                    ->min(0)
                    ->max(5)
                    ->step(0.1)
            )
            ->add(
                'deposit_risk_low_rating_multiplier',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_low_rating_multiplier'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_low_rating_multiplier_helper'))
                    ->value(CarRentalsHelper::getSetting('deposit_risk_low_rating_multiplier', 1.1))
                    ->min(0.1)
                    ->step(0.01)
            )
            ->add(
                'deposit_risk_escalation_multiplier',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_escalation_multiplier'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_escalation_multiplier_helper'))
                    ->value(CarRentalsHelper::getSetting('deposit_risk_escalation_multiplier', 1.25))
                    ->min(0.1)
                    ->step(0.01)
            )
            ->add(
                'deposit_risk_medium_threshold',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_medium_threshold'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_medium_threshold_helper'))
                    ->value(CarRentalsHelper::getSetting('deposit_risk_medium_threshold', 1.1))
                    ->min(1)
                    ->step(0.01)
            )
            ->add(
                'deposit_risk_high_threshold',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_high_threshold'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.deposit_risk_high_threshold_helper'))
                    ->value(CarRentalsHelper::getSetting('deposit_risk_high_threshold', 1.35))
                    ->min(1)
                    ->step(0.01)
            )
            ->add(
                'deposit_category_multipliers_json',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.deposit_category_multipliers_json'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.deposit_category_multipliers_json_helper'))
                    ->value((string) CarRentalsHelper::getSetting('deposit_category_multipliers_json', '{}'))
                    ->rows(4)
            )
            ->add(
                'deposit_type_multipliers_json',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.deposit_type_multipliers_json'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.deposit_type_multipliers_json_helper'))
                    ->value((string) CarRentalsHelper::getSetting('deposit_type_multipliers_json', '{}'))
                    ->rows(4)
            )
            ->add(
                'price_lock_expired_message',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/car-rentals::settings.price_lock.forms.price_lock_expired_message'))
                    ->helperText(trans('plugins/car-rentals::settings.price_lock.forms.price_lock_expired_message_helper'))
                    ->value(CarRentalsHelper::getSetting(
                        'price_lock_expired_message',
                        'Price lock expired or quote changed. We refreshed your total. Please review and try again.'
                    ))
                    ->maxLength(500)
                    ->colspan(2)
            );
    }
}
