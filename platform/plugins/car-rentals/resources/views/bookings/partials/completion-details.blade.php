@php
    $canEditCompletion = is_in_admin(true) && auth()->check() && auth()->user()->hasPermission('car-rentals.bookings.edit');
    $hasCompletionData = $booking->completion_miles || $booking->completion_gas_level || $booking->completion_damage_images || $booking->completion_notes;
    $hasDepositHoldData = $booking->deposit_hold_status || (float) $booking->deposit_hold_amount > 0;
    
    // Helper function to get status badge class and label
    $getStatusBadge = function($status) {
        $statusMap = [
            'pending_authorization' => ['warning', 'Pending Authorization'],
            'authorized' => ['warning', 'Authorized Hold'],
            'release_pending_provider_expiry' => ['info', 'Release Pending'],
            'released' => ['success', 'Released'],
            'captured' => ['success', 'Captured'],
            'captured_directly' => ['success', 'Captured (No Hold)'],
        ];
        return $statusMap[$status] ?? ['secondary', ucwords(str_replace('_', ' ', $status))];
    };
@endphp

@if ($hasDepositHoldData)
    <fieldset class="form-fieldset mb-4 mt-4" style="border-left: 4px solid #f0ad4e;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 style="margin-bottom: 0;">
                <i class="ti ti-lock"></i> {{ trans('plugins/car-rentals::booking.deposit_hold_information') }}
            </h4>
        </div>

        <x-core::datagrid>
            @if ($booking->deposit_hold_status)
                @php
                    [$badgeColor, $statusLabel] = $getStatusBadge($booking->deposit_hold_status);
                @endphp
                <x-core::datagrid.item :title="trans('plugins/car-rentals::booking.deposit_hold_status')">
                    <span class="label label-{{ $badgeColor }}">{{ $statusLabel }}</span>
                </x-core::datagrid.item>
            @endif

            @if ((float) $booking->deposit_hold_amount > 0)
                @php
                    $amountTitle = $booking->deposit_hold_status === 'captured_directly'
                        ? trans('plugins/car-rentals::booking.deposit_captured_amount')
                        : trans('plugins/car-rentals::booking.deposit_authorized_amount');
                @endphp
                <x-core::datagrid.item :title="$amountTitle">
                    <strong style="font-size: 1.1em; color: #d9534f;">{{ format_price($booking->deposit_hold_amount, $booking->currency_id) }}</strong>
                </x-core::datagrid.item>
            @endif

            @if ((float) $booking->deposit_captured_amount > 0)
                <x-core::datagrid.item :title="trans('plugins/car-rentals::booking.deposit_captured_amount')">
                    <span class="text-success" style="font-weight: 600;">
                        <i class="ti ti-check-circle"></i> {{ format_price($booking->deposit_captured_amount, $booking->currency_id) }}
                    </span>
                </x-core::datagrid.item>
            @endif

            @if ((float) $booking->deposit_released_amount > 0)
                <x-core::datagrid.item :title="trans('plugins/car-rentals::booking.deposit_released_amount')">
                    <span class="text-info" style="font-weight: 600;">
                        <i class="ti ti-arrow-back-up"></i> {{ format_price($booking->deposit_released_amount, $booking->currency_id) }}
                    </span>
                </x-core::datagrid.item>
            @endif

            @if ($booking->deposit_authorized_at)
                <x-core::datagrid.item :title="trans('plugins/car-rentals::booking.deposit_authorized_date')">
                    <small>{{ $booking->deposit_authorized_at->format('M d, Y @ H:i') }}</small>
                </x-core::datagrid.item>
            @endif

            @if ($booking->deposit_settled_at)
                <x-core::datagrid.item :title="trans('plugins/car-rentals::booking.deposit_settlement_date')">
                    <small>{{ $booking->deposit_settled_at->format('M d, Y @ H:i') }}</small>
                </x-core::datagrid.item>
            @endif
        </x-core::datagrid>
    </fieldset>
@endif

