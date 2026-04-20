@extends('plugins/car-rentals::checkouts.layouts.master')

@section('title', __('Checkout'))

@section('content')
    @if (is_plugin_active('payment') && $totalAmount)
        @include('plugins/payment::partials.header')
    @endif

    <div class="row checkout-form-wrapper">
        <div class="col-lg-7">
            <div class="d-block">
                @include('plugins/car-rentals::checkouts.partials.logo')
            </div>

            {!! $checkoutForm->renderForm() !!}
        </div>

        <div id="booking-information-block"
             data-update-service-url="{{ route('public.ajax.booking.services.update', $token) }}"
             data-url="{{ route('public.ajax.booking.update', $token) }}" class="col-lg-5 col-md-6 order-1 order-md-2">
             
            @include('plugins/car-rentals::checkouts.partials.booking-information', [
                'car' => $car,
                'amount' => $amount,
                'totalAmount' => $totalAmount,
                'taxTitle' => $taxTitle,
                'taxAmount' => $taxAmount,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'couponCode' => $couponCode ?? null,
                'couponAmount' => $couponAmount ?? null,
                'token' => $token,
                'rentalCarAmount' => $rentalCarAmount,
                'serviceIds' => $serviceIds ?? [],
                'services' => $services ?? [],
                // --- NEW DATA PASSED DOWN ---
                'guestProtectionPlan' => $guest_protection_plan ?? null,
                'guestProtectionFee' => $guest_protection_fee ?? 0,
            ])
        </div>
    </div>

    @if (is_plugin_active('payment'))
        @include('plugins/payment::partials.footer')
    @endif
@stop

@push('footer')
    <script type="text/javascript" src="{{ asset('vendor/core/core/js-validation/js/js-validation.js') }}?v=1.0.1"></script>
    <script>
        (function () {
            function formatTime(totalSeconds) {
                const minutes = Math.floor(totalSeconds / 60);
                const seconds = totalSeconds % 60;

                return `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
            }

            function initPriceLockCountdown() {
                const wrapper = document.getElementById('price-lock-wrapper');
                const countdown = document.getElementById('price-lock-countdown');

                if (!wrapper || !countdown) {
                    return;
                }

                const expiresAt = wrapper.dataset.expiresAt;

                if (!expiresAt) {
                    countdown.textContent = '--:--';
                    return;
                }

                if (window.priceLockCountdownTimer) {
                    clearInterval(window.priceLockCountdownTimer);
                }

                const endTime = new Date(expiresAt).getTime();

                if (!Number.isNaN(endTime) && endTime > Date.now()) {
                    window.__priceLockExpiredReloadDone = false;
                }

                function tick() {
                    const now = Date.now();
                    const remainingSeconds = Math.max(0, Math.floor((endTime - now) / 1000));

                    if (remainingSeconds <= 0) {
                        countdown.classList.remove('bg-warning', 'text-dark');
                        countdown.classList.add('bg-danger', 'text-white');
                        countdown.textContent = '{{ __('Expired') }}';
                        if (window.priceLockCountdownTimer) {
                            clearInterval(window.priceLockCountdownTimer);
                            window.priceLockCountdownTimer = null;
                        }
                        if (!window.__priceLockExpiredReloadDone) {
                            window.__priceLockExpiredReloadDone = true;
                            window.location.reload();
                        }
                        return;
                    }

                    countdown.classList.remove('bg-danger', 'text-white');
                    countdown.classList.add('bg-warning', 'text-dark');
                    countdown.textContent = formatTime(remainingSeconds);
                }

                tick();
                window.priceLockCountdownTimer = setInterval(tick, 1000);
            }

            document.addEventListener('DOMContentLoaded', function () {
                initPriceLockCountdown();

                const bookingBlock = document.getElementById('booking-information-block');

                if (!bookingBlock) {
                    return;
                }

                const observer = new MutationObserver(function () {
                    initPriceLockCountdown();
                });

                observer.observe(bookingBlock, {
                    childList: true,
                    subtree: false,
                });
            });
        })();
    </script>
@endpush