@php
    $canEditCompletion = is_in_admin(true) && auth()->check() && auth()->user()->hasPermission('car-rentals.bookings.edit');
    $hasCompletionData = $booking->completion_miles || $booking->completion_gas_level || $booking->completion_damage_images || $booking->completion_notes;
@endphp

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
