@php
    $updateRoute = auth()->check()
        ? route('car-rentals.bookings.update-completion', $booking->id)
        : route('customer.bookings.update-completion', $booking->id);
@endphp

<div class="modal fade" id="completion-modal" tabindex="-1" aria-labelledby="completion-modal-label" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="completion-modal-label">
                    {{ trans('plugins/car-rentals::booking.completion_details') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            @php
                                $isUsingMiles = \Botble\CarRentals\Facades\CarRentalsHelper::isUsingMiles();
                                $labelKey = $isUsingMiles ? 'completion_miles' : 'completion_kilometers';
                                $placeholderKey = $isUsingMiles ? 'enter_miles' : 'enter_kilometers';
                                $helpKey = $isUsingMiles ? 'completion_miles_help' : 'completion_kilometers_help';
                            @endphp
                            <x-core::form.label for="completion_miles" :value="trans('plugins/car-rentals::booking.' . $labelKey)" />
                            <x-core::form.text-input
                                name="completion_miles"
                                id="completion_miles"
                                type="number"
                                :value="old('completion_miles', $booking->completion_miles)"
                                :placeholder="trans('plugins/car-rentals::booking.' . $placeholderKey)"
                            />
                            <x-core::form.helper-text>
                                {{ trans('plugins/car-rentals::booking.' . $helpKey) }}
                            </x-core::form.helper-text>
                        </div>
                    </div>

                    {{-- CHECK-IN FUEL LEVEL --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <x-core::form.label for="checkin_fuel_level" value="Check-in Fuel Level" />
                            <x-core::form.select
                                name="checkin_fuel_level"
                                id="checkin_fuel_level"
                                :value="old('checkin_fuel_level', $booking->checkin_fuel_level)"
                            >
                                <option value="">Select Check-in Fuel Level</option>
                                <option value="empty" {{ old('checkin_fuel_level', $booking->checkin_fuel_level) == 'empty' ? 'selected' : '' }}>Empty</option>
                                <option value="quarter" {{ old('checkin_fuel_level', $booking->checkin_fuel_level) == 'quarter' ? 'selected' : '' }}>1/4 Tank</option>
                                <option value="half" {{ old('checkin_fuel_level', $booking->checkin_fuel_level) == 'half' ? 'selected' : '' }}>1/2 Tank</option>
                                <option value="three_quarters" {{ old('checkin_fuel_level', $booking->checkin_fuel_level) == 'three_quarters' ? 'selected' : '' }}>3/4 Tank</option>
                                <option value="full" {{ old('checkin_fuel_level', $booking->checkin_fuel_level) == 'full' ? 'selected' : '' }}>Full</option>
                            </x-core::form.select>
                            <x-core::form.helper-text>Fuel level when car was picked up</x-core::form.helper-text>
                        </div>
                    </div>

                    {{-- CHECK-OUT FUEL LEVEL --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <x-core::form.label for="completion_gas_level" :value="trans('plugins/car-rentals::booking.completion_gas_level')" />
                            <x-core::form.select
                                name="completion_gas_level"
                                id="completion_gas_level"
                                :value="old('completion_gas_level', $booking->completion_gas_level)"
                            >
                                <option value="">{{ trans('plugins/car-rentals::booking.select_gas_level') }}</option>
                                <option value="empty" {{ old('completion_gas_level', $booking->completion_gas_level) == 'empty' ? 'selected' : '' }}>{{ trans('plugins/car-rentals::booking.gas_empty') }}</option>
                                <option value="quarter" {{ old('completion_gas_level', $booking->completion_gas_level) == 'quarter' ? 'selected' : '' }}>{{ trans('plugins/car-rentals::booking.gas_quarter') }}</option>
                                <option value="half" {{ old('completion_gas_level', $booking->completion_gas_level) == 'half' ? 'selected' : '' }}>{{ trans('plugins/car-rentals::booking.gas_half') }}</option>
                                <option value="three_quarters" {{ old('completion_gas_level', $booking->completion_gas_level) == 'three_quarters' ? 'selected' : '' }}>{{ trans('plugins/car-rentals::booking.gas_three_quarters') }}</option>
                                <option value="full" {{ old('completion_gas_level', $booking->completion_gas_level) == 'full' ? 'selected' : '' }}>{{ trans('plugins/car-rentals::booking.gas_full') }}</option>
                            </x-core::form.select>
                            <x-core::form.helper-text>{{ trans('plugins/car-rentals::booking.completion_gas_level_help') }}</x-core::form.helper-text>
                        </div>
                    </div>

                    {{-- FUEL DIFFERENCE CHARGE PREVIEW --}}
                    <div class="col-md-6">
                        <div class="mb-3">
                            <x-core::form.label value="Fuel Difference Charge" />
                            <div class="alert alert-warning py-2 mb-0" id="fuel-difference-preview">
                                Select both fuel levels to calculate
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <x-core::form.label for="completion_damage_images" :value="trans('plugins/car-rentals::booking.damage_images')" />
                    <input
                        type="file"
                        name="completion_damage_images[]"
                        id="completion_damage_images"
                        class="form-control"
                        multiple
                        accept="image/*"
                    />
                    <x-core::form.helper-text>
                        {{ trans('plugins/car-rentals::booking.damage_images_help') }}
                    </x-core::form.helper-text>

                    @if ($booking->completion_damage_images)
                        @php
                            $existingImages = is_string($booking->completion_damage_images)
                                ? json_decode($booking->completion_damage_images, true)
                                : $booking->completion_damage_images;
                        @endphp

                        @if ($existingImages && count($existingImages) > 0)
                            <div class="mt-2">
                                <small class="text-muted">{{ trans('plugins/car-rentals::booking.existing_images') }}:</small>
                                <div class="row mt-1">
                                    @foreach ($existingImages as $index => $image)
                                        <div class="col-md-3 col-sm-6 mb-2">
                                            <div class="position-relative">
                                                <img
                                                    src="{{ RvMedia::getImageUrl($image, 'thumb') }}"
                                                    alt="Damage image"
                                                    class="img-thumbnail"
                                                    style="width: 100%; height: 80px; object-fit: cover;"
                                                >
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                                    onclick="removeExistingImage({{ $index }}, event)"
                                                    style="padding: 2px 6px; font-size: 10px;"
                                                >
                                                    <x-core::icon name="ti ti-x" />
                                                </button>
                                                <input type="hidden" name="existing_damage_images[]" value="{{ $image }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                <div class="mb-3">
                    @if (($booking->start_mileage_snapshot ?? $booking->start_mileage) !== null)
                        @php
                            $distanceCurrencyCode = strtoupper((string) ($booking->currency?->title ?: 'USD'));
                            if (strlen($distanceCurrencyCode) !== 3) {
                                $distanceCurrencyCode = 'USD';
                            }
                        @endphp
                        <div class="alert alert-info py-2 mb-3">
                            <div
                                class="d-flex flex-wrap gap-3"
                                id="distance-overage-summary"
                                data-start-mileage="{{ (int) ($booking->start_mileage_snapshot ?? $booking->start_mileage) }}"
                                data-included-limit="{{ (int) ($booking->included_distance_limit ?? 0) }}"
                                data-billing-mode="{{ $booking->distance_overage_billing_mode ?: 'end_of_trip' }}"
                                data-unit-price="{{ (float) ($booking->extra_distance_unit_price ?? 0) }}"
                                data-currency-code="{{ $distanceCurrencyCode }}"
                                data-currency-id="{{ $booking->currency_id }}"
                            >
                                <span><strong>{{ trans('plugins/car-rentals::booking.start_mileage') }}:</strong> {{ (int) ($booking->start_mileage_snapshot ?? $booking->start_mileage) }}</span>
                                <span><strong>{{ trans('plugins/car-rentals::booking.included_distance_limit') }}:</strong> {{ (int) ($booking->included_distance_limit ?? 0) }}</span>
                                <span><strong>{{ trans('plugins/car-rentals::booking.distance_travelled') }}:</strong> <span id="distance-travelled-value">{{ (int) ($booking->distance_travelled ?? 0) }}</span></span>
                                <span><strong>{{ trans('plugins/car-rentals::booking.distance_overage_units') }}:</strong> <span id="distance-overage-units-value">{{ (int) ($booking->distance_overage_units ?? 0) }}</span></span>
                                <span>
                                    <strong>{{ trans('plugins/car-rentals::booking.distance_overage_amount') }}:</strong>
                                    <span
                                        id="distance-overage-amount-value"
                                        data-fallback-formatted="{{ format_price((float) ($booking->distance_overage_amount ?? 0), $booking->currency_id) }}"
                                    >{{ format_price((float) ($booking->distance_overage_amount ?? 0), $booking->currency_id) }}</span>
                                </span>
                            </div>
                            <small class="text-muted d-block mt-1">{{ trans('plugins/car-rentals::booking.distance_overage_summary_help') }}</small>
                        </div>
                    @endif

                    {{-- ACTUAL RETURN DATETIME --}}
                    <div class="mb-3">
                        <x-core::form.label for="actual_return_datetime" value="Actual Return Date & Time" />
                        <input
                            type="datetime-local"
                            name="actual_return_datetime"
                            id="actual_return_datetime"
                            class="form-control"
                            value="{{ old('actual_return_datetime', $booking->actual_return_datetime ? \Carbon\Carbon::parse($booking->actual_return_datetime)->format('Y-m-d\TH:i') : '') }}"
                        />
                        <x-core::form.helper-text>When did customer actually return the car?</x-core::form.helper-text>

                        {{-- Late fee preview --}}
                        @if ($booking->car)
                            <div class="alert alert-warning py-2 mt-2" id="late-fee-preview" style="display:none;">
                            </div>
                        @endif
                    </div>

                    {{-- DAMAGE AMOUNT --}}
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <x-core::form.label for="damage_amount" value="Damage Amount" />
                                <x-core::form.text-input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    name="damage_amount"
                                    id="damage_amount"
                                    :value="old('damage_amount', $booking->damage_amount)"
                                    placeholder="e.g. 500.00"
                                />
                                <x-core::form.helper-text>
                                    Enter damage amount if any damage was found
                                </x-core::form.helper-text>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3" id="damage-charge-preview" style="display:none;">
                                <x-core::form.label value="Damage Preview" />
                                <div class="alert alert-danger py-2 mb-0" id="damage-preview-box"></div>
                            </div>
                        </div>
                    </div>

                    <x-core::form.label for="completion_notes" :value="trans('plugins/car-rentals::booking.completion_notes')" />
                    <x-core::form.textarea
                        name="completion_notes"
                        id="completion_notes"
                        rows="4"
                        :value="old('completion_notes', $booking->completion_notes)"
                        :placeholder="trans('plugins/car-rentals::booking.completion_notes_placeholder')"
                    />
                    <x-core::form.helper-text>
                        {{ trans('plugins/car-rentals::booking.completion_notes_help') }}
                    </x-core::form.helper-text>
                </div>

                @if ($booking->deposit_hold_status === 'authorized')
                    <div class="border rounded p-3 mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <x-core::form.label :value="trans('plugins/car-rentals::booking.deposit_authorized_amount')" />
                                <p class="mb-2 fw-semibold">{{ format_price($booking->deposit_hold_amount ?: $booking->deposit_amount, $booking->currency_id) }}</p>
                            </div>
                            <div class="col-md-6">
                                <x-core::form.label for="deposit_settlement_action" :value="trans('plugins/car-rentals::booking.deposit_settlement_action')" />
                                <x-core::form.select name="deposit_settlement_action" id="deposit_settlement_action">
                                    <option value="">{{ trans('plugins/car-rentals::booking.deposit_settlement_action') }}</option>
                                    <option value="release">{{ trans('plugins/car-rentals::booking.deposit_settlement_release') }}</option>
                                    <option value="capture_partial">{{ trans('plugins/car-rentals::booking.deposit_settlement_capture_partial') }}</option>
                                    <option value="capture_full">{{ trans('plugins/car-rentals::booking.deposit_settlement_capture_full') }}</option>
                                    <option value="capture_overage">{{ trans('plugins/car-rentals::booking.deposit_settlement_capture_overage') }}</option>
                                </x-core::form.select>
                                <x-core::form.helper-text>
                                    {{ trans('plugins/car-rentals::booking.deposit_settlement_action_help') }}
                                </x-core::form.helper-text>
                            </div>
                        </div>

                        <div class="row mt-2 d-none" id="deposit-capture-amount-wrapper">
                            <div class="col-md-6">
                                <x-core::form.label for="deposit_capture_amount" :value="trans('plugins/car-rentals::booking.deposit_capture_amount')" />
                                <x-core::form.text-input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    :max="(float) ($booking->deposit_hold_amount ?: $booking->deposit_amount)"
                                    name="deposit_capture_amount"
                                    id="deposit_capture_amount"
                                    :placeholder="trans('plugins/car-rentals::booking.deposit_capture_amount_placeholder')"
                                />
                                <x-core::form.helper-text>
                                    {{ trans('plugins/car-rentals::booking.deposit_capture_amount_help') }}
                                </x-core::form.helper-text>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="modal-footer">
                <x-core::button type="button" color="secondary" data-bs-dismiss="modal">
                    {{ __('core/base::forms.cancel') }}
                </x-core::button>
                <x-core::button type="button" color="primary" icon="ti ti-device-floppy" id="save-completion-btn">
                    {{ __('core/base::forms.save') }}
                </x-core::button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('completion-modal');
    if (modalEl && modalEl.parentNode !== document.body) {
        document.body.appendChild(modalEl);
    }

    const saveBtn = document.getElementById('save-completion-btn');
    if (saveBtn) {
        saveBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const submitBtn = this;
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>{{ trans("core/base::forms.save") }}';
            try {
                if (typeof submitCompletionForm === 'function') {
                    submitCompletionForm();
                } else {
                    throw new Error('Form submission function not found.');
                }
            } catch (error) {
                console.error('Completion form error:', error);
                alert(error.message || 'An error occurred. Please refresh the page and try again.');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }

    const settlementAction = document.getElementById('deposit_settlement_action');
    const captureAmountWrapper = document.getElementById('deposit-capture-amount-wrapper');
    if (settlementAction && captureAmountWrapper) {
        settlementAction.addEventListener('change', function () {
            if (this.value === 'capture_partial') {
                captureAmountWrapper.classList.remove('d-none');
                return;
            }
            captureAmountWrapper.classList.add('d-none');
        });
    }

    // Distance overage calculation
    const completionMilesInput = document.getElementById('completion_miles');
    const distanceSummary = document.getElementById('distance-overage-summary');
    const travelledValueEl = document.getElementById('distance-travelled-value');
    const overageUnitsValueEl = document.getElementById('distance-overage-units-value');
    const overageAmountValueEl = document.getElementById('distance-overage-amount-value');

    if (completionMilesInput && distanceSummary && travelledValueEl && overageUnitsValueEl && overageAmountValueEl) {
        const startMileage = Number(distanceSummary.dataset.startMileage || 0);
        const includedLimit = Number(distanceSummary.dataset.includedLimit || 0);
        const unitPrice = Number(distanceSummary.dataset.unitPrice || 0);
        const billingMode = String(distanceSummary.dataset.billingMode || 'end_of_trip');
        const currencyCode = String(distanceSummary.dataset.currencyCode || 'USD');
        const currencyId = Number(distanceSummary.dataset.currencyId || 0);
        const fallbackFormatted = overageAmountValueEl.dataset.fallbackFormatted || '0.00';

        const amountFormatter = (() => {
            try {
                return new Intl.NumberFormat(undefined, { style: 'currency', currency: currencyCode });
            } catch (error) {
                return null;
            }
        })();

        const formatAmount = (amount) => {
            if (currencyId > 0 && typeof Botble !== 'undefined' && typeof Botble.formatMoney === 'function') {
                return Botble.formatMoney(amount, currencyId);
            }
            if (amountFormatter) return amountFormatter.format(amount);
            return amount.toFixed(2) || fallbackFormatted;
        };

        const recalculateDistanceSummary = () => {
            const parsedCompletion = Number(completionMilesInput.value);
            if (Number.isNaN(parsedCompletion)) return;
            const travelled = Math.max(0, Math.floor(parsedCompletion) - Math.floor(startMileage));
            const overageUnits = Math.max(0, travelled - Math.max(0, Math.floor(includedLimit)));
            const shouldBillOverage = billingMode === 'end_of_trip' || billingMode === 'both';
            const overageAmount = shouldBillOverage ? Math.round(overageUnits * unitPrice * 100) / 100 : 0;
            travelledValueEl.textContent = String(travelled);
            overageUnitsValueEl.textContent = String(overageUnits);
            overageAmountValueEl.textContent = formatAmount(overageAmount);
        };

        completionMilesInput.addEventListener('input', recalculateDistanceSummary);
        completionMilesInput.addEventListener('change', recalculateDistanceSummary);
        recalculateDistanceSummary();
    }

    // Fuel difference preview
    const checkinFuel = document.getElementById('checkin_fuel_level');
    const checkoutFuel = document.getElementById('completion_gas_level');
    const fuelPreview = document.getElementById('fuel-difference-preview');

    const fuelOrder = { empty: 0, quarter: 1, half: 2, three_quarters: 3, full: 4 };
    const fuelLabels = { empty: 'Empty', quarter: '1/4 Tank', half: '1/2 Tank', three_quarters: '3/4 Tank', full: 'Full' };

    const updateFuelPreview = () => {
        if (!checkinFuel || !checkoutFuel || !fuelPreview) return;
        const checkin = checkinFuel.value;
        const checkout = checkoutFuel.value;
        if (!checkin || !checkout) {
            fuelPreview.className = 'alert alert-warning py-2 mb-0';
            fuelPreview.textContent = 'Select both fuel levels to calculate';
            return;
        }
        const checkinVal = fuelOrder[checkin] ?? 0;
        const checkoutVal = fuelOrder[checkout] ?? 0;
        if (checkoutVal < checkinVal) {
            const diff = checkinVal - checkoutVal;
            fuelPreview.className = 'alert alert-danger py-2 mb-0';
            fuelPreview.textContent = `Fuel decreased by ${diff} level(s): ${fuelLabels[checkin]} → ${fuelLabels[checkout]}. Fuel charge will be applied.`;
        } else {
            fuelPreview.className = 'alert alert-success py-2 mb-0';
            fuelPreview.textContent = `Fuel OK: ${fuelLabels[checkin]} → ${fuelLabels[checkout]}. No fuel charge.`;
        }
    };

    if (checkinFuel) checkinFuel.addEventListener('change', updateFuelPreview);
    if (checkoutFuel) checkoutFuel.addEventListener('change', updateFuelPreview);
    updateFuelPreview();
});

// Late fee preview
const actualReturnInput = document.getElementById('actual_return_datetime');
const lateFeePreview = document.getElementById('late-fee-preview');
@if($booking->car)
const lateRate = {{ (float) optional($booking->car->car)->late_fee_per_hour ?? 0 }};
const expectedReturn = '{{ $booking->car->rental_end_date ?? '' }}';
@endif

const updateLateFeePreview = () => {
    if (!actualReturnInput || !lateFeePreview || !expectedReturn) return;
    
    const actualTime = new Date(actualReturnInput.value);
    // const expectedTime = new Date(expectedReturn + 'T00:00:00');
    const expectedTime = new Date(expectedReturn);
    if (!actualReturnInput.value) {
        lateFeePreview.style.display = 'none';
        return;
    }
    
    const diffMs = actualTime - expectedTime;
    const diffHours = Math.ceil(diffMs / (1000 * 60 * 60));
    
    if (diffHours > 0) {
        const charge = diffHours * lateRate;
        lateFeePreview.className = 'alert alert-danger py-2 mt-2';
        lateFeePreview.textContent = `Late by ${diffHours} hour(s) × $${lateRate}/hr = $${charge.toFixed(2)} charge`;
        lateFeePreview.style.display = 'block';
    } else {
        lateFeePreview.className = 'alert alert-success py-2 mt-2';
        lateFeePreview.textContent = 'On time return — No late fee!';
        lateFeePreview.style.display = 'block';
    }
};

const damageInput = document.getElementById('damage_amount');
const damagePreview = document.getElementById('damage-charge-preview');
const damagePreviewBox = document.getElementById('damage-preview-box');

const updateDamagePreview = () => {
    if (!damageInput || !damagePreview || !damagePreviewBox) return;
    const amount = parseFloat(damageInput.value);
    if (amount > 0) {
        damagePreview.style.display = 'block';
        damagePreviewBox.textContent = `Damage charge of $${amount.toFixed(2)} will be raised to customer`;
    } else {
        damagePreview.style.display = 'none';
    }
};

if (damageInput) {
    damageInput.addEventListener('input', updateDamagePreview);
    updateDamagePreview();
}

if (actualReturnInput) {
    actualReturnInput.addEventListener('change', updateLateFeePreview);
    updateLateFeePreview();
}

function removeExistingImage(index, event) {
    const imageContainer = event?.target?.closest('.col-md-3');
    if (!imageContainer) return;
    imageContainer.remove();
}
</script>