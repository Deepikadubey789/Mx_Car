<?php

namespace Botble\CarRentals\Services\Kyc\Providers;

use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Models\Customer;
use Botble\CarRentals\Services\Kyc\KycProviderInterface;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;
use Throwable;

/**
 * Stripe Identity — document + selfie (see https://docs.stripe.com/identity/verify-identity-documents).
 * Server-side: {@see StripeClient} `identity->verificationSessions->create` with `type` document, metadata, etc.
 * Only the client secret is returned to the browser for `verifyIdentity`; secret key must stay in settings, not in code.
 */
class StripeIdentityKycProvider implements KycProviderInterface
{
    public function verify(array $payload): array
    {
        $secret = (string) CarRentalsHelper::getSetting('kyc_stripe_secret_key', '');
        if ($secret === '') {
            throw new \RuntimeException('Stripe secret key is not configured.');
        }

        $verificationId = (int) data_get($payload, 'verification_id', 0);
        $customerId = (int) data_get($payload, 'customer_id', 0);
        $customer = $customerId > 0 ? Customer::query()->find($customerId) : null;

        try {
            $stripe = new StripeClient($secret);

            $returnUrl = route('customer.kyc.stripe-identity-return', [], true);

            $session = $stripe->identity->verificationSessions->create([
                'type' => 'document',
                'client_reference_id' => 'cr_kyc_' . $verificationId,
                'metadata' => [
                    'cr_verification_id' => (string) $verificationId,
                    'cr_customer_id' => (string) $customerId,
                ],
                'provided_details' => array_filter([
                    'email' => $customer?->email,
                ]),
                'options' => [
                    'document' => [
                        'require_matching_selfie' => true,
                        'allowed_types' => ['driving_license', 'id_card', 'passport'],
                    ],
                ],
                'return_url' => $returnUrl,
            ], [
                'idempotency_key' => 'cr_kyc_vs_' . $verificationId,
            ]);

            $sessionArray = $session->toArray();
            $clientSecret = $session->client_secret;
            unset($sessionArray['client_secret']);

            return [
                'await_stripe_hosted' => true,
                'provider_reference' => $session->id,
                'stripe_identity_url' => $session->url,
                'stripe_client_secret' => $clientSecret,
                'license_valid' => false,
                'license_number' => null,
                'license_expiry_date' => null,
                'ocr_confidence_score' => null,
                'face_match_score' => null,
                'risk_score' => null,
                'ocr_payload' => [],
                'provider_payload' => $sessionArray,
                'sync_internal_status' => null,
            ];
        } catch (ApiErrorException $exception) {
            if ((bool) CarRentalsHelper::getSetting('fallback_to_mock_on_provider_error', true)) {
                Log::warning('car_rentals_kyc_stripe_identity_fallback', [
                    'error' => $exception->getMessage(),
                ]);

                $fallback = app(MockThirdPartyKycProvider::class)->verify($payload);
                $fallback['provider_payload'] = array_merge((array) ($fallback['provider_payload'] ?? []), [
                    'fallback' => true,
                    'provider' => 'mock',
                    'reason' => $exception->getMessage(),
                ]);
                $fallback['await_stripe_hosted'] = false;

                return $fallback;
            }

            throw $exception;
        } catch (Throwable $exception) {
            if ((bool) CarRentalsHelper::getSetting('fallback_to_mock_on_provider_error', true)) {
                Log::warning('car_rentals_kyc_stripe_identity_fallback', [
                    'error' => $exception->getMessage(),
                ]);

                $fallback = app(MockThirdPartyKycProvider::class)->verify($payload);
                $fallback['provider_payload'] = array_merge((array) ($fallback['provider_payload'] ?? []), [
                    'fallback' => true,
                    'provider' => 'mock',
                    'reason' => $exception->getMessage(),
                ]);
                $fallback['await_stripe_hosted'] = false;

                return $fallback;
            }

            throw $exception;
        }
    }
}
