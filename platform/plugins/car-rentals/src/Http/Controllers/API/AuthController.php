<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Http\Resources\CustomerResource;
use Botble\CarRentals\Models\Customer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseApiController
{
    /**
     * Register a new customer
     *
     * @group Car Rentals - Authentication
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:cr_customers'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'dob' => ['nullable', 'date'],
            'is_vendor' => ['boolean'],
        ]);

        $customer = Customer::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone'),
            'dob' => $request->input('dob'),
            'is_vendor' => $request->boolean('is_vendor', false),
        ]);

        event(new Registered($customer));

        $token = $customer->createToken('auth-token')->plainTextToken;

        return $this
            ->httpResponse()
            ->setData([
                'customer' => new CustomerResource($customer),
                'token' => $token,
                'token_type' => 'Bearer',
            ])
            ->setMessage('Registration successful')
            ->toApiResponse();
    }

    /**
     * Login customer
     *
     * @group Car Rentals - Authentication
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $customer = Customer::where('email', $request->input('email'))->first();

        if (! $customer || ! Hash::check($request->input('password'), $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // Revoke all existing tokens
        $customer->tokens()->delete();

        $token = $customer->createToken('auth-token')->plainTextToken;

        return $this
            ->httpResponse()
            ->setData([
                'customer' => new CustomerResource($customer),
                'token' => $token,
                'token_type' => 'Bearer',
            ])
            ->setMessage('Login successful')
            ->toApiResponse();
    }

    /**
     * Logout customer
     *
     * @group Car Rentals - Authentication
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this
            ->httpResponse()
            ->setMessage('Logout successful')
            ->toApiResponse();
    }

    /**
     * Send password reset link
     *
     * @group Car Rentals - Authentication
     */
    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::broker('customers')->sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return $this
                ->httpResponse()
                ->setMessage('Password reset link sent to your email')
                ->toApiResponse();
        }

        return $this
            ->httpResponse()
            ->setError()
            ->setMessage('Unable to send password reset link')
            ->toApiResponse();
    }

    /**
     * Reset password
     *
     * @group Car Rentals - Authentication
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($customer, $password): void {
                $customer->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return $this
                ->httpResponse()
                ->setMessage('Password reset successful')
                ->toApiResponse();
        }

        return $this
            ->httpResponse()
            ->setError()
            ->setMessage('Password reset failed')
            ->toApiResponse();
    }
}
