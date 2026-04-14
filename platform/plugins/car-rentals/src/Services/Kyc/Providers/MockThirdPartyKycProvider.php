<?php

namespace Botble\CarRentals\Services\Kyc\Providers;

use Botble\CarRentals\Services\Kyc\KycProviderInterface;
use Carbon\Carbon;

class MockThirdPartyKycProvider implements KycProviderInterface
{
    public function verify(array $payload): array
    {
        $licenseNumber = (string) data_get($payload, 'license_number', '');
        $selfiePath = (string) data_get($payload, 'selfie_path', '');
        $frontPath = (string) data_get($payload, 'license_front_path', '');

        $ocrConfidence = $licenseNumber !== '' ? 0.93 : 0.58;
        $faceMatch = ($selfiePath !== '' && $frontPath !== '') ? 0.87 : 0.45;
        $isLicenseValid = strlen($licenseNumber) >= 6;
        $expiryDate = Carbon::now()->addYears(3)->toDateString();

        return [
            'provider_reference' => 'kyc-' . uniqid(),
            'license_valid' => $isLicenseValid,
            'license_number' => $licenseNumber,
            'license_expiry_date' => $expiryDate,
            'ocr_confidence_score' => $ocrConfidence,
            'face_match_score' => $faceMatch,
            'risk_score' => round((1 - min($faceMatch, 1)) + (1 - min($ocrConfidence, 1)), 4),
            'ocr_payload' => [
                'raw_license_number' => $licenseNumber,
                'issuer' => 'mock-provider',
            ],
            'provider_payload' => [
                'source' => 'mock-third-party',
                'evaluated_at' => Carbon::now()->toIso8601String(),
            ],
        ];
    }
}