<fieldset class="form-fieldset mb-4 mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>{{ trans('plugins/car-rentals::booking.completion_details') }}</h4>
        @if ($canEditCompletion && !$hasCompletionData)
            <x-core::button
                type="button"
                color="primary"
                size="sm"
                data-bs-toggle="modal"
                data-bs-target="#completion-modal"
                icon="ti ti-plus"
            >
                {{ trans('plugins/car-rentals::booking.add_completion_details') }}
            </x-core::button>
        @endif
    </div>

    @if ($hasCompletionData)
        <x-core::datagrid>
            @if ($booking->completion_miles)
                @php
                    $isUsingMiles = \Botble\CarRentals\Facades\CarRentalsHelper::isUsingMiles();
                    $labelKey = $isUsingMiles ? 'completion_miles' : 'completion_kilometers';
                    $unitKey = $isUsingMiles ? 'miles' : 'kilometers';
                @endphp
                <x-core::datagrid.item :title="trans('plugins/car-rentals::booking.' . $labelKey)">
                    {{ number_format($booking->completion_miles) }} {{ trans('plugins/car-rentals::booking.' . $unitKey) }}
                </x-core::datagrid.item>
            @endif

            @if ($booking->completion_gas_level)
                <x-core::datagrid.item :title="trans('plugins/car-rentals::booking.completion_gas_level')">
                    {{ $booking->completion_gas_level }}
                </x-core::datagrid.item>
            @endif
            
            @if ($booking->checkin_fuel_level)
                <x-core::datagrid.item title="Check-in Fuel Level">
                    {{ ucfirst(str_replace('_', ' ', $booking->checkin_fuel_level)) }}
                </x-core::datagrid.item>
            @endif

            @if ((float) $booking->fuel_difference_charge > 0)
                <x-core::datagrid.item title="Fuel Difference Charge">
                    <strong style="color: #d9534f;">
                        {{ format_price($booking->fuel_difference_charge, $booking->currency_id) }}
                    </strong>
                </x-core::datagrid.item>
            @endif

            @if ($booking->actual_return_datetime)
                <x-core::datagrid.item title="Actual Return Time">
                    {{ $booking->actual_return_datetime->format('M d, Y H:i') }}
                </x-core::datagrid.item>
            @endif

            @if ((float) $booking->late_fee_charge > 0)
                <x-core::datagrid.item title="Late Return Fee">
                    <strong style="color: #d9534f;">
                        {{ format_price($booking->late_fee_charge, $booking->currency_id) }}
                    </strong>
                </x-core::datagrid.item>
            @endif

            @if ((float) $booking->damage_amount > 0)
            <x-core::datagrid.item title="Damage Amount">
                <strong style="color: #d9534f;">
                    {{ format_price($booking->damage_amount, $booking->currency_id) }}
                </strong>
            </x-core::datagrid.item>
        @endif

        @if ($booking->damage_status)
            <x-core::datagrid.item title="Damage Status">
                @php
                    $statusColors = [
                        'pending' => 'warning',
                        'accepted' => 'success',
                        'disputed' => 'danger',
                        'resolved' => 'info',
                    ];
                    $color = $statusColors[$booking->damage_status] ?? 'secondary';
                @endphp
                <span class="label label-{{ $color }}">
                    {{ ucfirst($booking->damage_status) }}
                </span>
            </x-core::datagrid.item>
        @endif

            @if ($booking->completion_notes)
                <x-core::datagrid.item :title="trans('plugins/car-rentals::booking.completion_notes')">
                    {{ $booking->completion_notes }}
                </x-core::datagrid.item>
            @endif

            @if ($booking->completed_at)
                <x-core::datagrid.item :title="trans('plugins/car-rentals::booking.completed_at')">
                    {{ $booking->completed_at->format('Y-m-d H:i:s') }}
                </x-core::datagrid.item>
            @endif
        </x-core::datagrid>

        @if ($booking->completion_damage_images)
            @php
                $damageImages = is_string($booking->completion_damage_images)
                    ? json_decode($booking->completion_damage_images, true)
                    : $booking->completion_damage_images;
            @endphp

            @if ($damageImages && count($damageImages) > 0)
                <div class="mt-3">
                    <h5>{{ trans('plugins/car-rentals::booking.damage_images') }}</h5>
                    <div class="row">
                        @foreach ($damageImages as $image)
                            <div class="col-md-3 col-sm-6 mb-3">
                                <div class="card">
                                    <img
                                        src="{{ RvMedia::getImageUrl($image, 'thumb', false, RvMedia::getDefaultImage()) }}"
                                        alt="{{ trans('plugins/car-rentals::booking.damage_image') }}"
                                        class="card-img-top"
                                        style="height: 200px; object-fit: cover; cursor: pointer;"
                                        onclick="window.open('{{ RvMedia::getImageUrl($image) }}', '_blank')"
                                    >
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        @if ($canEditCompletion)
            <div class="mt-3">
                <x-core::button
                    type="button"
                    color="warning"
                    size="sm"
                    data-bs-toggle="modal"
                    data-bs-target="#completion-modal"
                    icon="ti ti-edit"
                >
                    {{ trans('plugins/car-rentals::booking.edit_completion_details') }}
                </x-core::button>
            </div>
        @endif
    @else
        <x-core::alert type="info">
            {{ trans('plugins/car-rentals::booking.no_completion_details') }}
        </x-core::alert>
    @endif
</fieldset>

@if ($canEditCompletion)
    @include('plugins/car-rentals::bookings.partials.completion-modal', ['booking' => $booking])
@endif
