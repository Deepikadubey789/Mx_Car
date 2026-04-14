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
use Botble\CarRentals\Http\Requests\HostProtectionPlanRequest;
use Botble\CarRentals\Models\HostProtectionPlan;

class HostProtectionPlanForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(HostProtectionPlan::class)
            ->setValidatorClass(HostProtectionPlanRequest::class)
            ->withCustomFields()
            ->add('name', TextField::class, NameFieldOption::make()->required()->toArray())
            ->add('description', TextareaField::class, DescriptionFieldOption::make()->toArray())
            ->add(
                'revenue_share_percentage', 
                NumberField::class, 
                NumberFieldOption::make()
                    ->label('Revenue Share Percentage (%)')
                    ->helperText('Example: 75 means the host keeps 75% of the trip price.')
                    ->required()
                    ->toArray()
            )
            ->add(
                'deductible_amount', 
                NumberField::class, 
                NumberFieldOption::make()
                    ->label('Deductible Amount ($)')
                    ->helperText('Maximum out-of-pocket the host pays for a damage claim.')
                    ->required()
                    ->toArray()
            )
            ->add('status', SelectField::class, StatusFieldOption::make()->toArray())
            ->setBreakFieldPoint('status');
    }
}