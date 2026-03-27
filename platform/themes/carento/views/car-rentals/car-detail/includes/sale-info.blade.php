@if($car->is_for_sale)
    <div class="sale-info">
        <div class="head-sale-info d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <x-core::icon name="ti ti-car" class="text-primary" size="18" />
                <p class="text-lg-bold neutral-1000 mb-0">{{ __('Buy This Car') }}</p>
            </div>
            @if($car->sale_status)
                <span class="car-sale-status">{!! $car->sale_status->toHtml() !!}</span>
            @endif
        </div>

        <div class="content-sale-info mt-3">
            <div class="car-price-section text-center py-3 px-2 rounded-2 bg-light">
                <span class="text-xs text-uppercase text-muted fw-medium d-block mb-1">{{ __('Sale Price') }}</span>
                <h4 class="sale-price mb-0 fw-bold text-primary">{{ $car->price_html }}</h4>
            </div>

            @if($car->condition || ($car->tax && $car->tax->percentage) || $car->warranty_information)
                <div class="sale-details mt-3">
                    <div class="row g-2">
                        @if($car->condition)
                            <div class="col-6">
                                <div class="detail-item d-flex align-items-center gap-2 p-2 rounded-2 bg-light">
                                    <x-core::icon name="ti ti-certificate" class="text-success" size="16" />
                                    <div class="small">
                                        <span class="text-muted d-block" style="font-size: 10px;">{{ __('Condition') }}</span>
                                        <span class="fw-medium">{{ $car->condition->label() }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($car->tax && $car->tax->percentage)
                            <div class="col-6">
                                <div class="detail-item d-flex align-items-center gap-2 p-2 rounded-2 bg-light">
                                    <x-core::icon name="ti ti-receipt-tax" class="text-warning" size="16" />
                                    <div class="small">
                                        <span class="text-muted d-block" style="font-size: 10px;">{{ __('Tax') }}</span>
                                        <span class="fw-medium">{{ $car->tax->percentage }}%</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($car->warranty_information)
                            <div class="col-12">
                                <div class="detail-item d-flex align-items-center gap-2 p-2 rounded-2 bg-light">
                                    <x-core::icon name="ti ti-shield-check" class="text-info" size="16" />
                                    <div class="small">
                                        <span class="text-muted d-block" style="font-size: 10px;">{{ __('Warranty') }}</span>
                                        <span class="fw-medium">{{ $car->warranty_information }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if(get_car_rentals_setting('enable_message_form', true))
                <div class="sale-cta mt-3">
                    <a href="javascript:void(0);" class="btn btn-primary btn-sm w-100" onclick="document.querySelector('.form-contact-wrapper')?.scrollIntoView({behavior: 'smooth', block: 'center'})">
                        <x-core::icon name="ti ti-message-circle" class="me-1" size="16" />
                        {{ __('Contact Seller') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif
