<?php

namespace Botble\CarRentals\Services\Kyc\Providers;

use Botble\CarRentals\Facades\CarRentalsHelper;
use Botble\CarRentals\Services\Kyc\KycProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class VeriffKycProvider implements KycProviderInterface
{
    public function verify(array $payload): array
    {
        $baseUrl = rtrim((string) CarRentalsHelper::getSetting('kyc_veriff_base_url', 'https://stationapi.veriff.com'), '/');
        $apiKey = (string) CarRentalsHelper::getSetting('kyc_veriff_api_key');
        $sharedSecret = (string) CarRentalsHelper::getSetting('kyc_veriff_shared_secret');

        try {
            if ($apiKey === '' || $sharedSecret === '') {
                throw new \RuntimeException('Veriff credentials are not configured.');
            }

            $response = Http::withHeaders([
                'X-AUTH-CLIENT' => $apiKey,
                'X-HMAC-SIGNATURE' => hash_hmac('sha256', json_encode($payload), $sharedSecret),
                'Content-Type' => 'application/json',
            ])
                ->timeout(15)
                ->post($baseUrl . '/v1/kyc/verify', $payload);

            if (! $response->successful()) {
                throw new \RuntimeException('Veriff request failed with status ' . $response->status());
            }

            $data = $response->json();

            $licenseValid = (bool) data_get($data, 'document.license.valid', false);
            $faceMatch = (float) data_get($data, 'scores.face_match', 0);
            $ocrConfidence = (float) data_get($data, 'scores.ocr_confidence', 0);
            $riskScore = (float) data_get($data, 'scores.risk', 1);

            return [
                'provider_reference' => (string) data_get($data, 'verification.id', Str::uuid()->toString()),
                'license_valid' => $licenseValid,
                'license_number' => (string) data_get($data, 'document.license.number', data_get($payload, 'license_number')),
                'license_expiry_date' => data_get($data, 'document.license.expiry'),
                'ocr_confidence_score' => $ocrConfidence,
                'face_match_score' => $faceMatch,
                'risk_score' => $riskScore,
                'ocr_payload' => (array) data_get($data, 'document.ocr', []),
                'provider_payload' => $data,
            ];
        } catch (Throwable $exception) {
            if ((bool) CarRentalsHelper::getSetting('fallback_to_mock_on_provider_error', true)) {
                Log::warning('car_rentals_kyc_veriff_fallback', [
                    'error' => $exception->getMessage(),
                ]);

                $fallback = app(MockThirdPartyKycProvider::class)->verify($payload);
                $fallback['provider_payload'] = [
                    'fallback' => true,
                    'provider' => 'mock',
                    'reason' => $exception->getMessage(),
                ];

                return $fallback;
            }

            throw $exception;
        }
    }
}
