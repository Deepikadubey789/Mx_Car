@php
    $route ??= 'invoices.generate';
    $buttonClass ??= 'btn-primary';
    $displayBookingStatus ??= true;
@endphp

@if ($booking)
    <x-core::datagrid class="mb-4">
        <x-core::datagrid.item :title="__('Booking Number')">
            {{ $booking->booking_number }}
        </x-core::datagrid.item>

        <x-core::datagrid.item :title="__('Time')">
            {{ $booking->created_at }}
        </x-core::datagrid.item>

        <x-core::datagrid.item :title="__('Full Name')">
            {{ $booking->customer_name }}
        </x-core::datagrid.item>

        <x-core::datagrid.item :title="__('Email')">
            <a href="mailto:{{ $booking->customer->email }}">{{ $booking->customer->email }}</a>
        </x-core::datagrid.item>

        @if ($booking->customer->phone)
            <x-core::datagrid.item :title="__('Phone')">
                <a href="tel:{{ $booking->customer->phone }}">{{ $booking->customer->phone }}</a>
            </x-core::datagrid.item>
        @endif
    </x-core::datagrid>

    <x-core::datagrid class="mb-4">
        <x-core::datagrid.item :title="__('Car')">
            {{ $booking->car->car_name }}
        </x-core::datagrid.item>

        <x-core::datagrid.item :title="__('Rental Start Date')">
            {{ $booking->car->rental_start_date_formatted }}
        </x-core::datagrid.item>

        <x-core::datagrid.item :title="__('Rental End Date')">
            {{ $booking->car->rental_end_date_formatted }}
        </x-core::datagrid.item>

        @if ($booking->note)
            <x-core::datagrid.item :title="__('Note')">
                {{ $booking->note }}
            </x-core::datagrid.item>
        @endif

        @if ($booking->coupon_code)
            <x-core::datagrid.item :title="__('Coupon Code')">
                <span class="badge bg-success-lt">
                    <x-core::icon name="ti ti-discount-2" />
                    {{ $booking->coupon_code }}
                </span>
            </x-core::datagrid.item>
        @endif
    </x-core::datagrid>

    <div class="mb-4">
        <h4>{{ __('Car') }}</h4>
        <x-core::table>
            <x-core::table.header>
                <x-core::table.header.cell class="text-center" style="width: 150px;">{{ __('Image') }}</x-core::table.header.cell>
                <x-core::table.header.cell>{{ __('Name') }}</x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">{{ __('Rental Start Date') }}</x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">{{ __('Rental End Date') }}</x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">{{ __('Price') }}</x-core::table.header.cell>
                <x-core::table.header.cell class="text-center">{{ __('Tax') }}</x-core::table.header.cell>
            </x-core::table.header>
            <x-core::table.body>
                <x-core::table.body.row>
                    @if ($booking->car->car)
                        <x-core::table.body.cell class="text-center" style="width: 150px; vertical-align: middle !important;">
                            <a href="{{ $booking->car->car->url }}" target="_blank">
                                <img src="{{ RvMedia::getImageUrl($booking->car->car->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $booking->car->car_name }}" width="140">
                            </a>
                        </x-core::table.body.cell>
                        <x-core::table.body.cell style="vertical-align: middle !important;">
                            <a class="booking-information-link" href="{{ $booking->car->car->url }}" target="_blank">{{ $booking->car->car_name }}</a>
                        </x-core::table.body.cell>
                    @else
                        <x-core::table.body.cell>
                            <img src="{{ RvMedia::getImageUrl($booking->car->car_image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $booking->car->car_name }}" width="140">
                        </x-core::table.body.cell>
                        <x-core::table.body.cell style="vertical-align: middle !important;">{{ $booking->car->car_name }}</x-core::table.body.cell>
                    @endif
                    <x-core::table.body.cell class="text-center" style="vertical-align: middle !important;">{{ $booking->car->rental_start_date_formatted }}</x-core::table.body.cell>
                    <x-core::table.body.cell class="text-center" style="vertical-align: middle !important;">{{ $booking->car->rental_end_date_formatted }}</x-core::table.body.cell>
                    <x-core::table.body.cell class="text-center" style="vertical-align: middle !important;"><strong>{{ format_price($booking->car->price, $booking->currency_id) }}</strong></x-core::table.body.cell>
                    <x-core::table.body.cell class="text-center" style="vertical-align: middle !important;"><strong>{{ format_price($booking->tax_amount, $booking->currency_id) }}</strong></x-core::table.body.cell>
                </x-core::table.body.row>
            </x-core::table.body>
        </x-core::table>
    </div>

    @if($services = $booking->services)
        @if($services->isNotEmpty())
            <div class="mb-4">
                <h4>{{ __('Services') }}</h4>
                <x-core::table>
                    <x-core::table.header>
                        <x-core::table.header.cell class="text-center" style="width: 150px;">{{ __('Image') }}</x-core::table.header.cell>
                        <x-core::table.header.cell>{{ __('Name') }}</x-core::table.header.cell>
                        <x-core::table.header.cell class="text-center">{{ __('Price') }}</x-core::table.header.cell>
                    </x-core::table.header>
                    <x-core::table.body>
                        @foreach($services as $service)
                            <x-core::table.body.row>
                                <x-core::table.body.cell class="text-center" style="width: 150px; vertical-align: middle !important;">
                                    <a href="{{ $service->url }}" target="_blank">
                                        <img src="{{ RvMedia::getImageUrl($service->logo, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $service->name }}" width="140">
                                    </a>
                                </x-core::table.body.cell>
                                <x-core::table.body.cell style="vertical-align: middle !important;">{{ $service->name }}</x-core::table.body.cell>
                                <x-core::table.body.cell class="text-center" style="vertical-align: middle !important;"><strong>{{ format_price($service->price, $booking->currency_id) }}</strong></x-core::table.body.cell>
                            </x-core::table.body.row>
                        @endforeach
                    </x-core::table.body>
                </x-core::table>
            </div>
        @endif
    @endif

    {{-- Cleaned Guest Protection Plan Table --}}
    @if ($booking->guest_protection_fee > 0)
        <div class="mb-4">
            <h4>{{ __('Guest Protection Plan') }}</h4>
            <x-core::table>
                <x-core::table.header>
                    <x-core::table.header.cell>{{ __('Coverage Details') }}</x-core::table.header.cell>
                    <x-core::table.header.cell class="text-center" style="width: 200px;">{{ __('Price Paid by Guest') }}</x-core::table.header.cell>
                </x-core::table.header>
                <x-core::table.body>
                    <x-core::table.body.row>
                        <x-core::table.body.cell style="vertical-align: middle !important;">
                            <i class="ti ti-shield-check text-success me-2"></i> {{ __('Vehicle Protection Coverage') }}
                            @if ($booking->guest_deductible_amount > 0)
                                <br><small class="text-muted ms-4">{{ __('Guest Out-of-pocket Deductible') }}: {{ format_price($booking->guest_deductible_amount, $booking->currency_id) }}</small>
                            @endif
                        </x-core::table.body.cell>
                        <x-core::table.body.cell class="text-center" style="vertical-align: middle !important;">
                            <strong>{{ format_price($booking->guest_protection_fee, $booking->currency_id) }}</strong>
                        </x-core::table.body.cell>
                    </x-core::table.body.row>
                </x-core::table.body>
            </x-core::table>
        </div>
    @endif

    {{-- Host Protection Plan Table (Crucial for Vendors) --}}
    @if ($booking->host_protection_plan_id)
        <div class="mb-4">
            <h4>{{ __('Your Protection Plan (Host)') }}</h4>
            <x-core::table>
                <x-core::table.header>
                    <x-core::table.header.cell>{{ __('Revenue Share') }}</x-core::table.header.cell>
                    <x-core::table.header.cell class="text-center" style="width: 200px;">{{ __('Your Deductible') }}</x-core::table.header.cell>
                </x-core::table.header>
                <x-core::table.body>
                    <x-core::table.body.row>
                        <x-core::table.body.cell style="vertical-align: middle !important;">
                            <i class="ti ti-shield-chevron text-info me-2"></i> {{ (float) $booking->host_revenue_share_percentage }}% {{ __('Payout') }}
                        </x-core::table.body.cell>
                        <x-core::table.body.cell class="text-center" style="vertical-align: middle !important;">
                            <strong>{{ format_price($booking->host_deductible_amount, $booking->currency_id) }}</strong>
                        </x-core::table.body.cell>
                    </x-core::table.body.row>
                </x-core::table.body>
            </x-core::table>
        </div>
    @endif

    @php
        $distanceUnit = (string) ($booking->distance_unit ?: 'km');
        $startMileageValue = $booking->start_mileage_snapshot ?? $booking->start_mileage;
        $completionMileageValue = $booking->completion_miles;
        $includedDistanceLimit = (int) ($booking->included_distance_limit ?? 0);
        $distanceTravelled = (int) ($booking->distance_travelled ?? 0);
        $distanceOverageUnits = (int) ($booking->distance_overage_units ?? 0);
        $extraDistanceUnitPrice = (float) ($booking->extra_distance_unit_price ?? 0);
        $distanceOverageAmount = round((float) ($booking->distance_overage_amount ?? 0), 2);
        $baseTripAmount = max(0, round(
            (float) ($booking->sub_total ?? 0)
            + (float) ($booking->tax_amount ?? 0)
            - (float) ($booking->coupon_amount ?? 0)
            + (float) ($booking->fee_amount ?? 0)
            + (float) ($booking->deposit_amount ?? 0),
            2
        ));
        $hasMileageBreakdown = $completionMileageValue !== null
            || $startMileageValue !== null
            || $includedDistanceLimit > 0
            || $distanceTravelled > 0
            || $distanceOverageUnits > 0
            || $distanceOverageAmount > 0
            || $extraDistanceUnitPrice > 0;
    @endphp

    <x-core::datagrid>
        <x-core::datagrid.item :title="__('Sub Total')">
            {{ format_price($booking->sub_total, $booking->currency_id) }}
        </x-core::datagrid.item>

        @if ($booking->coupon_amount > 0)
            <x-core::datagrid.item :title="__('Discount Amount')">
                <span class="text-success">
                    -{{ format_price($booking->coupon_amount, $booking->currency_id) }}
                    @if ($booking->coupon_code)
                        <small class="text-muted">({{ $booking->coupon_code }})</small>
                    @endif
                </span>
            </x-core::datagrid.item>
        @endif

        <x-core::datagrid.item :title="__('Tax Amount')">
            {{ format_price($booking->tax_amount, $booking->currency_id) }}
        </x-core::datagrid.item>

        @if ($booking->fee_amount > 0)
            <x-core::datagrid.item :title="$booking->fee_name ?: __('Service Fee')">
                {{ format_price($booking->fee_amount, $booking->currency_id) }}
            </x-core::datagrid.item>
        @endif

        {{-- NEW: Delivery Fee Breakdown --}}
        @if ($booking->delivery_location_id)
            <x-core::datagrid.item :title="__('Delivery Fee')">
                @if($booking->delivery_fee > 0)
                    {{ format_price($booking->delivery_fee, $booking->currency_id) }}
                @else
                    <span class="badge bg-success-lt fw-bold">{{ __('Free') }}</span>
                @endif
            </x-core::datagrid.item>
        @endif

        @if ($booking->deposit_amount > 0)
            <x-core::datagrid.item :title="__('Refundable Deposit') . ' ' . ($booking->deposit_type === 'fixed' ? '(' . __('Fixed') . ')' : '(' . (float) ($booking->deposit_rate ?? 0) . '%)')">
                {{ format_price($booking->deposit_amount, $booking->currency_id) }}
            </x-core::datagrid.item>
        @endif

        @if ($hasMileageBreakdown)
            <x-core::datagrid.item :title="__('Trip Total Before Mileage Extra')">
                {{ format_price($baseTripAmount, $booking->currency_id) }}
            </x-core::datagrid.item>

            @if ($startMileageValue !== null)
                <x-core::datagrid.item :title="__('Trip Start Mileage')">
                    {{ (int) $startMileageValue }} {{ $distanceUnit }}
                </x-core::datagrid.item>
            @endif

            @if ($completionMileageValue !== null)
                <x-core::datagrid.item :title="__('Trip End Mileage')">
                    {{ (int) $completionMileageValue }} {{ $distanceUnit }}
                </x-core::datagrid.item>
            @endif

            <x-core::datagrid.item :title="__('Included Distance Limit')">
                {{ $includedDistanceLimit }} {{ $distanceUnit }}
            </x-core::datagrid.item>

            <x-core::datagrid.item :title="__('Distance Travelled')">
                {{ $distanceTravelled }} {{ $distanceUnit }}
            </x-core::datagrid.item>

            <x-core::datagrid.item :title="__('Extra Distance Units')">
                {{ $distanceOverageUnits }} {{ $distanceUnit }}
            </x-core::datagrid.item>

            <x-core::datagrid.item :title="__('Extra Distance Rate')">
                {{ format_price($extraDistanceUnitPrice, $booking->currency_id) }}/{{ $distanceUnit }}
            </x-core::datagrid.item>

            <x-core::datagrid.item :title="__('Mileage Extra Amount')">
                {{ format_price($distanceOverageAmount, $booking->currency_id) }}
            </x-core::datagrid.item>
        @endif

        <x-core::datagrid.item :title="__('Total Amount')">
            {{ format_price($booking->amount, $booking->currency_id) }}
        </x-core::datagrid.item>

        @if($booking->status == \Botble\CarRentals\Enums\BookingStatusEnum::CANCELLED)
        <x-core::datagrid.item :title="__('Cancellation Policy')">
            @if($booking->cancellation_policy === 'free')
                <span class="badge bg-success">Full Refund</span>
            @elseif($booking->cancellation_policy === 'partial')
                <span class="badge bg-warning">50% Partial Refund</span>
            @else
                <span class="badge bg-danger">No Refund</span>
            @endif
        </x-core::datagrid.item>

        <x-core::datagrid.item :title="__('Refund Amount')">
            <strong class="text-success">{{ format_price($booking->refund_amount ?? 0, $booking->currency_id) }}</strong>
        </x-core::datagrid.item>

        @if($booking->cancellation_reason)
            <x-core::datagrid.item :title="__('Cancellation Reason')">
                {{ $booking->cancellation_reason }}
            </x-core::datagrid.item>
        @endif

        @if($booking->cancelled_at)
            <x-core::datagrid.item :title="__('Cancelled At')">
                {{ $booking->cancelled_at->format('M d, Y h:i A') }}
            </x-core::datagrid.item>
        @endif
    @endif

        <x-core::datagrid.item :title="__('Status')">
            {!! $booking->status->toHtml() !!}
        </x-core::datagrid.item>

        @if (is_plugin_active('payment') && $booking->payment->id)
            @auth
                <x-core::datagrid.item :title="__('Payment ID')">
                    <a href="{{ route('payment.show', $booking->payment->id) }}" target="_blank">
                        {{ $booking->payment->charge_id }}
                        <x-core::icon name="ti ti-external-link" />
                    </a>
                </x-core::datagrid.item>
            @endauth

            <x-core::datagrid.item :title="__('Payment method')">
                <span>{{ $booking->payment->payment_channel->label() }}</span>
            </x-core::datagrid.item>

            <x-core::datagrid.item :title="__('Payment status')">
                <span>{!! $booking->payment->status->toHtml() !!}</span>
            </x-core::datagrid.item>

            @if ($booking->payment->payment_channel == \Botble\Payment\Enums\PaymentMethodEnum::BANK_TRANSFER && $booking->payment->status == \Botble\Payment\Enums\PaymentStatusEnum::PENDING)
                <x-core::datagrid.item :title="__('Payment info')">
                    {!! BaseHelper::clean(get_payment_setting('description', $booking->payment->payment_channel)) !!}
                </x-core::datagrid.item>
            @endif
        @endif

        @if ($displayBookingStatus ?? false)
            <x-core::datagrid.item :title="__('Booking status')">
                {!! $booking->status->toHtml() !!}
            </x-core::datagrid.item>
        @endif
    </x-core::datagrid>

    @if ($booking->status == \Botble\CarRentals\Enums\BookingStatusEnum::COMPLETED)
        @include('plugins/car-rentals::bookings.partials.completion-details', ['booking' => $booking])
        @include('plugins/car-rentals::bookings.partials.completion-form', ['booking' => $booking])
    @endif

    @if(in_array($booking->status->getValue(), ['confirmed', 'processing', 'completed']))
        <div class="mt-4 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold mb-0">
                    <i class="ti ti-camera me-2"></i>Before Photos (Pickup)
                </h6>
                <button type="button" class="btn btn-sm btn-success" onclick="document.getElementById('pickupPhotosModal').style.setProperty('display','flex','important');window.scrollTo(0,0);">
                    + Add Pickup Photos
                </button>
            </div>

            @if($booking->pickup_photos && count($booking->pickup_photos) > 0)
                <div style="display:flex; flex-wrap:wrap; gap:10px;">
                    @foreach($booking->pickup_photos as $index => $photo)
                        <div style="position:relative; width:100px; height:100px; flex-shrink:0;">
                            <img src="{{ RvMedia::getImageUrl($photo, 'thumb') }}" style="width:100px; height:100px; object-fit:cover; border-radius:10px; display:block; border:1px solid #e2e8f0;">
                            <button type="button" onclick="deletePickupPhoto({{ $index }})" style="position: absolute; top: -8px; right: -8px; width: 24px; height: 24px; border-radius: 50%; background: #ffffff; border: 1.5px solid #cbd5e1; color: #64748b; font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: center; cursor: pointer; padding: 0; line-height: 1; box-shadow: 0 1px 4px rgba(0,0,0,0.12); transition: all 0.2s ease;" onmouseover="this.style.background='#fee2e2';this.style.borderColor='#f87171';this.style.color='#ef4444';" onmouseout="this.style.background='#ffffff';this.style.borderColor='#cbd5e1';this.style.color='#64748b';">✕</button>
                        </div>
                    @endforeach
                </div>
                @if($booking->pickup_photos_uploaded_at)
                    <small class="text-muted mt-2 d-block">
                        Uploaded: {{ $booking->pickup_photos_uploaded_at->format('M d, Y h:i A') }}
                    </small>
                @endif
            @else
                <div class="alert alert-light border">
                    <i class="ti ti-info-circle me-1"></i> No pickup photos added yet.
                </div>
            @endif
        </div>
    @endif

    <div class="btn-list mt-5">
    {{-- Trip Modification Approve/Reject --}}
        @if($booking->modification_status === 'pending' && in_array($booking->modification_type, ['extend', 'shorten']))
            <div class="alert alert-warning d-flex align-items-center justify-content-between gap-3 mb-3" style="border-radius:10px;">
                <div>
                    <strong><i class="ti ti-clock me-1"></i>Pending Request:</strong>
                    {{ ucfirst($booking->modification_type) }} trip —
                    <small class="text-muted">{{ $booking->modification_reason }}</small>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success btn-sm" onclick="handleModification('approve')">
                        <i class="ti ti-check me-1"></i>Approve
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="handleModification('reject')">
                        <i class="ti ti-x me-1"></i>Reject
                    </button>
                </div>
            </div>
        @endif

        @if ((auth()->check() || $booking->customer_id) && ($invoiceId = $booking->invoice->id) && $route)
            <x-core::button tag="a" :href="route($route, ['invoice' => $invoiceId, 'type' => 'print'])" target="_blank" icon="ti ti-printer" :class="$buttonClass ?? ''">
                {{ __('View Invoice') }}
            </x-core::button>
            <x-core::button tag="a" :href="route($route, ['invoice' => $invoiceId, 'type' => 'download'])" target="_blank" icon="ti ti-download" :class="$buttonClass ?? ''">
                {{ __('Download Invoice') }}
            </x-core::button>
        @endif

        @php
            $printRoute = $printBookingRoute ?? (auth()->check() ? 'car-rentals.bookings.print' : 'customer.bookings.print');
        @endphp

        <x-core::button tag="a" :href="route($printRoute, $booking->id)" target="_blank" icon="ti ti-file-text" color="info" :class="$buttonClass ?? ''">
            {{ __('plugins/car-rentals::booking.print_booking_info') }}
        </x-core::button>

        @if(in_array($booking->status->getValue(), ['confirmed', 'processing', 'completed']))
            <x-core::button type="button" icon="ti ti-key" color="warning" onclick="openKeyModal()" :class="$buttonClass ?? ''">
                {{ __('Send Key Instructions') }}
            </x-core::button>
        @endif
    </div>

    {{-- Pickup Photos Modal --}}
    <div id="pickupPhotosModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:12px;width:500px;max-width:95%;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
                <h6 class="mb-0"><i class="ti ti-camera me-2"></i>Upload Pickup Photos</h6>
                <button type="button" class="btn-close" onclick="document.getElementById('pickupPhotosModal').style.setProperty('display','none','important')"></button>
            </div>
            <form id="pickupPhotosForm" enctype="multipart/form-data">
                @csrf
                <div class="p-3">
                    <input type="file" name="pickup_photos[]" class="form-control" multiple accept="image/*" required>
                    <small class="text-muted">Select multiple photos</small>
                </div>
                <div class="p-3 border-top d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="document.getElementById('pickupPhotosModal').style.setProperty('display','none','important')">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm"><i class="ti ti-upload me-1"></i>Upload Photos</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Key Instructions Modal --}}
    <div id="keyInstructionsModal" class="d-none" tabindex="-1" style="position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:9999;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:12px;width:600px;max-width:95%;box-shadow:0 20px 60px rgba(0,0,0,0.2);max-height:90vh;overflow-y:auto;">
            <div class="modal-header" style="padding:16px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
                <h5 class="modal-title mb-0"><i class="ti ti-key me-2"></i>{{ __('Send Key Instructions') }}</h5>
                <button type="button" class="btn-close" onclick="closeKeyModal()"></button>
            </div>
            <form action="{{ (auth()->check() && auth()->user()->isSuperUser()) ? route('car-rentals.bookings.send-key-instructions', $booking->id) : route('car-rentals.vendor.bookings.send-key-instructions', $booking->id) }}" method="POST">
                @csrf
                <div class="modal-body" style="padding:20px;">
                    @if($booking->car->default_pickup_instructions)
                        <div class="alert alert-info mb-3">
                            <strong>{{ __('Default Instructions from Car:') }}</strong><br>
                            {{ $booking->car->default_pickup_instructions }}
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Key Instructions to Send') }} <span class="text-danger">*</span></label>
                        <textarea name="key_instructions" class="form-control" rows="6" required placeholder="Enter pickup location, key/lock box code, and special instructions...">{{ $booking->key_instructions ?? $booking->car->default_pickup_instructions }}</textarea>
                        <small class="text-muted">{{ __('This will be emailed to') }}: <strong>{{ $booking->customer_email }}</strong></small>
                    </div>
                    @if($booking->key_instructions_sent_at)
                        <div class="alert alert-success">
                            <i class="ti ti-check me-1"></i>
                            {{ __('Last sent:') }} {{ $booking->key_instructions_sent_at->format('M d, Y h:i A') }}
                        </div>
                    @endif
                </div>
                <div class="modal-footer" style="padding:16px 20px;border-top:1px solid #e2e8f0;display:flex;justify-content:flex-end;gap:8px;">
                    <button type="button" class="btn btn-secondary" onclick="closeKeyModal()">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-warning">
                        <i class="ti ti-send me-1"></i>{{ __('Send to Customer') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openKeyModal() {
            const m = document.getElementById('keyInstructionsModal');
            m.classList.remove('d-none');
            m.style.setProperty('display', 'flex', 'important');
            window.scrollTo(0, 0);
        }

        function closeKeyModal() {
            const m = document.getElementById('keyInstructionsModal');
            m.classList.add('d-none');
            m.style.setProperty('display', 'none', 'important');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const pickupForm = document.getElementById('pickupPhotosForm');
            if(pickupForm) {
                pickupForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(this);
                    const btn = this.querySelector('button[type="submit"]');
                    btn.disabled = true;
                    btn.innerHTML = '<i class="ti ti-loader me-1"></i>Uploading...';
                    fetch('{{ route("car-rentals.bookings.upload-pickup-photos", $booking->id) }}', {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('pickupPhotosModal').style.setProperty('display','none','important');
                            window.onbeforeunload = null;
                            location.reload();
                        } else {
                            alert('Error uploading photos.');
                            btn.disabled = false;
                            btn.innerHTML = '<i class="ti ti-upload me-1"></i>Upload Photos';
                        }
                    })
                    .catch(() => {
                        alert('Upload failed. Please try again.');
                        btn.disabled = false;
                        btn.innerHTML = '<i class="ti ti-upload me-1"></i>Upload Photos';
                    });
                });
            }

            const keyForm = document.querySelector('#keyInstructionsModal form');
            if(keyForm) {
                keyForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(keyForm);
                    const action = keyForm.getAttribute('action');
                    const btn = keyForm.querySelector('button[type="submit"]');
                    btn.disabled = true;
                    btn.innerHTML = '<i class="ti ti-loader me-1"></i> Sending...';
                    fetch(action, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    })
                    .then(res => {
                        closeKeyModal();
                        const toast = document.createElement('div');
                        toast.innerHTML = `
                            <div style="position:fixed;bottom:30px;right:30px;z-index:99999;background:#2fb344;color:#fff;padding:16px 24px;border-radius:8px;font-weight:600;font-size:15px;box-shadow:0 4px 15px rgba(0,0,0,0.2);display:flex;align-items:center;gap:10px;">
                                <i class="ti ti-circle-check" style="font-size:20px;"></i>
                                Key instructions sent successfully!
                            </div>`;
                        document.body.appendChild(toast);
                        setTimeout(() => toast.remove(), 4000);
                        btn.disabled = false;
                        btn.innerHTML = '<i class="ti ti-send me-1"></i> Send to Customer';
                    })
                    .catch(() => {
                        btn.disabled = false;
                        btn.innerHTML = '<i class="ti ti-send me-1"></i> Send to Customer';
                    });
                });
            }
        });

        function deletePickupPhoto(index) {
            fetch('{{ route("car-rentals.bookings.delete-pickup-photo", $booking->id) }}', {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ index: index }),
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Could not delete photo.');
                }
            })
            .catch(() => alert('Delete failed. Try again.'));
        }

        function handleModification(action) {
        const bookingId = {{ $booking->id }};

        if (action === 'approve') {
        sendModification(bookingId, action, '');
        } else {
            document.getElementById('rejectReasonInput').value = '';
            document.getElementById('rejectReasonModal').style.display = 'flex';
            document.getElementById('rejectConfirmBtn').onclick = function() {
                const reason = document.getElementById('rejectReasonInput').value;
                document.getElementById('rejectReasonModal').style.display = 'none';
                sendModification(bookingId, action, reason);
            };
        }
    }
        function showToast(message, type) {
            const bg = type === 'success' ? '#16a34a' : '#dc2626';
            const icon = type === 'success' ? 'ti-circle-check' : 'ti-circle-x';
            const toast = document.createElement('div');
            toast.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:99999;padding:14px 20px;border-radius:10px;font-size:14px;font-weight:500;color:#fff;display:flex;align-items:center;gap:10px;box-shadow:0 4px 20px rgba(0,0,0,0.2);';
            toast.style.background = bg;
            toast.innerHTML = `<i class="ti ${icon}" style="font-size:18px;"></i>${message}`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 2500);
        }

        function sendModification(bookingId, action, reason) {
            fetch(`/api/v1/car-rentals/admin/bookings/${bookingId}/modification/${action}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({ reason: reason }),
            })
            .then(r => r.json())
            .then(data => {
                if (data.error) {
                    showToast(data.message, 'danger');
                } else {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1500);
                }
            })
            .catch(() => showToast('Something went wrong.', 'danger'));
        }
    </script>

    {{-- Reject Reason Modal --}}
    <div id="rejectReasonModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:99999;align-items:center;justify-content:center;">
        <div style="background:#fff;border-radius:12px;width:440px;max-width:95%;box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <div style="padding:16px 20px;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:15px;font-weight:600;color:#111827;"><i class="ti ti-x-circle text-danger me-2"></i>Reject Modification</span>
                <button onclick="document.getElementById('rejectReasonModal').style.display='none'" style="background:none;border:none;cursor:pointer;font-size:18px;color:#6b7280;">&times;</button>
            </div>
            <div style="padding:20px;">
                <label style="font-size:13px;font-weight:600;color:#374151;display:block;margin-bottom:6px;">Reason for rejection <span style="color:#9ca3af;">(optional)</span></label>
                <textarea id="rejectReasonInput" class="form-control" rows="3" placeholder="Enter reason..."></textarea>
            </div>
            <div style="padding:12px 20px;border-top:1px solid #f3f4f6;display:flex;justify-content:flex-end;gap:8px;">
                <button onclick="document.getElementById('rejectReasonModal').style.display='none'" class="btn btn-secondary btn-sm">Cancel</button>
                <button id="rejectConfirmBtn" class="btn btn-danger btn-sm"><i class="ti ti-x me-1"></i>Confirm Reject</button>
            </div>
        </div>
    </div>
@endif