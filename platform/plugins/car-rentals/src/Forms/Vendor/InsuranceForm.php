<?php

namespace Botble\CarRentals\Forms\Vendor;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\CarRentals\Http\Requests\Vendor\InsuranceRequest;
use Botble\CarRentals\Models\Insurance;

class InsuranceForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->setupModel(new Insurance())
            ->setValidatorClass(InsuranceRequest::class)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('core/base::forms.name'),
                'required' => true,
                'attr' => ['placeholder' => __('Insurance Package Name (e.g. Premium Cover)')],
            ])
            ->add('description', 'textarea', [
                'label' => trans('core/base::forms.description'),
                'attr' => ['rows' => 4, 'placeholder' => __('Describe what this covers...')],
            ])
            ->add('price', 'number', [
                'label' => __('Price'),
                'required' => true,
                'attr' => ['placeholder' => __('0.00'), 'step' => '0.01'],
            ])
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'required' => true,
                'choices' => BaseStatusEnum::labels(),
            ])
            ->setBreakFieldPoint('status');
    }
}