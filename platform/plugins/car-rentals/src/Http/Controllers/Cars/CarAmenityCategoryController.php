<?php

namespace Botble\CarRentals\Http\Controllers\Cars;

use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\CarRentals\Forms\CarAmenityCategoryForm;
use Botble\CarRentals\Http\Requests\CarAmenityCategoryRequest;
use Botble\CarRentals\Models\CarAmenityCategory;
use Botble\CarRentals\Tables\CarAmenityCategoryTable;
use Exception;

class CarAmenityCategoryController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans('plugins/car-rentals::car-rentals.attribute.name'))
            ->add(trans('plugins/car-rentals::car-amenity-category.name'), route('car-rentals.car-amenity-categories.index'));
    }

    public function index(CarAmenityCategoryTable $table)
    {
        $this->pageTitle(trans('plugins/car-rentals::car-amenity-category.name'));

        return $table->renderTable();
    }

    public function create(FormBuilder $formBuilder)
    {
        $this->pageTitle(trans('plugins/car-rentals::car-amenity-category.create'));

        return $formBuilder->create(CarAmenityCategoryForm::class)->renderForm();
    }

    public function store(CarAmenityCategoryRequest $request, BaseHttpResponse $response)
    {
        $carAmenityCategory = CarAmenityCategory::query()->create($request->validated());

        return $response
            ->setPreviousUrl(route('car-rentals.car-amenity-categories.index'))
            ->setNextUrl(route('car-rentals.car-amenity-categories.edit', $carAmenityCategory->getKey()))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    public function show(CarAmenityCategory $carAmenityCategory)
    {
        return view('plugins/car-rentals::car-amenity-categories.show', compact('carAmenityCategory'));
    }

    public function edit(CarAmenityCategory $carAmenityCategory, FormBuilder $formBuilder)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $carAmenityCategory->name]));

        return $formBuilder->create(CarAmenityCategoryForm::class, ['model' => $carAmenityCategory])->renderForm();
    }

    public function update(CarAmenityCategory $carAmenityCategory, CarAmenityCategoryRequest $request, BaseHttpResponse $response)
    {
        $carAmenityCategory->fill($request->validated());
        $carAmenityCategory->save();

        return $response
            ->setPreviousUrl(route('car-rentals.car-amenity-categories.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    public function destroy(CarAmenityCategory $carAmenityCategory, BaseHttpResponse $response)
    {
        try {
            $carAmenityCategory->delete();

            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }
}
