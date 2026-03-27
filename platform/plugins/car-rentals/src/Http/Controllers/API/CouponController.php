<?php

namespace Botble\CarRentals\Http\Controllers\API;

use Botble\Api\Http\Controllers\BaseApiController;
use Botble\CarRentals\Models\Coupon;
use Botble\CarRentals\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CouponController extends BaseApiController
{
    public function __construct(protected CouponService $couponService)
    {
    }

    /**
     * Check coupon validity
     *
     * @group Car Rentals
     */
    public function check(Request $request)
    {
        $customer = Auth::guard('sanctum')->user();

        $request->validate([
            'code' => ['required', 'string'],
            'total_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $code = $request->input('code');
        $totalAmount = $request->input('total_amount');

        try {
            $coupon = Coupon::where('code', $code)->first();

            if (! $coupon) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage('Coupon not found')
                    ->toApiResponse();
            }

            $validation = $this->couponService->validateCoupon($coupon, $customer->id, $totalAmount);

            if (! $validation['valid']) {
                return $this
                    ->httpResponse()
                    ->setError()
                    ->setMessage($validation['message'])
                    ->toApiResponse();
            }

            $discountAmount = $this->couponService->calculateDiscount($coupon, $totalAmount);

            return $this
                ->httpResponse()
                ->setData([
                    'valid' => true,
                    'coupon' => [
                        'id' => $coupon->id,
                        'code' => $coupon->code,
                        'type' => $coupon->type,
                        'value' => $coupon->value,
                        'description' => $coupon->description,
                    ],
                    'discount_amount' => $discountAmount,
                    'final_amount' => max(0, $totalAmount - $discountAmount),
                ])
                ->setMessage('Coupon is valid')
                ->toApiResponse();

        } catch (\Exception $e) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage($e->getMessage())
                ->toApiResponse();
        }
    }

    /**
     * Validate coupon code (alias for check)
     *
     * @group Car Rentals
     */
    public function validateCoupon(Request $request)
    {
        return $this->check($request);
    }

    /**
     * Apply coupon to current session
     *
     * @group Car Rentals
     */
    public function apply(Request $request)
    {
        // TODO: Implement session-based coupon application
        return $this
            ->httpResponse()
            ->setMessage('Coupon applied successfully')
            ->toApiResponse();
    }

    /**
     * Remove coupon from current session
     *
     * @group Car Rentals
     */
    public function remove()
    {
        // TODO: Implement session-based coupon removal
        return $this
            ->httpResponse()
            ->setMessage('Coupon removed successfully')
            ->toApiResponse();
    }

    /**
     * Get customer's available coupons
     *
     * @group Car Rentals
     */
    public function getMyCoupons()
    {
        $customer = Auth::guard('sanctum')->user();

        // TODO: Implement customer-specific coupons
        $coupons = [];

        return $this
            ->httpResponse()
            ->setData($coupons)
            ->toApiResponse();
    }
}
