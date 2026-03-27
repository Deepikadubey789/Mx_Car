<?php

namespace Botble\CarRentals\Forms\Fronts;

use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Forms\Fronts\Auth\FieldOptions\TextFieldOption;
use Botble\CarRentals\Http\Requests\Fronts\ReviewRequest;
use Botble\Theme\FormFront;

class ReviewForm extends FormFront
{
    public function setup(): void
    {
        $isAuthenticated = auth('customer')->check();

        $this
            ->contentOnly()
            ->setFormOption('class', 'car-rentals-review-form')
            ->setUrl(route('public.car-reviews.create'))
            ->setValidatorClass(ReviewRequest::class)
            ->columns()
            ->add('customer_id', 'hidden', TextFieldOption::make()->value(auth('customer')->id()))
            ->add('car_id', 'hidden', TextFieldOption::make()->value($this->getModel()['car']?->getKey()))
            ->add(
                'rating-star',
                HtmlField::class,
                HtmlFieldOption::make()
                    ->view(CarRentalsHelper::viewPath('reviews.includes.form-rating-star'))
                    ->colspan(2)
            )
            ->add(
                'content',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->disabled(! $isAuthenticated)
                    ->label(trans('plugins/car-rentals::car-rentals.review.forms.content'))
                    ->placeholder('Share your experience with this car. What did you like? How was the condition? Would you recommend it to others? Your feedback helps other customers make informed decisions.')
                    ->helperText('Please provide an honest and detailed review (minimum 20 characters)')
                    ->rows(4)
                    ->colspan(2)
                    ->required()
            )
            ->add('button', 'submit', [
                'label' => trans('plugins/car-rentals::car-rentals.review.forms.submit'),
                'attr' => [
                    'class' => 'btn btn-primary',
                    'disabled' => ! $isAuthenticated,
                ],
                'colspan' => 2,
            ]);
    }
}
