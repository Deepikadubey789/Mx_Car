@php
    /** @var \Botble\CarRentals\Models\Car $car */
    $pricingPolicy = $car->pricingPolicy;
    $tripDiscounts = old('trip_discounts');

    if ($tripDiscounts === null) {
        $tripDiscounts = $pricingPolicy ? $pricingPolicy->tripDiscounts->map(function ($discount) {
            return [
                'id' => $discount->id,
                'min_days' => $discount->min_days,
                'max_days' => $discount->max_days,
                'discount_type' => $discount->discount_type,
                'discount_value' => $discount->discount_value,
                'priority' => $discount->priority,
                'active' => $discount->active,
                'description' => $discount->description,
            ];
        })->all() : [];
    }

    if (empty($tripDiscounts)) {
        $tripDiscounts = [[]];
    }
@endphp

<div class="pricing-policy-wrapper">
    <!-- Quick Links -->
    <div class="row mb-3">
        <div class="col-12">
            <a href="{{ route('car-rentals.auto-pricing.metrics') }}" class="btn btn-sm btn-info" target="_blank">
                <i class="ti ti-chart-line me-1"></i> View Auto-Pricing Metrics
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <p class="text-muted mb-0">
                {{ trans('plugins/car-rentals::car-rentals.pricing_policy.helper') }}
            </p>
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ trans('plugins/car-rentals::car-rentals.pricing_policy.weekly_discount_type') }}</label>
            <select name="weekly_discount_type" class="form-select">
                @foreach (['none', 'percentage', 'fixed'] as $type)
                    <option value="{{ $type }}" @selected(old('weekly_discount_type', optional($pricingPolicy)->weekly_discount_type ?? 'none') === $type)>
                        {{ trans('plugins/car-rentals::car-rentals.pricing_policy.discount_types.' . $type) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ trans('plugins/car-rentals::car-rentals.pricing_policy.weekly_discount_value') }}</label>
            <input name="weekly_discount_value" type="number" min="0" step="0.01" class="form-control" value="{{ old('weekly_discount_value', optional($pricingPolicy)->weekly_discount_value ?? 0) }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ trans('plugins/car-rentals::car-rentals.pricing_policy.monthly_discount_type') }}</label>
            <select name="monthly_discount_type" class="form-select">
                @foreach (['none', 'percentage', 'fixed'] as $type)
                    <option value="{{ $type }}" @selected(old('monthly_discount_type', optional($pricingPolicy)->monthly_discount_type ?? 'none') === $type)>
                        {{ trans('plugins/car-rentals::car-rentals.pricing_policy.discount_types.' . $type) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ trans('plugins/car-rentals::car-rentals.pricing_policy.monthly_discount_value') }}</label>
            <input name="monthly_discount_value" type="number" min="0" step="0.01" class="form-control" value="{{ old('monthly_discount_value', optional($pricingPolicy)->monthly_discount_value ?? 0) }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ trans('plugins/car-rentals::car-rentals.pricing_policy.included_distance_per_day') }}</label>
            <input name="included_distance_per_day" type="number" min="0" step="1" class="form-control" value="{{ old('included_distance_per_day', optional($pricingPolicy)->included_distance_per_day ?? '') }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ trans('plugins/car-rentals::car-rentals.pricing_policy.included_distance_per_trip') }}</label>
            <input name="included_distance_per_trip" type="number" min="0" step="1" class="form-control" value="{{ old('included_distance_per_trip', optional($pricingPolicy)->included_distance_per_trip ?? '') }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">{{ trans('plugins/car-rentals::car-rentals.pricing_policy.extra_distance_unit_price') }}</label>
            <input name="extra_distance_unit_price" type="number" min="0" step="0.0001" class="form-control" value="{{ old('extra_distance_unit_price', optional($pricingPolicy)->extra_distance_unit_price ?? 0) }}">
        </div>

        <div class="col-md-4">
            <label class="form-label">{{ trans('plugins/car-rentals::car-rentals.pricing_policy.distance_unit') }}</label>
            <select name="distance_unit" class="form-select">
                @foreach (['km', 'miles'] as $unit)
                    <option value="{{ $unit }}" @selected(old('distance_unit', optional($pricingPolicy)->distance_unit ?? 'km') === $unit)>
                        {{ trans('plugins/car-rentals::car-rentals.pricing_policy.distance_units.' . $unit) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">{{ trans('plugins/car-rentals::car-rentals.pricing_policy.distance_overage_billing_mode') }}</label>
            <select name="distance_overage_billing_mode" class="form-select">
                @foreach (['end_of_trip', 'prepaid_estimate', 'both'] as $mode)
                    <option value="{{ $mode }}" @selected(old('distance_overage_billing_mode', optional($pricingPolicy)->distance_overage_billing_mode ?? 'end_of_trip') === $mode)>
                        {{ trans('plugins/car-rentals::car-rentals.pricing_policy.billing_modes.' . $mode) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">{{ trans('plugins/car-rentals::car-rentals.pricing_policy.max_discount_cap_percent') }}</label>
            <input name="max_discount_cap_percent" type="number" min="0" max="100" step="0.01" class="form-control" value="{{ old('max_discount_cap_percent', optional($pricingPolicy)->max_discount_cap_percent ?? '') }}">
        </div>

        <div class="col-12">
            <hr class="my-2">
            <h5 class="mb-0">Demand pricing recommendations</h5>
            <small class="text-muted">Generate suggested prices from demand signals and let hosts apply them manually first.</small>
        </div>

        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check form-switch mb-2">
                <input type="hidden" name="demand_recommendations_enabled" value="0">
                <input class="form-check-input" type="checkbox" id="demand_recommendations_enabled" name="demand_recommendations_enabled" value="1" @checked(old('demand_recommendations_enabled', optional($pricingPolicy)->demand_recommendations_enabled ?? false))>
                <label class="form-check-label" for="demand_recommendations_enabled">
                    Enable demand recommendations
                </label>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Minimum recommended price</label>
            <input name="demand_min_price" type="number" min="0" step="0.01" class="form-control" value="{{ old('demand_min_price', optional($pricingPolicy)->demand_min_price ?? '') }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Maximum recommended price</label>
            <input name="demand_max_price" type="number" min="0" step="0.01" class="form-control" value="{{ old('demand_max_price', optional($pricingPolicy)->demand_max_price ?? '') }}">
        </div>

        <div class="col-md-6">
            <label class="form-label">Max daily change percentage</label>
            <input name="demand_max_daily_change_percent" type="number" min="0" max="100" step="0.01" class="form-control" value="{{ old('demand_max_daily_change_percent', optional($pricingPolicy)->demand_max_daily_change_percent ?? '') }}">
        </div>

        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check form-switch mb-2">
                <input type="hidden" name="allow_best_discount_only" value="0">
                <input class="form-check-input" type="checkbox" id="allow_best_discount_only" name="allow_best_discount_only" value="1" @checked(old('allow_best_discount_only', optional($pricingPolicy)->allow_best_discount_only ?? true))>
                <label class="form-check-label" for="allow_best_discount_only">
                    {{ trans('plugins/car-rentals::car-rentals.pricing_policy.allow_best_discount_only') }}
                </label>
            </div>
        </div>

        <div class="col-12">
            <hr class="my-2">
            <h5 class="mb-0">Auto-apply demand recommendations</h5>
            <small class="text-muted">Automatically apply high-confidence recommendations without manual review. You can pause at any time.</small>
        </div>

        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check form-switch mb-2">
                <input type="hidden" name="demand_auto_apply_enabled" value="0">
                <input class="form-check-input" type="checkbox" id="demand_auto_apply_enabled" name="demand_auto_apply_enabled" value="1" @checked(old('demand_auto_apply_enabled', optional($pricingPolicy)->demand_auto_apply_enabled ?? false))>
                <label class="form-check-label" for="demand_auto_apply_enabled">
                    Enable auto-apply
                </label>
            </div>
        </div>

        <div class="col-md-6">
            <label class="form-label">Minimum confidence threshold (0-1)</label>
            <input name="demand_auto_apply_min_confidence" type="number" min="0" max="1" step="0.01" class="form-control" value="{{ old('demand_auto_apply_min_confidence', optional($pricingPolicy)->demand_auto_apply_min_confidence ?? 0.70) }}" placeholder="0.70">
            <small class="text-muted d-block mt-1">Default: 0.70 (70%). Higher = more conservative, only applies confident recommendations</small>
        </div>

        <div class="col-md-6">
            <label class="form-label">Max daily change percentage (optional)</label>
            <input name="demand_auto_apply_max_daily_change_percent" type="number" min="0" max="100" step="0.01" class="form-control" value="{{ old('demand_auto_apply_max_daily_change_percent', optional($pricingPolicy)->demand_auto_apply_max_daily_change_percent ?? '') }}">
            <small class="text-muted d-block mt-1">Leave empty to use global setting. Limits how much price can change per day.</small>
        </div>

        <div class="col-md-6 d-flex align-items-end gap-2">
            <div class="flex-grow-1">
                <label class="form-label">Pause auto-apply for</label>
                <div class="input-group">
                    <input type="number" min="1" max="336" class="form-control" id="demand_auto_apply_pause_hours" name="demand_auto_apply_pause_hours" placeholder="Hours">
                    <button class="btn btn-outline-warning" type="button" id="btn-pause-auto-apply">
                        <i class="ti ti-player-pause me-1"></i> Pause
                    </button>
                </div>
                <small class="text-muted d-block mt-1">Temporarily stop auto-applying prices. Max 2 weeks (336 hours).</small>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h5 class="mb-1">{{ trans('plugins/car-rentals::car-rentals.pricing_policy.trip_discount_rules') }}</h5>
            <small class="text-muted">{{ trans('plugins/car-rentals::car-rentals.pricing_policy.trip_discount_rules_helper') }}</small>
        </div>
        <button type="button" class="btn btn-outline-primary" id="add-trip-discount-row">
            {{ trans('plugins/car-rentals::car-rentals.pricing_policy.add_trip_discount_rule') }}
        </button>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle mb-0" id="trip-discounts-table">
            <thead>
                <tr>
                    <th>{{ trans('plugins/car-rentals::car-rentals.pricing_policy.min_days') }}</th>
                    <th>{{ trans('plugins/car-rentals::car-rentals.pricing_policy.max_days') }}</th>
                    <th>{{ trans('plugins/car-rentals::car-rentals.pricing_policy.discount_type') }}</th>
                    <th>{{ trans('plugins/car-rentals::car-rentals.pricing_policy.discount_value') }}</th>
                    <th>{{ trans('plugins/car-rentals::car-rentals.pricing_policy.priority') }}</th>
                    <th>{{ trans('plugins/car-rentals::car-rentals.pricing_policy.active') }}</th>
                    <th>{{ trans('plugins/car-rentals::car-rentals.pricing_policy.description') }}</th>
                    <th style="width: 60px;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tripDiscounts as $index => $discount)
                    @php
                        $row = is_array($discount) ? $discount : [];
                    @endphp
                    <tr class="trip-discount-row">
                        <td>
                            <input type="hidden" name="trip_discounts[{{ $index }}][id]" value="{{ $row['id'] ?? '' }}">
                            <input type="number" min="1" step="1" class="form-control" name="trip_discounts[{{ $index }}][min_days]" value="{{ $row['min_days'] ?? '' }}">
                        </td>
                        <td><input type="number" min="1" step="1" class="form-control" name="trip_discounts[{{ $index }}][max_days]" value="{{ $row['max_days'] ?? '' }}"></td>
                        <td>
                            <select class="form-select" name="trip_discounts[{{ $index }}][discount_type]">
                                <option value="percentage" @selected(($row['discount_type'] ?? 'percentage') === 'percentage')>{{ trans('plugins/car-rentals::car-rentals.pricing_policy.discount_types.percentage') }}</option>
                                <option value="fixed" @selected(($row['discount_type'] ?? 'percentage') === 'fixed')>{{ trans('plugins/car-rentals::car-rentals.pricing_policy.discount_types.fixed') }}</option>
                            </select>
                        </td>
                        <td><input type="number" min="0" step="0.01" class="form-control" name="trip_discounts[{{ $index }}][discount_value]" value="{{ $row['discount_value'] ?? '' }}"></td>
                        <td><input type="number" min="0" step="1" class="form-control" name="trip_discounts[{{ $index }}][priority]" value="{{ $row['priority'] ?? 0 }}"></td>
                        <td class="text-center">
                            <input type="hidden" name="trip_discounts[{{ $index }}][active]" value="0">
                            <input type="checkbox" class="form-check-input" name="trip_discounts[{{ $index }}][active]" value="1" @checked((bool) ($row['active'] ?? true))>
                        </td>
                        <td><input type="text" class="form-control" name="trip_discounts[{{ $index }}][description]" value="{{ $row['description'] ?? '' }}"></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-link text-danger p-0 remove-trip-discount-row">&times;</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <template id="trip-discount-template">
        <tr class="trip-discount-row">
            <td>
                <input type="hidden" name="trip_discounts[__INDEX__][id]" value="">
                <input type="number" min="1" step="1" class="form-control" name="trip_discounts[__INDEX__][min_days]" value="">
            </td>
            <td><input type="number" min="1" step="1" class="form-control" name="trip_discounts[__INDEX__][max_days]" value=""></td>
            <td>
                <select class="form-select" name="trip_discounts[__INDEX__][discount_type]">
                    <option value="percentage">{{ trans('plugins/car-rentals::car-rentals.pricing_policy.discount_types.percentage') }}</option>
                    <option value="fixed">{{ trans('plugins/car-rentals::car-rentals.pricing_policy.discount_types.fixed') }}</option>
                </select>
            </td>
            <td><input type="number" min="0" step="0.01" class="form-control" name="trip_discounts[__INDEX__][discount_value]" value=""></td>
            <td><input type="number" min="0" step="1" class="form-control" name="trip_discounts[__INDEX__][priority]" value="0"></td>
            <td class="text-center">
                <input type="hidden" name="trip_discounts[__INDEX__][active]" value="0">
                <input type="checkbox" class="form-check-input" name="trip_discounts[__INDEX__][active]" value="1" checked>
            </td>
            <td><input type="text" class="form-control" name="trip_discounts[__INDEX__][description]" value=""></td>
            <td class="text-center">
                <button type="button" class="btn btn-link text-danger p-0 remove-trip-discount-row">&times;</button>
            </td>
        </tr>
    </template>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tableBody = document.querySelector('#trip-discounts-table tbody');
        const addButton = document.getElementById('add-trip-discount-row');
        const template = document.getElementById('trip-discount-template');

        if (!tableBody || !addButton || !template) {
            return;
        }

        const getNextIndex = () => tableBody.querySelectorAll('.trip-discount-row').length;

        const bindRemoveButtons = () => {
            tableBody.querySelectorAll('.remove-trip-discount-row').forEach((button) => {
                button.onclick = function () {
                    const row = button.closest('tr');
                    if (row) {
                        row.remove();
                    }
                };
            });
        };

        addButton.addEventListener('click', function () {
            const index = getNextIndex();
            const html = template.innerHTML.replaceAll('__INDEX__', index);
            const wrapper = document.createElement('tbody');
            wrapper.innerHTML = html.trim();
            const row = wrapper.firstElementChild;

            if (row) {
                tableBody.appendChild(row);
                bindRemoveButtons();
            }
        });

        bindRemoveButtons();
    });
</script>