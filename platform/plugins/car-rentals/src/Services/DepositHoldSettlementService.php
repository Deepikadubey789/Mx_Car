<?php

namespace Botble\CarRentals\Services;

use Botble\CarRentals\Models\Booking;
use Botble\Payment\Models\Payment;
use Botble\Stripe\Services\Gateways\StripePaymentService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Stripe\Charge;

class DepositHoldSettlementService
{
    public function settle(Booking $booking, string $action, ?float $requestedCaptureAmount = null): array
    {
        $payment = $booking->payment;

        if (! $payment || ! $payment->id) {
            return [
                'ok' => false,
                'message' => __('No authorized deposit hold found for this booking.'),
            ];
        }

        // Recover legacy/inconsistent rows where booking shows authorized hold
        // but payment hold flags were not persisted.
        if (! $payment->is_authorized_hold) {
            $fallbackAuthorizedAmount = (float) ($booking->deposit_hold_amount ?: $booking->deposit_amount);

            if ($booking->deposit_hold_status === 'authorized' && $fallbackAuthorizedAmount > 0) {
                $payment->update([
                    'is_authorized_hold' => true,
                    'authorized_amount' => $payment->authorized_amount ?: $fallbackAuthorizedAmount,
                    'authorized_at' => $payment->authorized_at ?: $booking->deposit_authorized_at ?: Carbon::now(),
                ]);

                $payment->refresh();
            }
        }

        if (! $payment->is_authorized_hold) {
            return [
                'ok' => false,
                'message' => __('No authorized deposit hold found for this booking.'),
            ];
        }

        if ($booking->deposit_settled_at) {
            return [
                'ok' => true,
                'message' => __('Deposit hold already settled.'),
            ];
        }

        $authorizedAmount = (float) ($payment->authorized_amount ?: $booking->deposit_hold_amount ?: $booking->deposit_amount);

        if ($authorizedAmount <= 0) {
            return [
                'ok' => false,
                'message' => __('Authorized deposit amount is invalid.'),
            ];
        }

        if ($action === 'capture_partial') {
            $requested = (float) $requestedCaptureAmount;

            if ($requested <= 0) {
                return [
                    'ok' => false,
                    'message' => __('Capture amount must be greater than 0 for partial capture.'),
                ];
            }

            if ($requested > $authorizedAmount) {
                return [
                    'ok' => false,
                    'message' => __('Capture amount cannot exceed the authorized hold amount.'),
                ];
            }
        }

        $captureAmount = $this->resolveCaptureAmount($action, $authorizedAmount, $requestedCaptureAmount);
        $releaseAmount = round(max(0, $authorizedAmount - $captureAmount), 2);

        $stripeMethod = defined('STRIPE_PAYMENT_METHOD_NAME') ? STRIPE_PAYMENT_METHOD_NAME : 'stripe';

        if ((string) $payment->payment_channel === $stripeMethod && $payment->charge_id) {
            $response = $this->settleStripeAuthorization($payment, $action, $captureAmount);

            if (! $response['ok']) {
                return $response;
            }
        }

        $settledStatus = $action === 'release'
            ? ((string) $payment->payment_channel === $stripeMethod ? 'release_pending_provider_expiry' : 'released')
            : 'captured';

        DB::transaction(function () use ($booking, $payment, $settledStatus, $captureAmount, $releaseAmount): void {
            $now = Carbon::now();

            $booking->update([
                'deposit_hold_status' => $settledStatus,
                'deposit_settled_at' => $now,
                'deposit_captured_amount' => $captureAmount,
                'deposit_released_amount' => $releaseAmount,
            ]);

            $payment->update([
                'captured_amount' => $captureAmount,
                'released_amount' => $releaseAmount,
                'captured_at' => $captureAmount > 0 ? $now : null,
                'released_at' => $releaseAmount > 0 ? $now : null,
                'is_authorized_hold' => false,
            ]);
        });

        return [
            'ok' => true,
            'message' => __('Deposit hold settled successfully.'),
        ];
    }

    protected function settleStripeAuthorization(Payment $payment, string $action, float $captureAmount): array
    {
        $gateway = app(StripePaymentService::class)->setCurrency($payment->currency);

        if (! $gateway->setClient()) {
            return [
                'ok' => false,
                'message' => __('Stripe settings are invalid.'),
            ];
        }

        try {
            $charge = Charge::retrieve($payment->charge_id);

            if ($charge->captured) {
                return [
                    'ok' => true,
                    'message' => __('Charge already captured on provider side.'),
                ];
            }

            if ($action === 'release') {
                // Stripe uncaptured charges are released automatically by network expiration.
                return [
                    'ok' => true,
                    'message' => __('Authorization release scheduled with provider expiration window.'),
                ];
            }

            $params = [];
            if ($action === 'capture_partial' && $captureAmount > 0) {
                $params['amount'] = $this->convertForStripe($captureAmount, strtoupper($payment->currency));
            }

            $charge->capture($params);

            return [
                'ok' => true,
                'message' => __('Authorization captured successfully.'),
            ];
        } catch (\Throwable $exception) {
            return [
                'ok' => false,
                'message' => $exception->getMessage(),
            ];
        }
    }

    protected function resolveCaptureAmount(string $action, float $authorizedAmount, ?float $requestedCaptureAmount): float
    {
        if ($action === 'release') {
            return 0;
        }

        if ($action === 'capture_full') {
            return round($authorizedAmount, 2);
        }

        return round(min(max((float) $requestedCaptureAmount, 0), $authorizedAmount), 2);
    }

    protected function convertForStripe(float $amount, string $currency): int
    {
        $multiplier = 100;

        $zeroDecimalCurrencies = [
            'BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'UGX', 'VND', 'VUV', 'XAF', 'XOF', 'XPF',
        ];

        if (in_array($currency, $zeroDecimalCurrencies, true)) {
            return (int) round($amount);
        }

        return (int) round($amount * $multiplier);
    }
}
