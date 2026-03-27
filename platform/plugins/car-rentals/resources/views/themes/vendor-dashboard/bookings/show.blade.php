@extends(CarRentalsHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
    <div class="row">
        <div class="col-md-12">
            <x-core::card>
                <x-core::card.header>
                    <x-core::card.title>
                        {{ trans('plugins/car-rentals::booking.booking_details') }} {{ $booking->booking_number }}
                    </x-core::card.title>
                </x-core::card.header>

                <x-core::card.body>
                    @if ($booking->canBeApproved())
                        <x-core::alert type="warning" class="mb-3">
                            <strong>{{ trans('plugins/car-rentals::booking.pending_approval_notice') }}</strong>
                            <p class="mb-0">{{ trans('plugins/car-rentals::booking.pending_approval_description') }}</p>
                        </x-core::alert>

                        <div class="btn-list mb-3">
                            <button type="button" class="btn btn-success" id="btnApproveBooking">
                                <i class="ti ti-check"></i>
                                {{ trans('plugins/car-rentals::booking.approve_booking') }}
                            </button>

                            <button type="button" class="btn btn-danger" id="btnCancelBooking">
                                <i class="ti ti-x"></i>
                                {{ trans('plugins/car-rentals::booking.cancel_booking') }}
                            </button>
                        </div>
                    @elseif ($booking->canBeCancelled())
                        <div class="btn-list mb-3">
                            <button type="button" class="btn btn-danger" id="btnCancelBooking">
                                <i class="ti ti-x"></i>
                                {{ trans('plugins/car-rentals::booking.cancel_booking') }}
                            </button>
                        </div>
                    @endif

                    @include('plugins/car-rentals::bookings.information', [
                        'booking' => $booking,
                        'route' => 'car-rentals.vendor.invoices.generate',
                        'printBookingRoute' => 'car-rentals.vendor.bookings.print',
                        'buttonClass' => 'btn-primary'
                    ])
                </x-core::card.body>
            </x-core::card>
        </div>
    </div>

    @if ($booking->canBeApproved())
        <div class="modal fade" id="approveBookingModal" tabindex="-1" aria-labelledby="approveBookingModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approveBookingModalLabel">
                            <i class="ti ti-check me-2"></i>
                            {{ trans('plugins/car-rentals::booking.approve_booking') }}
                        </h5>
                        <button type="button" class="btn-close" id="btnCloseApproveModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ trans('plugins/car-rentals::booking.approve_booking_confirmation') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="btnCancelApprove">
                            {{ trans('core/base::base.no') }}
                        </button>
                        <form action="{{ route('car-rentals.vendor.bookings.approve', $booking->id) }}" method="POST" style="display: inline-block;" class="booking-action-form">
                            @csrf
                            <button type="submit" class="btn btn-success btn-submit-booking-action">
                                <i class="ti ti-check me-2"></i>
                                {{ trans('core/base::base.yes') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($booking->canBeCancelled())
        <div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-labelledby="cancelBookingModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelBookingModalLabel">
                            <i class="ti ti-x me-2"></i>
                            {{ trans('plugins/car-rentals::booking.cancel_booking') }}
                        </h5>
                        <button type="button" class="btn-close" id="btnCloseCancelModal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ trans('plugins/car-rentals::booking.cancel_booking_confirmation') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="btnCancelCancel">
                            {{ trans('core/base::base.no') }}
                        </button>
                        <form action="{{ route('car-rentals.vendor.bookings.cancel', $booking->id) }}" method="POST" style="display: inline-block;" class="booking-action-form">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-submit-booking-action">
                                <i class="ti ti-x me-2"></i>
                                {{ trans('core/base::base.yes') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            'use strict';

            $(document).ready(function() {
                @if ($booking->canBeApproved())
                    $('#btnApproveBooking').on('click', function() {
                        $('#approveBookingModal').modal('show');
                    });

                    $('#btnCloseApproveModal, #btnCancelApprove').on('click', function() {
                        $('#approveBookingModal').modal('hide');
                    });
                @endif

                @if ($booking->canBeCancelled())
                    $('#btnCancelBooking').on('click', function() {
                        $('#cancelBookingModal').modal('show');
                    });

                    $('#btnCloseCancelModal, #btnCancelCancel').on('click', function() {
                        $('#cancelBookingModal').modal('hide');
                    });
                @endif

                $('.booking-action-form').on('submit', function(e) {
                    var $form = $(this);
                    var $submitButton = $form.find('.btn-submit-booking-action');
                    var $icon = $submitButton.find('i');
                    var originalIconClass = $icon.attr('class');
                    var originalHtml = $submitButton.html();

                    $submitButton.prop('disabled', true);

                    $icon.attr('class', 'spinner-border spinner-border-sm me-2');
                    $icon.attr('role', 'status');
                    $icon.attr('aria-hidden', 'true');

                    var textContent = $submitButton.contents().filter(function() {
                        return this.nodeType === 3;
                    }).first();

                    if (textContent.length) {
                        textContent[0].nodeValue = '{{ trans('plugins/car-rentals::booking.processing') }}';
                    }

                    var $modal = $submitButton.closest('.modal');
                    if ($modal.length) {
                        $modal.find('.btn-close, .btn-secondary').prop('disabled', true);
                    }
                });
            });
        </script>
    @endpush
@endsection