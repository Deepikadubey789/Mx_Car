<?php

namespace Botble\CarRentals\Http\Controllers;

use Botble\Base\Facades\EmailHandler;
use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Enums\CarStatusEnum;
use Botble\CarRentals\Enums\CustomerStatusEnum;
use Botble\CarRentals\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class VendorBlockedController extends BaseController
{
    public function store(int|string $id, Request $request)
    {
        $customer = Customer::query()
            ->where('status', CustomerStatusEnum::ACTIVATED)
            ->where('is_vendor', true)
            ->findOrFail($id);

        $request->validate([
            'reason' => ['required', 'string', 'max:400'],
        ]);

        $customer->block_reason = $request->input('reason');
        $customer->status = CustomerStatusEnum::LOCKED;
        $customer->save();

        $customer->cars()
            ->where('status', CarStatusEnum::AVAILABLE)
            ->update(['status' => CarStatusEnum::BLOCKED]);

        EmailHandler::setModule(CAR_RENTALS_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'block_reason' => $customer->block_reason,
                'block_date' => Carbon::now()->translatedFormat('M d, Y'),
                'vendor_name' => $customer->name,
                'vendor_email' => $customer->email,
            ])
            ->sendUsingTemplate('vendor-account-blocked', $customer->email);

        return redirect()
    ->back()
    ->with('success_msg', trans('plugins/car-rentals::vendor.control.blocked_success'));
    }

    public function destroy(int|string $id)
    {
        $customer = Customer::query()
            ->where('status', CustomerStatusEnum::LOCKED)
            ->where('is_vendor', true)
            ->findOrFail($id);

        $customer->block_reason = null;
        $customer->status = CustomerStatusEnum::ACTIVATED;
        $customer->save();

        $customer->cars()
            ->where('status', CarStatusEnum::BLOCKED)
            ->update(['status' => CarStatusEnum::AVAILABLE]);

        EmailHandler::setModule(CAR_RENTALS_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'vendor_name' => $customer->name,
                'vendor_email' => $customer->email,
                'unblock_date' => Carbon::now()->translatedFormat('M d, Y'),
            ])
            ->sendUsingTemplate('vendor-account-unblocked', $customer->email);

        return redirect()
    ->back()
    ->with('success_msg', trans('plugins/car-rentals::vendor.control.unblocked_success'));
    }
}
