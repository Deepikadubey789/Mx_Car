@extends(CarRentalsHelper::viewPath('customers.layouts.master'))

@section('content')
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/car-rentals/css/overview-custom.css') }}?v={{ filemtime(public_path('vendor/core/plugins/car-rentals/css/overview-custom.css')) }}">
    <style>
        .kyc-upload-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
        }
        .kyc-field-label {
            display: block;
            font-size: 12px;
            color: #4b5563;
            font-weight: 600;
            margin-bottom: 6px;
        }
        .kyc-file-input {
            width: 100%;
            font-size: 13px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 8px;
            background: #fff;
        }
        .kyc-upload-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 8px 12px;
            background: #f8fafc;
            color: #1f2937;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        .kyc-upload-btn:hover {
            background: #f1f5f9;
        }
        .kyc-doc-status-list {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 14px;
        }
        .kyc-doc-status-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 12px;
            font-size: 14px;
            background: #fff;
            border-bottom: 1px solid #f1f5f9;
        }
        .kyc-doc-status-row:last-child {
            border-bottom: 0;
        }
        .kyc-doc-ok {
            color: #16a34a;
            font-weight: 600;
        }
        .kyc-doc-missing {
            color: #6b7280;
            font-weight: 500;
        }
        .kyc-retry-box {
            margin-top: 8px;
        }
        @media (max-width: 900px) {
            .kyc-upload-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="breadcrumb">
        Home &rsaquo; Account &rsaquo; <span>KYC Verification</span>
    </div>

    <div class="content">
        @if(!empty($returnedFromStripeIdentity))
            <div class="alert alert-info mb-3" role="status">
                {{ __('Thanks for submitting your identity document. We are processing your verification; your status will update shortly. You can refresh this page in a few moments.') }}
            </div>
        @endif
        <div class="two-col">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ __('KYC Status') }}</div>
                    <div class="card-action"><a href="{{ route('customer.profile') }}">{{ __('Back to profile') }}</a></div>
                </div>
                <div class="card-body">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px;background:#f5f3ef;border-radius:10px;">
                        <div style="font-size:13px;color:#555;">{{ __('Current status') }}</div>
                        <div style="font-size:12px;font-weight:500;background:{{ $kycDisplay['bg'] }};color:{{ $kycDisplay['color'] }};padding:3px 10px;border-radius:20px;">
                            {{ $kycDisplay['label'] }}
                        </div>
                    </div>
                    <div style="margin-top:8px;font-size:12px;color:#666;">{{ $kycDisplay['note'] }}</div>
                    <div style="margin-top:8px;font-size:12px;color:#555;">
                        <strong>{{ __('KYC level') }}:</strong> {{ $customer->kyc_level ?: '-' }}
                    </div>
                    @if($verification)
                        <div style="margin-top:6px;font-size:12px;color:#555;">
                            <strong>{{ __('Verification ID') }}:</strong> #{{ $verification->id }}
                        </div>
                    @endif
                    @if($customer->kyc_status === 'verified')
                        <div class="alert alert-success mt-3 mb-0">{{ __('Your account is verified.') }}</div>
                    @endif
                </div>
            </div>

            @if(!empty($stripeIdentityModal) && $customer->kyc_status !== 'verified')
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">{{ __('Verify your identity') }}</div>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">{{ __('Click below to verify your passport, driver license, or national ID in a secure Stripe window. Results are applied to your account after processing.') }}</p>
                        @php
                            $uploadedDocs = collect($verification?->documents ?? [])->keyBy('document_type');
                            $requiredDocTypes = [
                                'license_front' => __('License front'),
                                'license_back' => __('License back'),
                                'selfie' => __('Selfie'),
                            ];
                            $missingRequiredDocs = collect($requiredDocTypes)->keys()->filter(fn (string $type) => ! $uploadedDocs->has($type));
                        @endphp
                        <form method="POST" action="{{ route('customer.kyc.upload') }}" enctype="multipart/form-data" class="mb-3">
                            @csrf
                            <div class="kyc-upload-grid">
                                <div>
                                    <label class="kyc-field-label">{{ __('License front') }}</label>
                                    <input type="file" name="license_front" class="kyc-file-input" accept="image/*">
                                </div>
                                <div>
                                    <label class="kyc-field-label">{{ __('License back') }}</label>
                                    <input type="file" name="license_back" class="kyc-file-input" accept="image/*">
                                </div>
                                <div>
                                    <label class="kyc-field-label">{{ __('Selfie') }}</label>
                                    <input type="file" name="selfie" class="kyc-file-input" accept="image/*">
                                </div>
                            </div>
                            <button type="submit" class="kyc-upload-btn mt-3">{{ __('Upload documents') }}</button>
                        </form>
                        <div class="kyc-doc-status-list">
                            @foreach($requiredDocTypes as $docType => $docLabel)
                                <div class="kyc-doc-status-row">
                                    <span>{{ $docLabel }}</span>
                                    <span class="{{ $uploadedDocs->has($docType) ? 'kyc-doc-ok' : 'kyc-doc-missing' }}">
                                        {{ $uploadedDocs->has($docType) ? __('Uploaded') : __('Missing') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                        @php
                            $decisionReasons = collect((array) ($verification?->decision_reasons ?? []));
                            $retryHints = $decisionReasons
                                ->map(function (string $reason): string {
                                    return match ($reason) {
                                        'selfie_face_mismatch' => __('Selfie does not match the face on your document. Retake selfie in good lighting with your full face visible.'),
                                        'selfie_manipulated' => __('Selfie appears manipulated. Capture a fresh, unedited selfie directly from your camera.'),
                                        'selfie_document_missing_photo' => __('Your document does not contain a usable portrait. Try another supported government-issued photo ID.'),
                                        'document_expired' => __('Your document appears expired. Upload a valid, non-expired ID.'),
                                        'document_type_not_supported' => __('Document type is not supported. Use passport, ID card, or driving license.'),
                                        'ocr_required_fields_missing' => __('Some required details could not be read from the document. Retake photos with all text clearly visible.'),
                                        'requires_manual_biometric_review' => __('Selfie verification needs manual review. You can retry with improved lighting, or wait for support review.'),
                                        'kyc_pending_review' => __('Your submission is currently queued for manual review.'),
                                        'high_risk_deposit_profile' => __('Your account is currently flagged as high-risk and may require additional review.'),
                                        'document_unverified_other', 'selfie_unverified_other', 'stripe_verification_failed' => __('Stripe could not verify your submission. Retake clear photos and avoid glare or blur.'),
                                        default => '',
                                    };
                                })
                                ->filter()
                                ->values();
                        @endphp
                        @if($verification && $verification->status === 'manual_review')
                            <div class="alert alert-info small mb-3">
                                {{ __('Your verification is in manual review. You may retry verification if your documents have changed or image quality can be improved.') }}
                            </div>
                        @endif
                        @if($verification && $verification->status === 'pending' && $verification->provider_reference)
                            <div class="alert alert-warning small mb-3">
                                {{ __('Verification is still processing or awaiting an update from Stripe.') }}
                            </div>
                        @endif
                        @if($retryHints->isNotEmpty() && in_array((string) $verification?->status, ['rejected', 'manual_review'], true))
                            <div class="alert alert-danger small mb-3 kyc-retry-box">
                                <div class="fw-semibold mb-1">{{ __('Please fix the following and try again:') }}</div>
                                <ul class="mb-0 ps-3">
                                    @foreach($retryHints as $hint)
                                        <li>{{ $hint }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <script src="https://js.stripe.com/v3/"></script>
                        <button
                            type="button"
                            class="btn btn-success px-4 py-2 fw-semibold"
                            style="min-width: 140px; border-radius: 10px;"
                            id="verify-button"
                            {{ $missingRequiredDocs->isNotEmpty() ? 'disabled' : '' }}
                        >
                            {{ __('Verify identity') }}
                        </button>
                        @if($missingRequiredDocs->isNotEmpty())
                            <div class="small text-muted mt-2">{{ __('Upload all required documents before starting Stripe verification.') }}</div>
                        @endif
                    </div>
                </div>
            @elseif($customer->kyc_status !== 'verified')
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">{{ __('Verify your identity') }}</div>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-warning mb-0">
                            {{ __('Stripe Identity modal is unavailable. Please configure Stripe publishable key in KYC settings to show the Verify button.') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if(!empty($stripeIdentityModal) && $customer->kyc_status !== 'verified')
        {{-- Client flow aligned with https://docs.stripe.com/identity/verify-identity-documents (publishable key from settings, session from Laravel route). --}}
        <script type="text/javascript">
            (function () {
                // Set your publishable key: remember to change this to your live publishable key in production
                // See your keys here: https://dashboard.stripe.com/apikeys
                // Loaded from Car Rentals KYC settings — do not hardcode keys in templates.
                var stripe = Stripe(@json($stripePublishableKey));
                var verifyButton = document.getElementById('verify-button');
                if (!verifyButton) {
                    return;
                }

                var createVerificationSessionUrl = @json($stripeCreateSessionUrl);
                var afterVerifyReturnUrl = @json($stripeReturnUrl);
                var csrfMeta = document.querySelector('meta[name="csrf-token"]');
                var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

                verifyButton.addEventListener('click', function () {
                    verifyButton.disabled = true;
                    fetch(createVerificationSessionUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({})
                    })
                        .then(function (response) {
                            return response.json().then(function (session) {
                                if (!response.ok) {
                                    throw new Error(session.message || 'Request failed');
                                }
                                return session;
                            });
                        })
                        .then(function (session) {
                            // Show the verification modal.
                            return stripe.verifyIdentity(session.client_secret);
                        })
                        .then(function (result) {
                            // If `verifyIdentity` fails, display the localized error message using `error.message`.
                            if (result.error) {
                                alert(result.error.message);
                                return;
                            }
                            window.location.href = afterVerifyReturnUrl;
                        })
                        .catch(function (error) {
                            console.error('Error:', error);
                            alert(error.message || 'Error');
                        })
                        .finally(function () {
                            verifyButton.disabled = false;
                        });
                });
            })();
        </script>
    @endif
@endsection
