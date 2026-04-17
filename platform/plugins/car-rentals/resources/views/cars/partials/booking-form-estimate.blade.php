<div class="pricing-summary mb-3">
    @php
        $discountSource = $policyDiscountSource ?? '';
        $discountSourceLabel = trans('plugins/car-rentals::car-rentals.price_breakdown.discount_sources.fallback');

        if ($discountSource === 'weekly') {
            $discountSourceLabel = trans('plugins/car-rentals::car-rentals.price_breakdown.discount_sources.weekly');
        } elseif ($discountSource === 'monthly') {
            $discountSourceLabel = trans('plugins/car-rentals::car-rentals.price_breakdown.discount_sources.monthly');
        } elseif (str_starts_with($discountSource, 'trip-rule:')) {
            $discountSourceLabel = trans('plugins/car-rentals::car-rentals.price_breakdown.discount_sources.trip_rule');
        } elseif ($discountSource === 'combined') {
            $discountSourceLabel = trans('plugins/car-rentals::car-rentals.price_breakdown.discount_sources.combined');
        }

        $billingModeLabels = [
            'end_of_trip' => trans('plugins/car-rentals::car-rentals.price_breakdown.billing_modes.end_of_trip'),
            'prepaid_estimate' => trans('plugins/car-rentals::car-rentals.price_breakdown.billing_modes.prepaid_estimate'),
            'both' => trans('plugins/car-rentals::car-rentals.price_breakdown.billing_modes.both'),
        ];

        $distanceModeLabel = $billingModeLabels[$distanceOverageBillingMode ?? 'end_of_trip']
            ?? trans('plugins/car-rentals::car-rentals.price_breakdown.billing_modes.end_of_trip');
    @endphp

    {{-- FIX: Removed the collapsible details/summary tags and made it a standard div --}}
    <div class="mb-3 border-bottom pb-3">
        <h6 class="fw-semibold mb-3">
            {{ trans('plugins/car-rentals::car-rentals.price_breakdown.toggle') }}
        </h6>

        <div class="pt-2">
            <div class="row">
                <div class="col-lg-8 col-7 text-muted">{{ trans('plugins/car-rentals::car-rentals.price_breakdown.base_rental') }}</div>
                <div class="col-lg-4 col-5 text-end">{{ format_price($baseRentalAmount ?? 0, $currencyId ?? null) }}</div>
            </div>
            <div class="row">
                <div class="col-lg-8 col-7 text-muted">{{ trans('plugins/car-rentals::car-rentals.price_breakdown.rental_days') }}</div>
                <div class="col-lg-4 col-5 text-end">{{ (int) ($rentalDays ?? 0) }}</div>
            </div>

            @if (($policyDiscountAmount ?? 0) > 0)
                <div class="row">
                    <div class="col-lg-8 col-7 text-muted">
                        {{ trans('plugins/car-rentals::car-rentals.price_breakdown.policy_discount') }}
                        <small class="d-block">{{ $discountSourceLabel }}</small>
                    </div>
                    <div class="col-lg-4 col-5 text-end">-{{ format_price($policyDiscountAmount, $currencyId ?? null) }}</div>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-7 text-muted">{{ trans('plugins/car-rentals::car-rentals.price_breakdown.rental_after_policy_discount') }}</div>
                    <div class="col-lg-4 col-5 text-end">{{ format_price($rentalAmount ?? 0, $currencyId ?? null) }}</div>
                </div>
            @endif

            <div class="row mt-1">
                <div class="col-lg-8 col-7 text-muted">{{ trans('plugins/car-rentals::car-rentals.price_breakdown.services_subtotal') }}</div>
                <div class="col-lg-4 col-5 text-end">{{ format_price($serviceAmount ?? 0, $currencyId ?? null) }}</div>
            </div>
            
            {{-- FIX: Changed $insuranceAmount to $guestProtectionFee --}}
            <div class="row">
                <div class="col-lg-8 col-7 text-muted">{{ trans('plugins/car-rentals::car-rentals.price_breakdown.insurance_subtotal') }}</div>
                <div class="col-lg-4 col-5 text-end">{{ format_price($guestProtectionFee ?? 0, $currencyId ?? null) }}</div>
            </div>

            {{-- NEW: Delivery Fee Line Item (Placed right below Insurance) --}}
            @if(isset($deliveryFee) && $deliveryFee > 0)
                <div class="row mt-1">
                    <div class="col-lg-8 col-7 text-muted">{{ __('Delivery Fee') }}</div>
                    <div class="col-lg-4 col-5 text-end">{{ format_price($deliveryFee, $currencyId ?? null) }}</div>
                </div>
            @elseif(isset($deliveryFee) && $deliveryFee === 0.00 && request()->input('delivery_location_id'))
                <div class="row mt-1">
                    <div class="col-lg-8 col-7 text-muted">
                        {{ __('Delivery Fee') }} 
                        <span class="badge bg-success text-white ms-1" style="font-size: 0.65rem; padding: 2px 4px;">{{ __('Free') }}</span>
                    </div>
                    <div class="col-lg-4 col-5 text-end text-success fw-bold">{{ format_price(0, $currencyId ?? null) }}</div>
                </div>
            @endif
            @if(!empty($distanceError))
                <div class="alert alert-danger" style="font-size: 13px; padding: 10px; border-radius: 8px;">
                    <i class="ti ti-map-pin-off me-1"></i> {{ $distanceError }}
                </div>
            @endif

            @if (($tax ?? 0) > 0)
                <div class="row mt-1">
                    <div class="col-lg-8 col-7 text-muted">{{ trans('plugins/car-rentals::car-rentals.price_breakdown.tax') }}</div>
                    <div class="col-lg-4 col-5 text-end">{{ format_price($tax, $currencyId ?? null) }}</div>
                </div>
            @endif

            @if (($feeAmount ?? 0) > 0)
                <div class="row">
                    <div class="col-lg-8 col-7 text-muted">{{ $feeName ?: trans('plugins/car-rentals::car-rentals.price_breakdown.service_fee') }}</div>
                    <div class="col-lg-4 col-5 text-end">{{ format_price($feeAmount, $currencyId ?? null) }}</div>
                </div>
            @endif

            @if (($depositAmount ?? 0) > 0)
                <div class="row mt-1">
                    <div class="col-lg-8 col-7 text-muted">
                        {{ trans('plugins/car-rentals::car-rentals.price_breakdown.deposit_hold') }}
                        <small class="d-block">
                            @if (($depositType ?? 'percentage') === 'fixed')
                                {{ trans('plugins/car-rentals::car-rentals.price_breakdown.deposit_fixed') }}
                            @else
                                {{ trans('plugins/car-rentals::car-rentals.price_breakdown.deposit_percentage', ['rate' => (float) ($depositRate ?? 0)]) }}
                            @endif
                        </small>
                    </div>
                    <div class="col-lg-4 col-5 text-end">{{ format_price($depositAmount, $currencyId ?? null) }}</div>
                </div>
                <div class="row">
                    <div class="col-lg-8 col-7 text-muted">{{ trans('plugins/car-rentals::car-rentals.price_breakdown.deposit_base') }}</div>
                    <div class="col-lg-4 col-5 text-end">{{ format_price($depositBaseAmount ?? 0, $currencyId ?? null) }}</div>
                </div>
            @endif

            @if (($includedDistanceLimit ?? null) !== null || ($extraDistanceUnitPrice ?? 0) > 0)
                <div class="row mt-1">
                    <div class="col-12">
                        <small class="text-muted d-block">
                            {{ trans('plugins/car-rentals::car-rentals.price_breakdown.distance_rules', [
                                'limit' => (int) ($includedDistanceLimit ?? 0),
                                'unit' => $distanceUnit ?? 'km',
                                'rate' => format_price($extraDistanceUnitPrice ?? 0, $currencyId ?? null),
                                'mode' => $distanceModeLabel,
                            ]) }}
                        </small>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 col-6">{{ __('Subtotal') }}</div>
        <div class="col-lg-4 col-6 text-end"><strong>{{ format_price($subtotal ?? 0, $currencyId ?? null) }}</strong></div>
    </div>
    @if (($tax ?? 0) > 0)
        <div class="row">
            <div class="col-lg-8 col-6"><span class="tax-label">{{ __('Taxes') }}</span> @if (!empty($taxInfo)) - <small class="text-muted tax-info-text">{{ $taxInfo }}</small>@endif</div>
            <div class="col-lg-4 col-6 text-end"><strong>{{ format_price($tax, $currencyId ?? null) }}</strong></div>
        </div>
    @endif
    @if (($discount ?? 0) > 0)
        <div class="row">
            <div class="col-lg-8 col-6">{{ __('Sale discount') }}</div>
            <div class="col-lg-4 col-6 text-end"><strong>{{ format_price($discount, $currencyId ?? null) }}</strong></div>
        </div>
    @endif
    @if (($feeAmount ?? 0) > 0)
        <div class="row">
            <div class="col-lg-8 col-6">{{ __('Service fee') }}</div>
            <div class="col-lg-4 col-6 text-end"><strong>{{ format_price($feeAmount, $currencyId ?? null) }}</strong></div>
        </div>
    @endif
    @if (($depositAmount ?? 0) > 0)
        <div class="row">
            <div class="col-lg-8 col-6">{{ __('Refundable Deposit (authorization hold)') }}</div>
            <div class="col-lg-4 col-6 text-end"><strong>{{ format_price($depositAmount, $currencyId ?? null) }}</strong></div>
        </div>
    @endif
    <div class="row total">
        <div class="col-lg-8 col-6">{{ __('Total') }}</div>
        <div class="col-lg-4 col-6 text-end"><strong>{{ format_price($total ?? 0, $currencyId ?? null) }}</strong></div>
    </div>
</div>