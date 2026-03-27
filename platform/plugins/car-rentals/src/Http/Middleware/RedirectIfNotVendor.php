<?php

namespace Botble\CarRentals\Http\Middleware;

use Botble\Base\Facades\BaseHelper;
use Botble\CarRentals\Enums\CustomerStatusEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfNotVendor
{
    public function handle(Request $request, Closure $next, string $guard = 'customer')
    {
        if (! Auth::guard($guard)->check() || ! Auth::guard($guard)->user()->is_vendor) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            }

            return redirect()->guest(route('customer.login'));
        }

        $vendor = Auth::guard($guard)->user();

        if ($vendor->status != CustomerStatusEnum::ACTIVATED) {
            Auth::guard($guard)->logout();

            if ($request->ajax() || $request->wantsJson()) {
                return response(__('Your vendor account has been blocked.'), 403);
            }

            return redirect()
                ->guest(route('customer.login'))
                ->with('error_msg', __('Your vendor account has been blocked. Please contact support for assistance.'));
        }

        if (get_car_rentals_setting('verify_vendor', false) && ! $vendor->vendor_verified_at) {
            if ($request->ajax() || $request->wantsJson()) {
                return response(__('Vendor account is not verified.'), 403);
            }

            return redirect()->guest(BaseHelper::getHomepageUrl());
        }

        return $next($request);
    }
}
