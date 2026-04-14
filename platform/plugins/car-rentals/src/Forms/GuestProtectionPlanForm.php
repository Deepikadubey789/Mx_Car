<?php

namespace Botble\CarRentals\Forms;

use Botble\Base\Forms\FieldOptions\DescriptionFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\CarRentals\Http\Requests\GuestProtectionPlanRequest;
use Botble\CarRentals\Models\GuestProtectionPlan;

class GuestProtectionPlanForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(GuestProtectionPlan::class)
            ->setValidatorClass(GuestProtectionPlanRequest::class)
            ->withCustomFields()
            ->add('name', TextField::class, NameFieldOption::make()->required()->toArray())
            ->add('description', TextareaField::class, DescriptionFieldOption::make()->toArray())
            ->add(
                'daily_fee', 
                NumberField::class, 
                NumberFieldOption::make()
                    ->label('Daily Fee ($)')
                    ->helperText('How much the guest pays per day for this protection.')
                    ->required()
                    ->toArray()
            )
            ->add(
                'deductible_amount', 
                NumberField::class, 
                NumberFieldOption::make()
                    ->label('Deductible Amount ($)')
                    ->helperText('Maximum out-of-pocket the guest pays for damage.')
                    ->required()
                    ->toArray()
            )
            ->add(
                'liability_limit', 
                NumberField::class, 
                NumberFieldOption::make()
                    ->label('Liability Limit ($)')
                    ->helperText('Maximum coverage amount (optional).')
                    ->toArray()
            )
            ->add('status', SelectField::class, StatusFieldOption::make()->toArray())
            ->setBreakFieldPoint('status');
    }
}