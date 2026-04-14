<?php

namespace Botble\CarRentals\Services\Kyc;

use Carbon\Carbon;

class KycDecisionService
{
    public function decide(array $providerResult): array
    {
        $faceThreshold = $this->getFloatSetting('kyc_face_match_threshold', 0.8);
        $ocrThreshold = $this->getFloatSetting('kyc_ocr_confidence_threshold', 0.8);
        $manualReviewRiskThreshold = $this->getFloatSetting('kyc_manual_review_risk_threshold', 0.5);

        $hasFaceMatchScore = data_get($providerResult, 'face_match_score') !== null;
        $hasOcrConfidenceScore = data_get($providerResult, 'ocr_confidence_score') !== null;
        $hasLicenseValidFlag = data_get($providerResult, 'license_valid') !== null;

        $faceMatch = (float) data_get($providerResult, 'face_match_score', 0);
        $ocrConfidence = (float) data_get($providerResult, 'ocr_confidence_score', 0);
        $riskScore = (float) data_get($providerResult, 'risk_score', 1);
        $licenseValid = (bool) data_get($providerResult, 'license_valid', false);
        $expiryDate = data_get($providerResult, 'license_expiry_date');
        $documentStatus = (string) data_get($providerResult, 'stripe_identity.report.document.status', '');
        $selfieStatus = (string) data_get($providerResult, 'stripe_identity.report.selfie.status', '');
        $documentErrorCode = (string) data_get($providerResult, 'stripe_identity.report.document.error_code', '');
        $selfieErrorCode = (string) data_get($providerResult, 'stripe_identity.report.selfie.error_code', '');
        $ocrFields = (array) data_get($providerResult, 'ocr_payload.extracted_fields', []);
        $ocrSource = (string) data_get($providerResult, 'ocr_payload.source', '');
        $requiresInput = (string) data_get($providerResult, 'status', '') === 'requires_input'
            || (string) data_get($providerResult, 'stripe_identity.status', '') === 'requires_input';

        $reasons = [];

        if ($hasLicenseValidFlag && ! $licenseValid) {
            $reasons[] = 'license_invalid';
        }

        if ($hasFaceMatchScore && $faceMatch < $faceThreshold) {
            $reasons[] = 'face_match_below_threshold';
        }

        if ($hasOcrConfidenceScore && $ocrConfidence < $ocrThreshold) {
            $reasons[] = 'ocr_confidence_below_threshold';
        }

        if ($this->hasMissingRequiredOcrFields($ocrFields)) {
            $reasons[] = $ocrSource === 'stripe'
                ? 'stripe_ocr_required_fields_missing'
                : 'ocr_required_fields_missing';
        }

        if ($expiryDate && Carbon::parse($expiryDate)->isPast()) {
            $reasons[] = 'license_expired';
        }

        if (in_array($documentStatus, ['unverified', 'requires_input'], true)) {
            $reasons[] = $documentErrorCode !== '' ? $documentErrorCode : 'document_unverified_other';
        }

        if (in_array($selfieStatus, ['unverified', 'requires_input'], true)) {
            $reasons[] = $selfieErrorCode !== '' ? $selfieErrorCode : 'selfie_unverified_other';
        }

        $status = 'approved';

        if (! empty(array_intersect($reasons, ['license_invalid', 'license_expired', 'document_expired', 'document_type_not_supported']))) {
            $status = 'rejected';
        } elseif ($requiresInput) {
            $status = 'manual_review';
        } elseif (($riskScore >= $manualReviewRiskThreshold) || ! empty($reasons)) {
            $status = 'manual_review';
        }

        return [
            'status' => $status,
            'reasons' => array_values(array_unique($reasons)),
        ];
    }

    protected function hasMissingRequiredOcrFields(array $ocrFields): bool
    {
        if (empty($ocrFields)) {
            return false;
        }

        $required = ['license_number', 'full_name', 'expiration_date', 'issuing_country'];

        foreach ($required as $field) {
            $value = data_get($ocrFields, $field);
            if (! is_string($value) || trim($value) === '') {
                return true;
            }
        }

        return false;
    }

    protected function getFloatSetting(string $key, float $default): float
    {
        $value = (float) $this->getSetting($key, $default);

        return $value > 0 ? $value : $default;
    }

    protected function getSetting(string $key, mixed $default = null): mixed
    {
        try {
            if (function_exists('get_car_rentals_setting')) {
                return get_car_rentals_setting($key, $default);
            }

            if (class_exists(\Botble\CarRentals\Facades\CarRentalsHelper::class)) {
                return \Botble\CarRentals\Facades\CarRentalsHelper::getSetting($key, $default);
            }
        } catch (\Throwable) {
            return $default;
        }

        return $default;
    }
}
