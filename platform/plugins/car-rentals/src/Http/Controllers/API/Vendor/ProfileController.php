<?php

namespace Botble\CarRentals\Http\Controllers\API\Vendor;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Http\Resources\CustomerResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProfileController extends BaseApiController
{
    /**
     * Get vendor profile
     *
     * @group Car Rentals - Vendor
     */
    public function show()
    {
        $vendor = Auth::guard('sanctum')->user();

        return $this
            ->httpResponse()
            ->setData(new CustomerResource($vendor))
            ->toApiResponse();
    }

    /**
     * Update vendor profile
     *
     * @group Car Rentals - Vendor
     */
    public function update(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => ['string', 'max:255'],
                'last_name' => ['string', 'max:255'],
                'company_name' => ['nullable', 'string', 'max:255'],
                'phone' => ['nullable', 'string', 'max:20'],
                'address' => ['nullable', 'string', 'max:500'],
                'city' => ['nullable', 'string', 'max:100'],
                'state' => ['nullable', 'string', 'max:100'],
                'country' => ['nullable', 'string', 'max:100'],
                'postal_code' => ['nullable', 'string', 'max:20'],
                'website' => ['nullable', 'url', 'max:255'],
                'description' => ['nullable', 'string', 'max:2000'],
                'business_license' => ['nullable', 'string', 'max:255'],
                'tax_id' => ['nullable', 'string', 'max:255'],
            ]);
        } catch (ValidationException $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setCode(422)
                ->setMessage('Validation failed')
                ->setData(['errors' => $e->errors()])
                ->toApiResponse();
        }

        $vendor = Auth::guard('sanctum')->user();
        $vendor->update($validated);

        return $this
            ->httpResponse()
            ->setData(new CustomerResource($vendor->fresh()))
            ->setMessage('Profile updated successfully')
            ->toApiResponse();
    }
}
