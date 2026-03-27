<?php

namespace Botble\CarRentals\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Forms\CustomerForm;
use Botble\CarRentals\Http\Requests\UpdateCustomerRequest;
use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Tables\VendorTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans('plugins/car-rentals::car-rentals.name'))
            ->add(trans('plugins/car-rentals::car-rentals.vendor.name'), route('car-rentals.vendors.index'));
    }

    public function index(VendorTable $table)
    {
        $this->pageTitle(trans('plugins/car-rentals::car-rentals.vendor.name'));

        return $table->renderTable();
    }

    public function edit(Customer $vendor)
    {
        abort_unless($vendor->is_vendor, 404);

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $vendor->name]));

        $vendor->password = null;

        return CustomerForm::createFromModel($vendor)
            ->setValidatorClass(UpdateCustomerRequest::class)
            ->renderForm();
    }

    public function update(Customer $vendor, UpdateCustomerRequest $request)
    {
        abort_unless($vendor->is_vendor, 404);

        CustomerForm::createFromModel($vendor)->saving(function (CustomerForm $form) use ($request): void {
            $model = $form->getModel();
            $model->update($request->validated());
        });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('car-rentals.vendors.index'))
            ->withUpdatedSuccessMessage();
    }

    public function view(Customer $vendor)
    {
        abort_unless($vendor->is_vendor, 404);

        $this->pageTitle(trans('plugins/car-rentals::car-rentals.vendor.view', ['name' => $vendor->name]));

        Assets::addScriptsDirectly('vendor/core/plugins/car-rentals/js/vendor-view.js');

        return view('plugins/car-rentals::vendors.view', compact('vendor'));
    }

    public function destroy(Customer $vendor)
    {
        abort_unless($vendor->is_vendor, 404);

        return DeleteResourceAction::make($vendor);
    }

    public function verify(Customer $vendor, Request $request)
    {
        abort_unless($vendor->is_vendor, 404);

        if ($vendor->is_verified) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/car-rentals::car-rentals.vendor.already_verified'));
        }

        $vendor->is_verified = true;
        $vendor->verified_at = Carbon::now();
        $vendor->verified_by = Auth::id();
        $vendor->verification_note = $request->input('verification_note');
        $vendor->save();

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/car-rentals::car-rentals.vendor.verified_successfully'));
    }

    public function unverify(Customer $vendor, Request $request)
    {
        abort_unless($vendor->is_vendor, 404);

        if (! $vendor->is_verified) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/car-rentals::car-rentals.vendor.not_verified_yet'));
        }

        $vendor->is_verified = false;
        $vendor->verified_at = null;
        $vendor->verified_by = null;
        $vendor->verification_note = $request->input('verification_note');
        $vendor->save();

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/car-rentals::car-rentals.vendor.unverified_successfully'));
    }
}
