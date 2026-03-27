<?php

namespace Botble\CarRentals\Http\Controllers;

use Botble\Base\Facades\Assets;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Forms\CustomerForm;
use Botble\CarRentals\Http\Requests\StoreCustomerRequest;
use Botble\CarRentals\Http\Requests\UpdateCustomerRequest;
use Botble\CarRentals\Http\Resources\CustomerResource;
use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Tables\CustomerTable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends BaseController
{
    public function __construct()
    {
        $this
            ->breadcrumb()
            ->add(trans('plugins/car-rentals::car-rentals.name'))
            ->add(trans('plugins/car-rentals::car-rentals.customer.name'), route('car-rentals.customers.index'));
    }

    public function index(CustomerTable $table)
    {
        $this->pageTitle(trans('plugins/car-rentals::car-rentals.customer.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/car-rentals::car-rentals.customer.create'));

        return CustomerForm::create()->renderForm();
    }

    public function store(StoreCustomerRequest $request)
    {
        $form = CustomerForm::create()->setRequest($request);
        $form->save();

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('car-rentals.customers.index'))
            ->setNextUrl(route('car-rentals.customers.edit', $form->getModel()->getKey()))
            ->withCreatedSuccessMessage();
    }

    public function edit(Customer $customer)
    {
        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $customer->name]));

        $customer->password = null;

        return CustomerForm::createFromModel($customer)
            ->setValidatorClass(UpdateCustomerRequest::class)
            ->renderForm();
    }

    public function update(Customer $customer, UpdateCustomerRequest $request)
    {
        CustomerForm::createFromModel($customer)->saving(function (CustomerForm $form) use ($request): void {
            $model = $form->getModel();

            $model->update($request->validated());
        });

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('car-rentals.customers.index'))
            ->withUpdatedSuccessMessage();
    }

    public function view(Customer $customer)
    {
        $this->pageTitle($customer->name . ' - ' . trans('plugins/car-rentals::car-rentals.customer.name'));

        if (! $customer->confirmed_at || ! $customer->is_vendor) {
            Assets::addScriptsDirectly('vendor/core/plugins/car-rentals/js/customer-view.js');
        }

        return view('plugins/car-rentals::customers.view', compact('customer'));
    }

    public function verify(Customer $customer, Request $request)
    {
        if ($customer->is_verified) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/car-rentals::car-rentals.customer.already_verified'));
        }

        $customer->is_verified = true;
        $customer->verified_at = Carbon::now();
        $customer->verified_by = Auth::id();
        $customer->verification_note = $request->input('verification_note');
        $customer->save();

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/car-rentals::car-rentals.customer.verify_success', ['name' => $customer->name]));
    }

    public function unverify(Customer $customer, Request $request)
    {
        if (! $customer->is_verified) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/car-rentals::car-rentals.customer.already_unverified'));
        }

        $customer->is_verified = false;
        $customer->verified_at = null;
        $customer->verified_by = null;
        $customer->verification_note = $request->input('verification_note');
        $customer->save();

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/car-rentals::car-rentals.customer.unverify_success', ['name' => $customer->name]));
    }

    public function verifyEmail(Customer $customer)
    {
        if ($customer->confirmed_at) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/car-rentals::car-rentals.customer.email_already_verified'));
        }

        $customer->confirmed_at = Carbon::now();
        $customer->save();

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/car-rentals::car-rentals.customer.verify_email_success', ['name' => $customer->name, 'email' => $customer->email]));
    }

    public function resendConfirmation(Customer $customer)
    {
        if ($customer->confirmed_at) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/car-rentals::car-rentals.customer.email_already_verified'));
        }

        try {
            $customer->sendEmailVerificationNotification();

            return $this
                ->httpResponse()
                ->setMessage(trans('plugins/car-rentals::car-rentals.customer.resend_confirmation_success', ['email' => $customer->email]));
        } catch (\Exception $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/car-rentals::car-rentals.customer.resend_confirmation_error'));
        }
    }

    public function upgradeToVendor(Customer $customer)
    {
        if ($customer->is_vendor) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/car-rentals::car-rentals.customer.already_vendor'));
        }

        $customer->is_vendor = true;
        $customer->save();

        // Send email notification to the customer
        if ($customer->email) {
            EmailHandler::setModule(CAR_RENTALS_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'customer_name' => $customer->name,
                    'customer_email' => $customer->email,
                    'dashboard_link' => route('car-rentals.vendor.dashboard'),
                ])
                ->sendUsingTemplate('vendor-upgrade', $customer->email);
        }

        return $this
            ->httpResponse()
            ->setData(['next_url' => route('car-rentals.vendor.dashboard')])
            ->setMessage(trans('plugins/car-rentals::car-rentals.customer.upgrade_to_vendor_success', ['name' => $customer->name]));
    }

    public function destroy(Customer $customer)
    {
        return DeleteResourceAction::make($customer);
    }

    public function getList(Request $request)
    {
        $keyword = BaseHelper::stringify($request->input('q'));

        if (! $keyword) {
            return $this
                ->httpResponse()
                ->setData([]);
        }

        $data = Customer::query()
            ->orWhere('name', 'LIKE', '%' . $keyword . '%')
            ->select(['id', 'name'])
            ->take(10)
            ->get();

        return $this
            ->httpResponse()
            ->setData(CustomerResource::collection($data));
    }
}
