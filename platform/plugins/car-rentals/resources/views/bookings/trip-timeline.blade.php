@php
    use Botble\Media\Facades\RvMedia;

    $uid = 'casefile-' . ($booking->getKey() ?: 'new');
    $claims = $claims ?? collect();
    $claimStatuses = $claimStatuses ?? ['open', 'under_review', 'resolved', 'rejected'];
    $assignees = $assignees ?? collect();

    $categoryIcons = [
        'chat' => 'ti-messages',
        'verification' => 'ti-id',
        'media' => 'ti-photo',
        'damage' => 'ti-car-crash',
        'payment' => 'ti-credit-card',
        'booking_lifecycle' => 'ti-calendar-event',
        'support_action' => 'ti-headset',
    ];
    $categoryLabels = [
        'chat' => __('plugins/car-rentals::disputes.category_chat'),
        'verification' => __('plugins/car-rentals::disputes.category_verification'),
        'media' => __('plugins/car-rentals::disputes.category_media'),
        'damage' => __('plugins/car-rentals::disputes.category_damage'),
        'payment' => __('plugins/car-rentals::disputes.category_payment'),
        'booking_lifecycle' => __('plugins/car-rentals::disputes.category_booking_lifecycle'),
        'support_action' => __('plugins/car-rentals::disputes.category_support_action'),
    ];

    $pickupPhotos = $booking->pickup_photos ?? [];
    $returnPhotos = $booking->after_photos ?? [];
    $damagePhotos = $booking->completion_damage_images ?? [];
    $kyc = $booking->kyc_verification_id ? $booking->kycVerification : null;
    $kycValid = $kyc && $kyc->getKey();
@endphp

