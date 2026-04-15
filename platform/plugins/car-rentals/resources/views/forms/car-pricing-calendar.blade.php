<div class="car-pricing-calendar-wrapper"
    data-url="{{ route('car-rentals.cars.pricing-calendar', $car->getKey()) }}"
    data-apply-url="{{ route('car-rentals.cars.pricing-recommendations.apply', ['car' => $car->getKey(), 'recommendation' => '__RECOMMENDATION__']) }}"
    data-dismiss-url="{{ route('car-rentals.cars.pricing-recommendations.dismiss', ['car' => $car->getKey(), 'recommendation' => '__RECOMMENDATION__']) }}">
    <div id="pricing-calendar" class="pricing-calendar mb-3"></div>

    <div class="d-flex flex-wrap gap-3 mb-3">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-azure text-azure-fg">$0</span>
            <small class="text-muted">{{ trans('plugins/car-rentals::car-rentals.pricing_calendar.base_price') }}</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-blue text-blue-fg">$0</span>
            <small class="text-muted">{{ trans('plugins/car-rentals::car-rentals.pricing_calendar.value_types.fixed') }}</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-green text-green-fg">$0</span>
            <small class="text-muted">{{ trans('plugins/car-rentals::car-rentals.pricing_calendar.value_types.amount_adjust') }}</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-orange text-orange-fg">$0</span>
            <small class="text-muted">{{ trans('plugins/car-rentals::car-rentals.pricing_calendar.value_types.percentage_adjust') }}</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-secondary">$0</span>
            <small class="text-muted">{{ trans('plugins/car-rentals::car-rentals.pricing_calendar.inactive') }}</small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-warning text-dark">$0</span>
            <small class="text-muted">Demand recommendation</small>
        </div>
    </div>

    <div class="modal fade" id="modal-pricing-calendar" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ trans('plugins/car-rentals::car-rentals.pricing_calendar.set_pricing') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form>
                    <div class="modal-body">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    id="pricing-active"
                                    checked
                                >
                                <label class="form-check-label" for="pricing-active">
                                    {{ trans('plugins/car-rentals::car-rentals.pricing_calendar.is_active') }}
                                </label>
                            </div>
                        </div>

                        <div id="conditional-fields">
                            <div class="mb-3">
                                <label class="form-label">{{ trans('plugins/car-rentals::car-rentals.pricing_calendar.value') }}</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    step="0.01"
                                    id="pricing-value"
                                >
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ trans('plugins/car-rentals::car-rentals.pricing_calendar.value_type') }}</label>
                                <select class="form-select" id="pricing-value-type">
                                    <option value="fixed">{{ trans('plugins/car-rentals::car-rentals.pricing_calendar.value_types.fixed') }}</option>
                                    <option value="amount_adjust">{{ trans('plugins/car-rentals::car-rentals.pricing_calendar.value_types.amount_adjust') }}</option>
                                    <option value="percentage_adjust">{{ trans('plugins/car-rentals::car-rentals.pricing_calendar.value_types.percentage_adjust') }}</option>
                                </select>
                            </div>

                            <div class="alert alert-info" id="pricing-percentage-info" style="display: none;">
                                {{ trans('plugins/car-rentals::car-rentals.pricing_calendar.percentage_info') }}
                            </div>
                            <div class="alert alert-info" id="pricing-amount-info" style="display: none;">
                                {{ trans('plugins/car-rentals::car-rentals.pricing_calendar.amount_info') }}
                            </div>

                            <div class="alert alert-warning d-none" id="demand-recommendation-info"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-warning d-none" id="btn-dismiss-recommendation">
                            Dismiss recommendation
                        </button>
                        <button type="button" class="btn btn-warning d-none" id="btn-apply-recommendation">
                            Apply recommendation
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            {{ trans('core/base::forms.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary btn-save">
                            {{ trans('core/base::forms.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
