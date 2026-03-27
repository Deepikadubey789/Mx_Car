<?php

namespace Botble\CarRentals\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FieldOptions\CoreIconFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\SortOrderFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\Fields\CoreIconField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Botble\CarRentals\Http\Requests\CarAmenityRequest;
use Botble\CarRentals\Models\CarAmenity;
use Botble\CarRentals\Models\CarAmenityCategory;

class CarAmenityForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(CarAmenity::class)
            ->setValidatorClass(CarAmenityRequest::class)
            ->add(
                'name',
                TextField::class,
                NameFieldOption::make()
                    ->required()
                    ->maxLength(120)
            )
            ->add('category_id', SelectField::class, [
                'label' => trans('plugins/car-rentals::car-amenity-category.category'),
                'choices' => ['' => trans('plugins/car-rentals::car-amenity-category.select_category')] + CarAmenityCategory::query()
                    ->wherePublished()
                    ->oldest('order')
                    ->oldest('name')
                    ->pluck('name', 'id')
                    ->toArray(),
            ])
            ->add('icon', CoreIconField::class, CoreIconFieldOption::make())
            ->add('status', SelectField::class, StatusFieldOption::make()->choices(BaseStatusEnum::labels()))
            ->add('order', NumberField::class, SortOrderFieldOption::make())
            ->setBreakFieldPoint('status');
    }
}
