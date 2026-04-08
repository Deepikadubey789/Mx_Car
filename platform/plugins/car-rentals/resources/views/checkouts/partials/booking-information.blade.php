<div class="my-3 bg-light">
    <div class="position-relative p-3" id="cart-item">
        <div id="price-lock-message" class="alert alert-warning mb-3" style="display: none;"></div>

        <p>{{ __('Car Information:') }}</p>
        @if ($car)
            @include('plugins/car-rentals::checkouts.partials.car-booking-info', [
                'carImage' => RvMedia::getImageUrl($car->image, 'thumb', false, RvMedia::getDefaultImage()),
                'carName' => $car->name,
                'carUrl' => null,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'rentalRate' => format_price($car->rental_rate, $car->currency_id) . '/ ' . $car->rental_type->label(),
                'pickupAddress' => $car->pickup_address_text ?? null,
                'returnAddress' => $car->return_address_text ?? null,
                'imageColSize' => '3'
            ])
        @endif

        @if(isset($services) && count($services) > 0)
            <p>{{ __('Services:') }}</p>
            @foreach($services as $service)
                <div class="row cart-item">
                    <div class="col">
                        <p class="mb-2"><strong>{{ $service->name }}</strong></p>
                    </div>
                    <div class="col-auto text-end">
                        <p class="mb-2">{{ format_price($service->price) }}</p>
                    </div>
                </div>
            @endforeach
        @endif

        {{-- NEW: Insurances Section --}}
        @if(isset($insurances) && count($insurances) > 0)
            <p class="mt-2">{{ __('Insurance Coverage:') }}</p>
            @foreach($insurances as $insurance)
                <div class="row cart-item">
                    <div class="col">
                        <p class="mb-2 text-success">
                            <i class="ti ti-shield-check me-1"></i> <strong>{{ $insurance->name }}</strong>
                        </p>
                    </div>
                    <div class="col-auto text-end">
                        <p class="mb-2 text-success">{{ format_price($insurance->price) }}</p>
                    </div>
                </div>
            @endforeach
        @endif

        <hr class="border-dark-subtle">

        <div class="mt-2 p-2">
            <div class="row">
                <div class="col-6">
                    <p>{{ __('Subtotal:') }}</p>
                </div>
                <div class="col-6">
                    <p class="price-text sub-total-text text-end">
                        {{ format_price($amount, $car->currency_id) }}
                    </p>
                </div>
            </div>

            @if($taxAmount)
                <div class="row">
                    <div class="col-6">
                        <p>{{ __('Tax') }} @if(!empty($taxTitle)) - <small class="d-inline-block text-muted tax-info-text">{{ $taxTitle }}</small>@endif</p>
                    </div>
                    <div class="col-6 float-end">
                        <p class="price-text tax-price-text text-end">
                            {{ format_price($taxAmount, $car->currency_id) }}
                        </p>
                    </div>
                </div>
            @endif

            @if ($couponCode && isset($couponAmount))
                <div class="row">
                    <div class="col-6"><p>{{ __('Coupon code') }}:</p></div>
                    <div class="col-6 float-end"><p class="price-text tax-price-text">{{ $couponCode }}</p></div>
                </div>
                <div class="row">
                    <div class="col-6"><p>{{ __('Coupon code discount amount') }}:</p></div>
                    <div class="col-6 float-end"><p class="price-text tax-price-text">{{ format_price($couponAmount) }}</p></div>
                </div>
            @endif

            @if (($feeAmount ?? 0) > 0)
                <div class="row">
                    <div class="col-6"><p>{{ ($feeName ?? __('Service fee')) }}:</p></div>
                    <div class="col-6 float-end"><p class="price-text tax-price-text">{{ format_price($feeAmount) }}</p></div>
                </div>
            @endif

            <div class="row">
                <div class="col-6">
                    <p><strong>{{ __('Total') }}</strong>:</p>
                </div>
                <div class="col-6 float-end">
                    <p class="total-text raw-total-text" data-price="{{ $totalAmount }}">
                        {!! format_price($totalAmount) !!}
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-6">
                    <p>
                        {{ __('Refundable Deposit') }}
                        @if (($depositType ?? 'percentage') === 'fixed')
                            ({{ __('Fixed') }})
                        @else
                            ({{ (float) ($depositRate ?? 0) }}%)
                        @endif:
                    </p>
                </div>
                <div class="col-6 float-end">
                    <p class="price-text text-end">{{ format_price($depositAmount ?? 0) }}</p>
                </div>
            </div>

            @if (! empty($depositRiskLevel ?? null))
                <div class="row">
                    <div class="col-6">
                        <p>{{ __('Deposit risk tier') }}:</p>
                    </div>
                    <div class="col-6 float-end">
                        <p class="price-text text-end text-capitalize">{{ $depositRiskLevel }}</p>
                    </div>
                </div>
            @endif

            @if (! empty($depositRiskMultiplier ?? null) && (float) $depositRiskMultiplier > 1)
                <div class="row">
                    <div class="col-6">
                        <p>{{ __('Deposit multiplier') }}:</p>
                    </div>
                    <div class="col-6 float-end">
                        <p class="price-text text-end">x{{ number_format((float) $depositRiskMultiplier, 2) }}</p>
                    </div>
                </div>
            @endif

            @if (! empty($depositRiskReasons ?? null) && is_array($depositRiskReasons))
                <div class="row">
                    <div class="col-12">
                        <p class="small text-muted mb-0">{{ __('Deposit hold is adjusted by profile and vehicle risk factors: :reasons', ['reasons' => implode(', ', $depositRiskReasons)]) }}</p>
                    </div>
                </div>
            @endif

            @if (($depositAmount ?? 0) > 0)
                <div class="row">
                    <div class="col-12">
                        <p class="small text-muted mb-0">{{ __('This amount is placed as an authorization hold and will be settled after trip inspection.') }}</p>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-6">
                    <p><strong>{{ __('Final payable before payment') }}</strong>:</p>
                </div>
                <div class="col-6 float-end">
                    <p class="total-text text-end">
                        {!! format_price($finalPayableAmount ?? ($totalAmount + ($depositAmount ?? 0))) !!}
                    </p>
                </div>
            </div>

            <div class="row" id="price-lock-wrapper" data-expires-at="{{ $priceLockExpiresAt ?? '' }}" data-expired-message="{{ $priceLockExpiredMessage ?? __('Price lock expired or quote changed. We refreshed your total. Please review and try again.') }}">
                <div class="col-6"><p class="mb-0">{{ __('Price lock') }}:</p></div>
                <div class="col-6 float-end">
                    <p class="mb-0 text-end">
                        <span id="price-lock-countdown" class="badge bg-warning text-dark">--:--</span>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="mt-3 mb-5">
    <div class="checkout-discount-section">
        <a class="btn-open-coupon-form" href="#">{{ __('You have a coupon code?') }}</a>
    </div>
    <div class="coupon-wrapper mt-2" @if(! $couponCode) style="display: none;" @endif>
        @if(! $couponCode)
            @include('plugins/car-rentals::coupons.partials.apply-coupon')
        @else
            @include('plugins/car-rentals::coupons.partials.remove-coupon')
        @endif
    </div>
    <div class="clearfix"></div>
</div>