<?php

namespace Botble\CarRentals\Forms;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Forms\FieldOptions\CoreIconFieldOption;
use Botble\Base\Forms\Fields\CoreIconField;
use Botble\Base\Forms\FormAbstract;
use Botble\CarRentals\Http\Requests\CarAmenityCategoryRequest;
use Botble\CarRentals\Models\CarAmenityCategory;

class CarAmenityCategoryForm extends FormAbstract
{
    public function buildForm(): void
    {
        $this
            ->setupModel(new CarAmenityCategory())
            ->setValidatorClass(CarAmenityCategoryRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('core/base::forms.name'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 255,
                ],
            ])
            ->add('icon', CoreIconField::class, CoreIconFieldOption::make())
            ->add('order', 'number', [
                'label' => trans('core/base::forms.order'),
                'attr' => [
                    'placeholder' => trans('core/base::forms.order_by_placeholder'),
                ],
                'default_value' => 0,
            ])
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'required' => true,
                'choices' => BaseStatusEnum::labels(),
            ])
            ->setBreakFieldPoint('status');
    }
}
