<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Http\Resources\CustomerResource;
use Botble\Media\Facades\RvMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfileController extends BaseApiController
{
    /**
     * Get customer profile
     *
     * @group Car Rentals - Profile
     */
    public function show()
    {
        $customer = Auth::guard('sanctum')->user();

        return $this
            ->httpResponse()
            ->setData(new CustomerResource($customer))
            ->toApiResponse();
    }

    /**
     * Update customer profile
     *
     * @group Car Rentals - Profile
     */
    public function update(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => 'sometimes|required|string|email|max:255|unique:cr_customers,email,' . $customer->id,
            'phone' => ['nullable', 'string', 'max:20'],
            'dob' => ['nullable', 'date'],
            'current_password' => ['required_with:password'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        // Verify current password if changing password
        if ($request->has('password')) {
            if (! Hash::check($request->input('current_password'), $customer->password)) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage('Current password is incorrect')
                    ->toApiResponse();
            }
        }

        $updateData = $request->only(['name', 'email', 'phone', 'dob']);

        if ($request->has('password')) {
            $updateData['password'] = Hash::make($request->input('password'));
        }

        $customer->update($updateData);

        return $this
            ->httpResponse()
            ->setData(new CustomerResource($customer->fresh()))
            ->setMessage('Profile updated successfully')
            ->toApiResponse();
    }

    /**
     * Update customer avatar
     *
     * @group Car Rentals - Profile
     */
    public function updateAvatar(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        try {
            $result = RvMedia::handleUpload($request->file('avatar'), 0, 'customers');

            if ($result['error']) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage($result['message'])
                    ->toApiResponse();
            }

            // Delete old avatar if exists
            if ($customer->avatar) {
                RvMedia::deleteFile($customer->avatar);
            }

            $customer->update([
                'avatar' => $result['data']->url,
            ]);

            return $this
                ->httpResponse()
                ->setData(new CustomerResource($customer->fresh()))
                ->setMessage('Avatar updated successfully')
                ->toApiResponse();

        } catch (\Exception $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Failed to upload avatar: ' . $e->getMessage())
                ->toApiResponse();
        }
    }

    /**
     * Change customer password
     *
     * @group Car Rentals - Profile
     */
    public function changePassword(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        if (! Hash::check($request->input('current_password'), $customer->password)) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage('Current password is incorrect')
                ->toApiResponse();
        }

        $customer->update([
            'password' => Hash::make($request->input('password')),
        ]);

        // Optionally revoke all other tokens to force re-login on other devices
        if ($request->boolean('logout_other_devices', false)) {
            $currentToken = $customer->currentAccessToken();
            $customer->tokens()->where('id', '!=', $currentToken->id)->delete();
        }

        return $this
            ->httpResponse()
            ->setMessage('Password changed successfully')
            ->toApiResponse();
    }
}
