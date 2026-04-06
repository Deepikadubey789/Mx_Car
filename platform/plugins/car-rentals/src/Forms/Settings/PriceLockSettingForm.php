<?php

namespace Botble\CarRentals\Forms\Settings;

use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
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
