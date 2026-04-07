@extends(CarRentalsHelper::viewPath('vendor-dashboard.layouts.master'))

@php
    // Fetch the actual review object instead of just checking if it exists
    $customerReview = \Botble\CarRentals\Models\CustomerReview::where('booking_id', $booking->id)
        ->where('vendor_id', auth('customer')->id())
        ->first();
        
    $hasRatedCustomer = $customerReview !== null;
    $isCompleted = $booking->status->getValue() === \Botble\CarRentals\Enums\BookingStatusEnum::COMPLETED;
@endphp

@section('content')
    <div class="row">
        <div class="col-md-12">
            <x-core::card>
               <x-core::card.header class="d-flex justify-content-between align-items-center">
                    <x-core::card.title>
                        {{ trans('plugins/car-rentals::booking.booking_details') }} {{ $booking->booking_number }}
                    </x-core::card.title>
                    
                    {{-- Show Customer Reputation Badge (Now Clickable) --}}
                    @if($booking->customer && $booking->customer->total_reviews > 0)
                        <button type="button" class="btn btn-light d-flex align-items-center px-3 py-1 rounded border shadow-sm transition" data-bs-toggle="modal" data-bs-target="#allCustomerReviewsModal">
                            <span class="text-muted me-2 text-sm">{{ __('Customer Rating:') }}</span>
                            <span class="text-warning fw-bold d-flex align-items-center">
                                <i class="ti ti-star-filled me-1"></i> {{ number_format($booking->customer->average_rating, 1) }}
                            </span>
                            <span class="text-muted ms-1 text-sm">({{ $booking->customer->total_reviews }} {{ __('Reviews') }})</span>
                        </button>
                    @elseif($booking->customer)
                        <div class="bg-light px-3 py-1 rounded border text-muted text-sm">
                            {{ __('New Customer (No reviews yet)') }}
                        </div>
                    @endif
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
                    @elseif ($isCompleted && !$hasRatedCustomer)
                        {{-- Rate Customer Button --}}
                        <div class="btn-list mb-3">
                            <button type="button" class="btn btn-primary" id="btnRateCustomer">
                                <i class="ti ti-star"></i>
                                {{ __('Rate Customer') }}
                            </button>
                        </div>
                    @elseif ($isCompleted && $hasRatedCustomer)
                        {{-- NEW: Display the actual review with a Delete button --}}
                        <div class="mb-4 p-3 bg-light rounded border">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1">{{ __('Your Review for :customer', ['customer' => $booking->customer_name]) }}</h6>
                                    <div class="text-warning mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="ti ti-star{{ $i <= $customerReview->star ? '-filled' : '' }}"></i>
                                        @endfor
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteReviewModal">
                                    <i class="ti ti-trash"></i> {{ __('Delete Review') }}
                                </button>
                            </div>
                            @if($customerReview->content)
                                <p class="mb-0 text-muted fst-italic">"{{ $customerReview->content }}"</p>
                            @else
                                <p class="mb-0 text-muted small">{{ __('No written comment provided.') }}</p>
                            @endif
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

    <div class="row mt-4">
        <div class="col-md-12">
            @include('plugins/car-rentals::partials.trip-messaging', [
                'booking' => $booking,
                'fetchUrl' => route('car-rentals.vendor.bookings.messages.index', $booking->id),
                'storeUrl' => route('car-rentals.vendor.bookings.messages.store', $booking->id),
                'escalateUrl' => route('car-rentals.vendor.bookings.messages.escalate', $booking->id)
            ])
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

    {{-- Rate Customer Modal --}}
    @if ($isCompleted && !$hasRatedCustomer)
        <div class="modal fade" id="rateCustomerModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti ti-star me-2 text-warning"></i>
                            {{ __('Rate Customer') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('car-rentals.vendor.bookings.rate-customer', $booking->id) }}" method="POST" class="booking-action-form">
                        @csrf
                        <div class="modal-body">
                            <p>{{ __('How was your experience renting to') }} <strong>{{ $booking->customer_name }}</strong>?</p>
                            
                            <div class="mb-3">
                                <label class="form-label required">{{ __('Rating') }}</label>
                                <select name="star" class="form-select" required>
                                    <option value="5">5 {{ __('Stars') }} - {{ __('Excellent') }}</option>
                                    <option value="4">4 {{ __('Stars') }} - {{ __('Good') }}</option>
                                    <option value="3">3 {{ __('Stars') }} - {{ __('Average') }}</option>
                                    <option value="2">2 {{ __('Stars') }} - {{ __('Poor') }}</option>
                                    <option value="1">1 {{ __('Star') }} - {{ __('Terrible') }}</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Review (Optional)') }}</label>
                                <textarea name="content" class="form-control" rows="3" placeholder="{{ __('Leave a comment about the customer...') }}"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary btn-submit-booking-action">
                                {{ __('Submit Review') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- NEW: Delete Review Modal --}}
    @if ($isCompleted && $hasRatedCustomer)
        <div class="modal fade" id="deleteReviewModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti ti-trash me-2 text-danger"></i>
                            {{ __('Delete Review') }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ __('Are you sure you want to delete this review? This action cannot be undone.') }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                        <form action="{{ route('car-rentals.vendor.bookings.delete-customer-review', $booking->id) }}" method="POST" style="display: inline-block;" class="booking-action-form">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-submit-booking-action">
                                <i class="ti ti-trash me-2"></i> {{ __('Delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- NEW: All Customer Reviews Modal --}}
    @if($booking->customer && $booking->customer->total_reviews > 0)
        <div class="modal fade" id="allCustomerReviewsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="ti ti-star-filled me-2 text-warning"></i>
                            {{ __('Reviews for :name', ['name' => $booking->customer->name]) }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-0">
                        <ul class="list-group list-group-flush">
                            {{-- Loop through all published reviews from newest to oldest --}}
                            @foreach($booking->customer->receivedReviews->where('status', 'published')->sortByDesc('created_at') as $review)
                                <li class="list-group-item p-4">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="d-flex align-items-center">
                                            @php $reviewer = $review->vendor; @endphp
                                            {{-- Vendor Avatar --}}
                                            <div class="me-3" style="width: 45px; height: 45px; border-radius: 50%; overflow: hidden; background: #f1f1f1;">
                                                <img src="{{ $reviewer ? $reviewer->avatar_url : asset('vendor/core/images/default-avatar.jpg') }}" alt="Avatar" style="width: 100%; height: 100%; object-fit: cover;">
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $reviewer ? $reviewer->name : __('Unknown Dealer') }}</h6>
                                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                        <div class="text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="ti ti-star{{ $i <= $review->star ? '-filled' : '' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    @if($review->content)
                                        <p class="mb-0 mt-3 text-dark">{{ $review->content }}</p>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="modal-footer border-top-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
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

                @if ($isCompleted && !$hasRatedCustomer)
                    $('#btnRateCustomer').on('click', function() {
                        $('#rateCustomerModal').modal('show');
                    });
                @endif

                $('.booking-action-form').on('submit', function(e) {
                    var $form = $(this);
                    var $submitButton = $form.find('.btn-submit-booking-action');
                    var $icon = $submitButton.find('i');
                    
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