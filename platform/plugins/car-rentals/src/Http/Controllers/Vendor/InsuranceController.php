<?php

namespace Botble\CarRentals\Http\Controllers\Vendor;

use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Forms\Vendor\InsuranceForm;
use Botble\CarRentals\Http\Requests\Vendor\InsuranceRequest;
use Botble\CarRentals\Models\Insurance;
use Botble\CarRentals\Tables\Vendor\InsuranceTable;

class InsuranceController extends BaseController
{
    public function index(InsuranceTable $table)
    {
        $this->pageTitle(__('Insurances'));
        return $table->render('plugins/car-rentals::themes.vendor-dashboard.table.base');
    }

    public function create()
    {
        $this->pageTitle(__('Create Insurance'));

        // Wrap the form inside the vendor dashboard layout
        return InsuranceForm::create()
            ->setFormOption('template', 'plugins/car-rentals::themes.vendor-dashboard.forms.base')
            ->renderForm();
    }

    public function store(InsuranceRequest $request)
    {
        $form = InsuranceForm::create()->setRequest($request);
        $form->saving(function (InsuranceForm $form) {
            $insurance = $form->getModel();
            $insurance->fill($form->getRequest()->input());
            $insurance->vendor_id = auth('customer')->id(); // Force vendor ID
            $insurance->save();
        });

        return $this->httpResponse()
            ->setPreviousUrl(route('car-rentals.vendor.insurances.index'))
            ->setNextUrl(route('car-rentals.vendor.insurances.edit', $form->getModel()->id))
            ->withCreatedSuccessMessage();
    }

    public function edit(Insurance $insurance)
    {
        abort_if($insurance->vendor_id != auth('customer')->id(), 403);
        
        $this->pageTitle(__('Edit Insurance') . ' "' . $insurance->name . '"');
        
        // Wrap the form inside the vendor dashboard layout
        return InsuranceForm::createFromModel($insurance)
            ->setFormOption('template', 'plugins/car-rentals::themes.vendor-dashboard.forms.base')
            ->renderForm();
    }

    public function update(Insurance $insurance, InsuranceRequest $request)
    {
        abort_if($insurance->vendor_id != auth('customer')->id(), 403);
        $form = InsuranceForm::createFromModel($insurance)->setRequest($request);
        $form->saving(function (InsuranceForm $form) {
            $insurance = $form->getModel();
            $insurance->fill($form->getRequest()->input());
            $insurance->save();
        });

        return $this->httpResponse()
            ->setPreviousUrl(route('car-rentals.vendor.insurances.index'))
            ->setNextUrl(route('car-rentals.vendor.insurances.edit', $insurance->id))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Insurance $insurance)
    {
        abort_if($insurance->vendor_id != auth('customer')->id(), 403);
        return DeleteResourceAction::make($insurance);
    }
}