<div class="booking-casefile card border shadow-sm" id="trip-timeline-casefile">
    <div class="card-header bg-body-tertiary border-bottom py-2 px-3">
        <ul class="nav nav-tabs nav-tabs-card flex-nowrap border-0 gap-1 mt-2" role="tablist">
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link active text-nowrap px-3 py-2"
                    id="{{ $uid }}-tab-timeline"
                    data-bs-toggle="tab"
                    data-bs-target="#{{ $uid }}-pane-timeline"
                    type="button"
                    role="tab"
                    aria-controls="{{ $uid }}-pane-timeline"
                    aria-selected="true"
                >
                    <x-core::icon name="ti ti-history" class="me-1" />
                    {{ __('plugins/car-rentals::disputes.tab_timeline') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link text-nowrap px-3 py-2"
                    id="{{ $uid }}-tab-evidence"
                    data-bs-toggle="tab"
                    data-bs-target="#{{ $uid }}-pane-evidence"
                    type="button"
                    role="tab"
                    aria-controls="{{ $uid }}-pane-evidence"
                    aria-selected="false"
                >
                    <x-core::icon name="ti ti-photo" class="me-1" />
                    {{ __('plugins/car-rentals::disputes.tab_evidence') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link text-nowrap px-3 py-2"
                    id="{{ $uid }}-tab-charges"
                    data-bs-toggle="tab"
                    data-bs-target="#{{ $uid }}-pane-charges"
                    type="button"
                    role="tab"
                    aria-controls="{{ $uid }}-pane-charges"
                    aria-selected="false"
                >
                    <x-core::icon name="ti ti-receipt" class="me-1" />
                    {{ __('plugins/car-rentals::disputes.tab_charges') }}
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    class="nav-link text-nowrap px-3 py-2"
                    id="{{ $uid }}-tab-claims"
                    data-bs-toggle="tab"
                    data-bs-target="#{{ $uid }}-pane-claims"
                    type="button"
                    role="tab"
                    aria-controls="{{ $uid }}-pane-claims"
                    aria-selected="false"
                >
                    <x-core::icon name="ti ti-gavel" class="me-1" />
                    {{ __('plugins/car-rentals::disputes.tab_claims') }}
                </button>
            </li>
        </ul>
    </div>
    <div class="card-body p-0 tab-content">
        <div
            class="tab-pane fade show active"
            id="{{ $uid }}-pane-timeline"
            role="tabpanel"
            aria-labelledby="{{ $uid }}-tab-timeline"
            tabindex="0"
        >
            <div class="p-3" style="max-height: min(52vh, 420px); overflow-y: auto;">
                <div class="mb-0">
                    @forelse ($timeline as $row)
                        @php
                            $cat = $row['category'] ?? 'booking_lifecycle';
                            $icon = $categoryIcons[$cat] ?? 'ti-point';
                        @endphp
                        <div class="border-start border-primary border-2 ps-3 pb-3 ms-1">
                            <div class="position-relative" style="margin-top: -2px;">
                                <span class="position-absolute top-0 start-0 translate-middle-x bg-primary rounded-circle p-1 d-inline-flex" style="left: -1.15rem !important;">
                                    <x-core::icon :name="'ti ' . $icon" class="text-white" style="width: 14px; height: 14px;" />
                                </span>
                                <div class="text-muted small">
                                    {{ \Carbon\Carbon::parse($row['occurred_at'])->timezone(config('app.timezone'))->format('M j, Y g:i A') }}
                                    · {{ $categoryLabels[$cat] ?? $cat }}
                                </div>
                                <div class="fw-semibold">{{ $row['title'] }}</div>
                                <div class="text-secondary small mt-1">{{ $row['summary'] }}</div>
                                @if (! empty($row['metadata']['evidence_provenance']))
                                    @php($prov = $row['metadata']['evidence_provenance'])
                                    <div class="small text-muted mt-1">
                                        Provenance: {{ $prov['source'] ?? 'system' }}
                                        @if (! empty($prov['recorded_at']))
                                            · {{ $prov['recorded_at'] }}
                                        @endif
                                        @if (! empty($prov['confidence']))
                                            · {{ $prov['confidence'] }}
                                        @endif
                                    </div>
                                @endif
                                @if (! empty($row['actor']['name']))
                                    <div class="small text-muted mt-1">
                                        {{ $row['actor']['name'] }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-muted">{{ __('No timeline events yet.') }}</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div
            class="tab-pane fade"
            id="{{ $uid }}-pane-evidence"
            role="tabpanel"
            aria-labelledby="{{ $uid }}-tab-evidence"
            tabindex="0"
        >
            <div class="p-3" style="max-height: min(52vh, 480px); overflow-y: auto;">
                <div class="accordion accordion-flush booking-casefile-accordion" id="{{ $uid }}-evidence-acc">
                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button py-2 px-0 shadow-none bg-transparent {{ count($pickupPhotos) ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $uid }}-ev-pickup" aria-expanded="{{ count($pickupPhotos) ? 'true' : 'false' }}">
                                <span class="small fw-semibold text-uppercase text-muted">{{ __('plugins/car-rentals::disputes.pickup_photos') }}</span>
                                <span class="badge bg-secondary ms-2">{{ count($pickupPhotos) }}</span>
                            </button>
                        </h2>
                        <div id="{{ $uid }}-ev-pickup" class="accordion-collapse collapse {{ count($pickupPhotos) ? 'show' : '' }}" data-bs-parent="#{{ $uid }}-evidence-acc">
                            <div class="accordion-body pt-0 pb-3 px-0">
                                <div class="row g-2">
                                    @forelse ($pickupPhotos as $url)
                                        <div class="col-6 col-sm-4 col-md-3">
                                            <a href="{{ RvMedia::url($url) }}" target="_blank" rel="noopener" class="d-block ratio ratio-4x3">
                                                <img src="{{ RvMedia::getImageUrl($url, 'thumb', false, RvMedia::getDefaultImage()) }}" class="rounded border object-fit-cover" alt="">
                                            </a>
                                        </div>
                                    @empty
                                        <div class="text-muted small">{{ __('None') }}</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button py-2 px-0 shadow-none bg-transparent {{ count($returnPhotos) ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $uid }}-ev-return" aria-expanded="{{ count($returnPhotos) ? 'true' : 'false' }}">
                                <span class="small fw-semibold text-uppercase text-muted">{{ __('plugins/car-rentals::disputes.return_photos') }}</span>
                                <span class="badge bg-secondary ms-2">{{ count($returnPhotos) }}</span>
                            </button>
                        </h2>
                        <div id="{{ $uid }}-ev-return" class="accordion-collapse collapse {{ count($returnPhotos) ? 'show' : '' }}" data-bs-parent="#{{ $uid }}-evidence-acc">
                            <div class="accordion-body pt-0 pb-3 px-0">
                                <div class="row g-2">
                                    @forelse ($returnPhotos as $url)
                                        <div class="col-6 col-sm-4 col-md-3">
                                            <a href="{{ RvMedia::url($url) }}" target="_blank" rel="noopener" class="d-block ratio ratio-4x3">
                                                <img src="{{ RvMedia::getImageUrl($url, 'thumb', false, RvMedia::getDefaultImage()) }}" class="rounded border object-fit-cover" alt="">
                                            </a>
                                        </div>
                                    @empty
                                        <div class="text-muted small">{{ __('None') }}</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0 border-bottom">
                        <h2 class="accordion-header">
                            <button class="accordion-button py-2 px-0 shadow-none bg-transparent {{ count($damagePhotos) ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $uid }}-ev-damage" aria-expanded="{{ count($damagePhotos) ? 'true' : 'false' }}">
                                <span class="small fw-semibold text-uppercase text-muted">{{ __('plugins/car-rentals::disputes.damage_photos') }}</span>
                                <span class="badge bg-secondary ms-2">{{ count($damagePhotos) }}</span>
                            </button>
                        </h2>
                        <div id="{{ $uid }}-ev-damage" class="accordion-collapse collapse {{ count($damagePhotos) ? 'show' : '' }}" data-bs-parent="#{{ $uid }}-evidence-acc">
                            <div class="accordion-body pt-0 pb-3 px-0">
                                <div class="row g-2">
                                    @forelse ($damagePhotos as $url)
                                        <div class="col-6 col-sm-4 col-md-3">
                                            <a href="{{ RvMedia::url($url) }}" target="_blank" rel="noopener" class="d-block ratio ratio-4x3">
                                                <img src="{{ RvMedia::getImageUrl($url, 'thumb', false, RvMedia::getDefaultImage()) }}" class="rounded border object-fit-cover" alt="">
                                            </a>
                                        </div>
                                    @empty
                                        <div class="text-muted small">{{ __('None') }}</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item border-0">
                        <h2 class="accordion-header">
                            <button class="accordion-button py-2 px-0 shadow-none bg-transparent {{ $kycValid ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#{{ $uid }}-ev-kyc" aria-expanded="{{ $kycValid ? 'true' : 'false' }}">
                                <span class="small fw-semibold text-uppercase text-muted">{{ __('plugins/car-rentals::disputes.kyc_heading') }}</span>
                                @if ($kycValid)
                                    <span class="badge bg-info ms-2">{{ $kyc->status }}</span>
                                @endif
                            </button>
                        </h2>
                        <div id="{{ $uid }}-ev-kyc" class="accordion-collapse collapse {{ $kycValid ? 'show' : '' }}" data-bs-parent="#{{ $uid }}-evidence-acc">
                            <div class="accordion-body pt-0 pb-2 px-0">
                                @if ($kycValid)
                                    <dl class="row small mb-2 gx-2">
                                        <dt class="col-sm-4 text-muted">{{ __('plugins/car-rentals::disputes.kyc_status') }}</dt>
                                        <dd class="col-sm-8 mb-1">{{ $kyc->status }}</dd>
                                        @if ($kyc->reviewed_at)
                                            <dt class="col-sm-4 text-muted">{{ __('plugins/car-rentals::disputes.kyc_reviewed_at') }}</dt>
                                            <dd class="col-sm-8 mb-1">{{ $kyc->reviewed_at }}</dd>
                                        @endif
                                        @if ($kyc->license_expiry_date)
                                            <dt class="col-sm-4 text-muted">{{ __('plugins/car-rentals::disputes.kyc_license_expiry') }}</dt>
                                            <dd class="col-sm-8 mb-1">{{ $kyc->license_expiry_date }}</dd>
                                        @endif
                                    </dl>
                                    @if ($kyc->documents?->isNotEmpty())
                                        <div class="text-muted small mb-2">{{ __('plugins/car-rentals::disputes.documents') }}</div>
                                        <div class="row g-2">
                                            @foreach ($kyc->documents as $doc)
                                                <div class="col-6 col-sm-4 col-md-3">
                                                    <a href="{{ RvMedia::url($doc->file_path) }}" target="_blank" rel="noopener" class="d-block ratio ratio-3x4">
                                                        <img src="{{ RvMedia::getImageUrl($doc->file_path, 'thumb', false, RvMedia::getDefaultImage()) }}" class="rounded border object-fit-cover" alt="{{ $doc->document_type }}">
                                                    </a>
                                                    <div class="small text-muted text-truncate mt-1" title="{{ $doc->document_type }}">{{ $doc->document_type }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @else
                                    <div class="text-muted small">{{ __('plugins/car-rentals::disputes.kyc_none') }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div
            class="tab-pane fade"
            id="{{ $uid }}-pane-charges"
            role="tabpanel"
            aria-labelledby="{{ $uid }}-tab-charges"
            tabindex="0"
        >
            <div class="table-responsive" style="max-height: min(52vh, 440px);">
                <table class="table table-sm table-striped mb-0 align-middle">
                    <tbody>
                        <tr>
                            <td class="text-muted small">{{ __('plugins/car-rentals::disputes.charge_sub_total') }}</td>
                            <td class="text-end small fw-medium">{{ format_price($booking->sub_total ?? 0, $booking->currency_id) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">{{ __('plugins/car-rentals::disputes.charge_tax') }}</td>
                            <td class="text-end small fw-medium">{{ format_price($booking->tax_amount ?? 0, $booking->currency_id) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">{{ __('plugins/car-rentals::disputes.charge_coupon') }}</td>
                            <td class="text-end small fw-medium">-{{ format_price($booking->coupon_amount ?? 0, $booking->currency_id) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">{{ __('plugins/car-rentals::disputes.charge_fee') }}</td>
                            <td class="text-end small fw-medium">{{ format_price($booking->fee_amount ?? 0, $booking->currency_id) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">{{ __('plugins/car-rentals::disputes.charge_deposit') }}</td>
                            <td class="text-end small fw-medium">{{ format_price($booking->deposit_amount ?? 0, $booking->currency_id) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">{{ __('plugins/car-rentals::disputes.charge_deposit_hold') }}</td>
                            <td class="text-end small fw-medium">{{ format_price($booking->deposit_hold_amount ?? 0, $booking->currency_id) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">{{ __('plugins/car-rentals::disputes.charge_deposit_hold_status') }}</td>
                            <td class="text-end small text-break">{{ $booking->deposit_hold_status ?: '—' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">{{ __('plugins/car-rentals::disputes.charge_deposit_captured') }}</td>
                            <td class="text-end small fw-medium">{{ format_price($booking->deposit_captured_amount ?? 0, $booking->currency_id) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">{{ __('plugins/car-rentals::disputes.charge_deposit_released') }}</td>
                            <td class="text-end small fw-medium">{{ format_price($booking->deposit_released_amount ?? 0, $booking->currency_id) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">{{ __('plugins/car-rentals::disputes.charge_damage') }}</td>
                            <td class="text-end small fw-medium">{{ format_price($booking->damage_amount ?? 0, $booking->currency_id) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">{{ __('plugins/car-rentals::disputes.charge_fuel') }}</td>
                            <td class="text-end small fw-medium">{{ format_price($booking->fuel_difference_charge ?? 0, $booking->currency_id) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">{{ __('plugins/car-rentals::disputes.charge_late') }}</td>
                            <td class="text-end small fw-medium">{{ format_price($booking->late_fee_charge ?? 0, $booking->currency_id) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted small">{{ __('plugins/car-rentals::disputes.charge_distance_overage') }}</td>
                            <td class="text-end small fw-medium">{{ format_price($booking->distance_overage_amount ?? 0, $booking->currency_id) }}</td>
                        </tr>
                        <tr class="table-light">
                            <td class="fw-semibold">{{ __('plugins/car-rentals::disputes.charge_total') }}</td>
                            <td class="text-end fw-semibold">{{ format_price($booking->amount ?? 0, $booking->currency_id) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div
            class="tab-pane fade"
            id="{{ $uid }}-pane-claims"
            role="tabpanel"
            aria-labelledby="{{ $uid }}-tab-claims"
            tabindex="0"
        >
            <div class="p-3" style="max-height: min(52vh, 520px); overflow-y: auto;">
                <div class="card mb-3 border">
                    <div class="card-header py-2">
                        <strong>{{ __('plugins/car-rentals::disputes.claims_new') }}</strong>
                    </div>
                    <div class="card-body pb-2">
                        <div class="row g-2 claim-create-form" data-url="{{ route('car-rentals.bookings.claims.store', $booking->id) }}">
                            <div class="col-md-3">
                                <label class="form-label small">{{ __('plugins/car-rentals::disputes.claim_category') }}</label>
                                <input type="text" class="form-control form-control-sm" name="claim_category" value="{{ old('category', 'damage') }}" maxlength="60" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">{{ __('plugins/car-rentals::disputes.claim_claimed_amount') }}</label>
                                <input type="number" class="form-control form-control-sm" name="claim_claimed_amount" min="0" step="0.01" value="{{ old('claimed_amount') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">{{ __('plugins/car-rentals::disputes.claim_status') }}</label>
                                <select class="form-select form-select-sm" name="claim_status">
                                    @foreach ($claimStatuses as $status)
                                        <option value="{{ $status }}">{{ str($status)->replace('_', ' ')->title() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">{{ __('plugins/car-rentals::disputes.claim_priority') }}</label>
                                <select class="form-select form-select-sm" name="claim_priority">
                                    @foreach ($claimPriorities as $priority)
                                        <option value="{{ $priority }}" @selected($priority === 'normal')>{{ __('plugins/car-rentals::disputes.priority_' . $priority) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">{{ __('plugins/car-rentals::disputes.claim_assignee') }}</label>
                                <select class="form-select form-select-sm" name="claim_assignee_id">
                                    <option value="">{{ __('plugins/car-rentals::disputes.claim_unassigned') }}</option>
                                    @foreach ($assignees as $assignee)
                                        <option value="{{ $assignee->id }}">
                                            {{ trim(($assignee->first_name ?? '') . ' ' . ($assignee->last_name ?? '')) ?: ($assignee->email ?: ('#' . $assignee->id)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label small">{{ __('plugins/car-rentals::disputes.claim_reason') }}</label>
                                <textarea class="form-control form-control-sm" name="claim_reason" rows="2" maxlength="5000" required>{{ old('reason') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">{{ __('plugins/car-rentals::disputes.outcome_action') }}</label>
                                <select class="form-select form-select-sm" name="claim_outcome_action">
                                    @foreach ($claimOutcomes as $outcome)
                                        <option value="{{ $outcome }}">{{ __('plugins/car-rentals::disputes.outcome_' . $outcome) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small">{{ __('plugins/car-rentals::disputes.claim_resolution_due') }}</label>
                                <input type="datetime-local" class="form-control form-control-sm" name="claim_resolution_due_at">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="1" id="{{ $uid }}-claim-requires-docs" name="claim_requires_docs">
                                    <label class="form-check-label small" for="{{ $uid }}-claim-requires-docs">
                                        Requires additional docs
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 text-end">
                                <button type="button" class="btn btn-sm btn-primary claim-create-submit">
                                    <x-core::icon name="ti ti-plus" class="me-1" />
                                    {{ __('plugins/car-rentals::disputes.claim_create') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border">
                    <div class="card-header py-2">
                        <strong>{{ __('plugins/car-rentals::disputes.claims_existing') }}</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>{{ __('plugins/car-rentals::disputes.claim_category') }}</th>
                                        <th>{{ __('plugins/car-rentals::disputes.claim_status') }}</th>
                                        <th>{{ __('plugins/car-rentals::disputes.claim_assignee') }}</th>
                                        <th class="text-end">{{ __('plugins/car-rentals::disputes.claim_claimed_amount') }}</th>
                                        <th class="text-end">{{ __('plugins/car-rentals::disputes.claim_approved_amount') }}</th>
                                        <th>{{ __('plugins/car-rentals::disputes.claim_resolution_note') }}</th>
                                        <th class="text-end">{{ __('plugins/car-rentals::disputes.claim_actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($claims as $claim)
                                        <tr>
                                            <td class="text-nowrap">{{ $claim->category ?: '—' }}</td>
                                            <td class="text-nowrap">
                                                <span class="badge bg-secondary text-white">{{ str($claim->status)->replace('_', ' ')->title() }}</span>
                                            </td>
                                            <td class="text-nowrap">{{ $claim->assignee?->name ?: __('plugins/car-rentals::disputes.claim_unassigned') }}</td>
                                            <td class="text-end text-nowrap">{{ $claim->claimed_amount !== null ? format_price($claim->claimed_amount, $booking->currency_id) : '—' }}</td>
                                            <td class="text-end text-nowrap">{{ $claim->approved_amount !== null ? format_price($claim->approved_amount, $booking->currency_id) : '—' }}</td>
                                            <td style="min-width: 220px;">
                                                <div class="small text-muted text-break">
                                                    {{ $claim->resolution_note ?: '—' }}
                                                </div>
                                            </td>
                                            <td class="text-end">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-primary claim-open-update-modal"
                                                    data-url="{{ route('car-rentals.bookings.claims.update', [$booking->id, $claim->id]) }}"
                                                    data-claim-id="{{ $claim->id }}"
                                                    data-status="{{ $claim->status }}"
                                                    data-assignee-id="{{ $claim->assignee_id }}"
                                                    data-approved-amount="{{ $claim->approved_amount }}"
                                                    data-reason="{{ e($claim->reason ?? '') }}"
                                                    data-resolution-note="{{ e($claim->resolution_note ?? '') }}"
                                                    data-category="{{ e($claim->category ?? '') }}"
                                                    data-priority="{{ e($claim->priority ?? 'normal') }}"
                                                    data-outcome-action="{{ e($claim->outcome_action ?? 'manual_only') }}"
                                                    data-resolution-due-at="{{ optional($claim->resolution_due_at)->format('Y-m-d\TH:i') }}"
                                                    data-escalated="{{ $claim->escalated_at ? '1' : '0' }}"
                                                >
                                                    {{ __('plugins/car-rentals::disputes.claim_update') }}
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-3">
                                                {{ __('plugins/car-rentals::disputes.claims_none') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="{{ $uid }}-claim-update-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('plugins/car-rentals::disputes.claim_update') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
            </div>
            <div class="modal-body">
                <div class="claim-modal-form" data-url="">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small">{{ __('plugins/car-rentals::disputes.claim_status') }}</label>
                            <select class="form-select form-select-sm" name="claim_status">
                                @foreach ($claimStatuses as $status)
                                    <option value="{{ $status }}">{{ str($status)->replace('_', ' ')->title() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">{{ __('plugins/car-rentals::disputes.claim_assignee') }}</label>
                            <select class="form-select form-select-sm" name="claim_assignee_id">
                                <option value="">{{ __('plugins/car-rentals::disputes.claim_unassigned') }}</option>
                                @foreach ($assignees as $assignee)
                                    <option value="{{ $assignee->id }}">
                                        {{ trim(($assignee->first_name ?? '') . ' ' . ($assignee->last_name ?? '')) ?: ($assignee->email ?: ('#' . $assignee->id)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">{{ __('plugins/car-rentals::disputes.claim_category') }}</label>
                            <input type="text" class="form-control form-control-sm" name="claim_category" maxlength="60">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">{{ __('plugins/car-rentals::disputes.claim_priority') }}</label>
                            <select class="form-select form-select-sm" name="claim_priority">
                                @foreach ($claimPriorities as $priority)
                                    <option value="{{ $priority }}">{{ __('plugins/car-rentals::disputes.priority_' . $priority) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">{{ __('plugins/car-rentals::disputes.claim_approved_amount') }}</label>
                            <input type="number" class="form-control form-control-sm" name="claim_approved_amount" min="0" step="0.01">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">{{ __('plugins/car-rentals::disputes.outcome_action') }}</label>
                            <select class="form-select form-select-sm" name="claim_outcome_action">
                                @foreach ($claimOutcomes as $outcome)
                                    <option value="{{ $outcome }}">{{ __('plugins/car-rentals::disputes.outcome_' . $outcome) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">{{ __('plugins/car-rentals::disputes.claim_resolution_due') }}</label>
                            <input type="datetime-local" class="form-control form-control-sm" name="claim_resolution_due_at">
                        </div>
                        <div class="col-12">
                            <label class="form-label small">{{ __('plugins/car-rentals::disputes.claim_reason') }}</label>
                            <input type="text" class="form-control form-control-sm" name="claim_reason" maxlength="5000">
                        </div>
                        <div class="col-12">
                            <label class="form-label small">{{ __('plugins/car-rentals::disputes.claim_resolution_note') }}</label>
                            <textarea class="form-control form-control-sm" name="claim_resolution_note" rows="3" maxlength="5000"></textarea>
                        </div>
                        <div class="col-12 d-flex align-items-center gap-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="{{ $uid }}-claim-escalated" name="claim_escalated">
                                <label class="form-check-label small" for="{{ $uid }}-claim-escalated">
                                    {{ __('plugins/car-rentals::disputes.claim_escalated') }}
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="{{ $uid }}-claim-requires-docs-modal" name="claim_requires_docs">
                                <label class="form-check-label small" for="{{ $uid }}-claim-requires-docs-modal">
                                    Requires additional docs
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary btn-sm claim-modal-save">{{ __('plugins/car-rentals::disputes.claim_update') }}</button>
            </div>
        </div>
    </div>
</div>

<style>
    .booking-casefile .nav-tabs-card .nav-link {
        border: 1px solid transparent;
        border-radius: 0.375rem;
        color: var(--bs-secondary-color, #6c757d);
        background: transparent;
    }
    .booking-casefile .nav-tabs-card .nav-link:hover {
        color: var(--bs-body-color, inherit);
        background: rgba(0, 0, 0, 0.04);
    }
    .booking-casefile .nav-tabs-card .nav-link.active {
        color: var(--bs-primary, #0d6efd);
        background: var(--bs-body-bg, #fff);
        border-color: var(--bs-border-color, #dee2e6);
    }
    .booking-casefile-accordion .accordion-button:not(.collapsed) {
        box-shadow: none;
    }
    .booking-casefile-accordion .accordion-button::after {
        margin-left: auto;
    }
</style>

<script>
    (() => {
        const casefile = document.getElementById('trip-timeline-casefile');
        if (!casefile || casefile.dataset.claimsBound === '1') {
            return;
        }
        casefile.dataset.claimsBound = '1';

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
            || document.querySelector('input[name="_token"]')?.value
            || '';

        const headers = {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            ...(csrfToken ? {'X-CSRF-TOKEN': csrfToken} : {}),
        };

        const submitClaim = async (url, payload, method = 'POST') => {
            const effectiveMethod = (method || 'POST').toUpperCase();
            const response = await fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers,
                body: JSON.stringify({
                    ...payload,
                    _token: csrfToken,
                    _method: effectiveMethod,
                }),
            });

            const json = await response.json().catch(() => ({}));
            if (!response.ok || json.error) {
                const message = json.message || 'Unable to update claim.';
                throw new Error(message);
            }

            return json;
        };

        const reloadCasefileWithoutDirtyPrompt = () => {
            window.onbeforeunload = null;
            document.querySelectorAll('form.dirty-check').forEach((form) => {
                form.classList.remove('dirty');
            });
            window.location.hash = 'trip-timeline-casefile';
            window.location.reload();
        };

        const updateModalEl = document.getElementById('{{ $uid }}-claim-update-modal');
        const bootstrapApi = window.bootstrap || null;
        const updateModal = (updateModalEl && bootstrapApi?.Modal) ? new bootstrapApi.Modal(updateModalEl) : null;
        const modalForm = updateModalEl?.querySelector('.claim-modal-form');

        const hideUpdateModalFallback = () => {
            if (!updateModalEl) {
                return;
            }

            updateModalEl.style.display = 'none';
            updateModalEl.classList.remove('show');
            updateModalEl.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-open');
        };

        updateModalEl?.querySelectorAll('[data-bs-dismiss="modal"]').forEach((btn) => {
            btn.addEventListener('click', (event) => {
                // If Bootstrap JS isn't available, the built-in `data-bs-dismiss` won't run.
                if (!window.bootstrap?.Modal) {
                    event.preventDefault();
                    hideUpdateModalFallback();
                }
            });
        });

        casefile.querySelector('.claim-create-submit')?.addEventListener('click', async (event) => {
            event.preventDefault();
            const wrapper = casefile.querySelector('.claim-create-form');
            if (!wrapper) {
                return;
            }

            const payload = {
                category: wrapper.querySelector('[name="claim_category"]')?.value || '',
                claimed_amount: wrapper.querySelector('[name="claim_claimed_amount"]')?.value || null,
                reason: wrapper.querySelector('[name="claim_reason"]')?.value || '',
                assignee_id: wrapper.querySelector('[name="claim_assignee_id"]')?.value || null,
                status: wrapper.querySelector('[name="claim_status"]')?.value || 'open',
                priority: wrapper.querySelector('[name="claim_priority"]')?.value || 'normal',
                outcome_action: wrapper.querySelector('[name="claim_outcome_action"]')?.value || 'manual_only',
                resolution_due_at: wrapper.querySelector('[name="claim_resolution_due_at"]')?.value || null,
                requires_additional_docs: wrapper.querySelector('[name="claim_requires_docs"]')?.checked ? 1 : 0,
            };

            try {
                await submitClaim(wrapper.dataset.url, payload, 'POST');
                reloadCasefileWithoutDirtyPrompt();
            } catch (error) {
                alert(error.message || 'Unable to create claim.');
            }
        });

        casefile.querySelectorAll('.claim-open-update-modal').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                if (!updateModalEl || !modalForm) {
                    return;
                }

                modalForm.dataset.url = button.dataset.url || '';
                modalForm.querySelector('[name="claim_status"]').value = button.dataset.status || 'open';
                modalForm.querySelector('[name="claim_assignee_id"]').value = button.dataset.assigneeId || '';
                modalForm.querySelector('[name="claim_approved_amount"]').value = button.dataset.approvedAmount || '';
                modalForm.querySelector('[name="claim_reason"]').value = button.dataset.reason || '';
                modalForm.querySelector('[name="claim_resolution_note"]').value = button.dataset.resolutionNote || '';
                modalForm.querySelector('[name="claim_category"]').value = button.dataset.category || '';
                modalForm.querySelector('[name="claim_priority"]').value = button.dataset.priority || 'normal';
                modalForm.querySelector('[name="claim_outcome_action"]').value = button.dataset.outcomeAction || 'manual_only';
                modalForm.querySelector('[name="claim_resolution_due_at"]').value = button.dataset.resolutionDueAt || '';
                modalForm.querySelector('[name="claim_escalated"]').checked = button.dataset.escalated === '1';

                if (updateModal) {
                    updateModal.show();
                    return;
                }

                // Fallback when bootstrap JS is not available yet
                updateModalEl.style.display = 'block';
                updateModalEl.classList.add('show');
                updateModalEl.removeAttribute('aria-hidden');
                document.body.classList.add('modal-open');
            });
        });

        updateModalEl?.querySelector('.claim-modal-save')?.addEventListener('click', async (event) => {
            event.preventDefault();
            if (!modalForm || !modalForm.dataset.url) {
                return;
            }

            const payload = {
                status: modalForm.querySelector('[name="claim_status"]')?.value || 'open',
                assignee_id: modalForm.querySelector('[name="claim_assignee_id"]')?.value || null,
                approved_amount: modalForm.querySelector('[name="claim_approved_amount"]')?.value || null,
                resolution_note: modalForm.querySelector('[name="claim_resolution_note"]')?.value || null,
                reason: modalForm.querySelector('[name="claim_reason"]')?.value || null,
                category: modalForm.querySelector('[name="claim_category"]')?.value || null,
                priority: modalForm.querySelector('[name="claim_priority"]')?.value || 'normal',
                outcome_action: modalForm.querySelector('[name="claim_outcome_action"]')?.value || 'manual_only',
                resolution_due_at: modalForm.querySelector('[name="claim_resolution_due_at"]')?.value || null,
                requires_additional_docs: modalForm.querySelector('[name="claim_requires_docs"]')?.checked ? 1 : 0,
                escalated: modalForm.querySelector('[name="claim_escalated"]')?.checked ? 1 : 0,
            };

            try {
                await submitClaim(modalForm.dataset.url, payload, 'PUT');
                reloadCasefileWithoutDirtyPrompt();
            } catch (error) {
                alert(error.message || 'Unable to update claim.');
            }
        });
    })();
</script>
