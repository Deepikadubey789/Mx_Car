<?php

namespace Botble\CarRentals\Http\Controllers\Vendor;

use Botble\Base\Http\Controllers\BaseController;
use Botble\CarRentals\Models\DeliveryLocation;
use Illuminate\Http\Request;
use Botble\CarRentals\Facades\CarRentalsHelper;

class DeliveryLocationController extends BaseController
{
    /**
     * Show the list of delivery locations
     */
    public function index()
    {
        $this->pageTitle(__('Delivery Zones & Airports'));
        $vendorId = auth('customer')->id();

        $locations = DeliveryLocation::where('vendor_id', $vendorId)->latest()->get();

        return CarRentalsHelper::view('vendor-dashboard.delivery-locations', compact('locations'));
    }

    /**
     * Store a new delivery location
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:airport,hotel,custom_zone,custom_address',
            'fee_amount' => 'required|numeric|min:0',
        ]);

        DeliveryLocation::create([
            'vendor_id' => auth('customer')->id(),
            'name' => $request->name,
            'type' => $request->type,
            'fee_amount' => $request->fee_amount,
            'status' => 'published',
        ]);

        return back()->with('success_msg', __('Delivery location added successfully!'));
    }

    /**
     * Delete a delivery location
     */
    public function destroy($id)
    {
        $location = DeliveryLocation::where('vendor_id', auth('customer')->id())->findOrFail($id);
        $location->delete();

        return back()->with('success_msg', __('Delivery location deleted successfully.'));
    }
}