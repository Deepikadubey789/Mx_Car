@extends('core/base::forms.form-tabs')

@section('form_end')
    @php
        $customer = $form->getModel();
        $kyc = null;
        $riskSummary = null;
        $recentRiskBookings = collect();

        if ($customer && $customer->exists) {
            $kyc = $customer->kyc_current_verification_id
                ? $customer->kycVerifications()->where('id', $customer->kyc_current_verification_id)->with('documents')->first()
                : null;

            if (! $kyc) {
                $kyc = $customer->kycVerifications()
                ->with('documents')
                ->latest('id')
                ->first();
            }

            $recentRiskBookings = \Botble\CarRentals\Models\Booking::query()
                ->where('customer_id', $customer->id)
                ->whereNotNull('deposit_risk_level')
                ->latest('id')
                ->take(5)
                ->get();

            $riskSummary = [
                'total_bookings' => (int) \Botble\CarRentals\Models\Booking::query()->where('customer_id', $customer->id)->count(),
                'high_risk_count' => (int) \Botble\CarRentals\Models\Booking::query()
                    ->where('customer_id', $customer->id)
                    ->where('deposit_risk_level', 'high')
                    ->count(),
                'latest' => $recentRiskBookings->first(),
            ];
        }
    @endphp

    @if($customer && $customer->exists)
        <x-core::card class="mt-3">
            <x-core::card.header>
                <x-core::card.title>{{ __('KYC Verification') }}</x-core::card.title>
            </x-core::card.header>

            <x-core::card.body>
                <dl class="row mb-3">
                    <dt class="col-4">{{ __('Status') }}</dt>
                    <dd class="col-8">{{ $customer->kyc_status ?: 'not_submitted' }}</dd>
                    <dt class="col-4">{{ __('Level') }}</dt>
                    <dd class="col-8">{{ $customer->kyc_level ?: '—' }}</dd>
                    <dt class="col-4">{{ __('Last Verified') }}</dt>
                    <dd class="col-8">{{ $customer->kyc_last_verified_at ? $customer->kyc_last_verified_at->format('Y-m-d H:i') : '—' }}</dd>
                    <dt class="col-4">{{ __('Provider Ref') }}</dt>
                    <dd class="col-8">{{ $kyc?->provider_reference ?: '—' }}</dd>
                    <dt class="col-4">{{ __('Submitted At') }}</dt>
                    <dd class="col-8">{{ $kyc?->created_at?->format('Y-m-d H:i') ?: '—' }}</dd>
                    <dt class="col-4">{{ __('Webhook Received') }}</dt>
                    <dd class="col-8">{{ data_get($kyc?->provider_payload, 'last_webhook_at', '—') }}</dd>
                    <dt class="col-4">{{ __('Last Event ID') }}</dt>
                    <dd class="col-8">{{ data_get($kyc?->provider_payload, 'last_event_id', '—') }}</dd>
                    <dt class="col-4">{{ __('Stripe Session Status') }}</dt>
                    <dd class="col-8">{{ data_get($kyc?->provider_payload, 'last_webhook_payload.stripe_identity.status', '—') }}</dd>
                    <dt class="col-4">{{ __('Document Check') }}</dt>
                    <dd class="col-8">
                        {{ data_get($kyc?->provider_payload, 'last_webhook_payload.stripe_identity.report.document.status', '—') }}
                        @if(data_get($kyc?->provider_payload, 'last_webhook_payload.stripe_identity.report.document.error_code'))
                            <span class="text-danger d-block small">{{ data_get($kyc?->provider_payload, 'last_webhook_payload.stripe_identity.report.document.error_code') }}</span>
                        @endif
                    </dd>
                    <dt class="col-4">{{ __('Selfie Check') }}</dt>
                    <dd class="col-8">
                        {{ data_get($kyc?->provider_payload, 'last_webhook_payload.stripe_identity.report.selfie.status', '—') }}
                        @if(data_get($kyc?->provider_payload, 'last_webhook_payload.stripe_identity.report.selfie.error_code'))
                            <span class="text-danger d-block small">{{ data_get($kyc?->provider_payload, 'last_webhook_payload.stripe_identity.report.selfie.error_code') }}</span>
                        @endif
                    </dd>
                </dl>

                @if($kyc)
                    <div class="mb-3">
                        <strong>{{ __('Documents') }}</strong>
                        <ul class="mb-0 mt-2">
                            @forelse($kyc->documents as $document)
                                <li class="mb-1">
                                    <span>{{ ucfirst(str_replace('_', ' ', $document->document_type)) }}:</span>
                                    @if(!empty($document->file_path))
                                        <a href="{{ \Botble\Media\Facades\RvMedia::getImageUrl($document->file_path) }}" target="_blank" rel="noopener noreferrer">
                                            {{ __('View file') }}
                                        </a>
                                    @else
                                        <span class="text-muted">{{ __('File is not available (redacted after processing).') }}</span>
                                    @endif
                                </li>
                            @empty
                                <li>{{ __('No documents uploaded') }}</li>
                            @endforelse
                        </ul>
                    </div>

                    @if(!empty($kyc->decision_reasons))
                        <div class="mb-3">
                            <strong>{{ __('Decision Reasons') }}</strong>
                            <div class="text-muted small">{{ implode(', ', $kyc->decision_reasons) }}</div>
                        </div>
                    @endif

                    @if($kyc->rejection_reason)
                        <div class="mb-3">
                            <strong>{{ __('Rejection Reason') }}</strong>
                            <div class="text-muted small">{{ $kyc->rejection_reason }}</div>
                        </div>
                    @endif
                @else
                    <p class="text-muted mb-3">{{ __('No KYC submission found for this customer.') }}</p>
                @endif

                <form method="POST" action="{{ route('car-rentals.customers.review-kyc', $customer) }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">{{ __('Review Status') }}</label>
                        <select name="status" class="form-select" required>
                            <option value="approved">{{ __('Approve') }}</option>
                            <option value="manual_review">{{ __('Manual Review') }}</option>
                            <option value="rejected">{{ __('Reject') }}</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">{{ __('Reason Code (optional)') }}</label>
                        <select name="reason_code" class="form-select">
                            <option value="">{{ __('Select reason code') }}</option>
                            <option value="selfie_face_mismatch">selfie_face_mismatch</option>
                            <option value="document_expired">document_expired</option>
                            <option value="document_type_not_supported">document_type_not_supported</option>
                            <option value="ocr_required_fields_missing">ocr_required_fields_missing</option>
                            <option value="kyc_pending_review">kyc_pending_review</option>
                            <option value="high_risk_deposit_profile">high_risk_deposit_profile</option>
                            <option value="other">other</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">{{ __('Reason (optional)') }}</label>
                        <textarea name="reason" class="form-control" rows="2" maxlength="500"></textarea>
                    </div>
                    <x-core::button type="submit" color="primary" icon="ti ti-check">{{ __('Update KYC Review') }}</x-core::button>
                </form>
            </x-core::card.body>
        </x-core::card>

        <x-core::card class="mt-3">
            <x-core::card.header>
                <x-core::card.title>{{ __('Risk Scoring') }}</x-core::card.title>
            </x-core::card.header>
            <x-core::card.body>
                @if($riskSummary && $riskSummary['latest'])
                    <dl class="row mb-3">
                        <dt class="col-4">{{ __('Total Bookings') }}</dt>
                        <dd class="col-8">{{ $riskSummary['total_bookings'] }}</dd>

                        <dt class="col-4">{{ __('High-Risk Bookings') }}</dt>
                        <dd class="col-8">{{ $riskSummary['high_risk_count'] }}</dd>

                        <dt class="col-4">{{ __('Latest Risk Level') }}</dt>
                        <dd class="col-8">{{ strtoupper((string) $riskSummary['latest']->deposit_risk_level) }}</dd>

                        <dt class="col-4">{{ __('Latest Multiplier') }}</dt>
                        <dd class="col-8">{{ (float) $riskSummary['latest']->deposit_risk_multiplier }}</dd>

                        <dt class="col-4">{{ __('Latest Deposit') }}</dt>
                        <dd class="col-8">{{ format_price($riskSummary['latest']->deposit_amount, $riskSummary['latest']->currency) }}</dd>
                    </dl>

                    <div class="mb-3">
                        <strong>{{ __('Latest Risk Reasons') }}</strong>
                        <div class="text-muted small">
                            @php $latestReasons = (array) ($riskSummary['latest']->deposit_risk_reasons ?? []); @endphp
                            {{ !empty($latestReasons) ? implode(', ', $latestReasons) : '—' }}
                        </div>
                    </div>

                    <div>
                        <strong>{{ __('Recent Risk Records') }}</strong>
                        <div class="table-responsive mt-2">
                            <table class="table table-sm table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('Booking') }}</th>
                                        <th>{{ __('Risk Level') }}</th>
                                        <th>{{ __('Multiplier') }}</th>
                                        <th>{{ __('Deposit') }}</th>
                                        <th>{{ __('Created') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentRiskBookings as $booking)
                                        <tr>
                                            <td>#{{ $booking->id }}</td>
                                            <td>{{ strtoupper((string) $booking->deposit_risk_level) }}</td>
                                            <td>{{ (float) $booking->deposit_risk_multiplier }}</td>
                                            <td>{{ format_price($booking->deposit_amount, $booking->currency) }}</td>
                                            <td>{{ $booking->created_at?->format('Y-m-d H:i') ?: '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <p class="text-muted mb-0">{{ __('No risk scoring data available for this customer yet.') }}</p>
                @endif
            </x-core::card.body>
        </x-core::card>
    @endif

    {!! apply_filters('car_rentals_customer_form_end', null, $form) !!}
@endsection
