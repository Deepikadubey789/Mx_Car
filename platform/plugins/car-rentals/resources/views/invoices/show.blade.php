@extends(BaseHelper::getAdminMasterLayoutTemplate())

@push('header-action')
    <x-core::button
        tag="a"
        :href="route('invoices.generate', ['invoice' => $invoice->id, 'type' => 'print'])"
        target="_blank"
        icon="ti ti-printer"
    >
        {{ trans('plugins/car-rentals::invoice.print') }}
    </x-core::button>
    <x-core::button
        tag="a"
        :href="route('invoices.generate', ['invoice' => $invoice->id, 'type' => 'download'])"
        target="_blank"
        icon="ti ti-download"
    >
        {{ trans('plugins/car-rentals::invoice.download') }}
    </x-core::button>
@endpush

@section('content')
    @php
        $bookingReference = $invoice->reference instanceof \Botble\CarRentals\Models\Booking
            ? $invoice->reference
            : null;
        $priceSnapshot = is_array($bookingReference?->price_snapshot) ? $bookingReference->price_snapshot : [];
        $policyDiscountSource = (string) ($priceSnapshot['policy_discount_source'] ?? '');
        $policyDiscountSourceLabel = match (true) {
            $policyDiscountSource === 'weekly' => trans('plugins/car-rentals::invoice.discount_sources.weekly'),
            $policyDiscountSource === 'monthly' => trans('plugins/car-rentals::invoice.discount_sources.monthly'),
            str_starts_with($policyDiscountSource, 'trip-rule:') => trans('plugins/car-rentals::invoice.discount_sources.trip_rule'),
            $policyDiscountSource === 'combined' => trans('plugins/car-rentals::invoice.discount_sources.combined'),
            default => trans('plugins/car-rentals::invoice.discount_sources.none'),
        };
        $distanceBillingMode = (string) ($bookingReference?->distance_overage_billing_mode ?: 'end_of_trip');
        $distanceBillingModeLabel = trans('plugins/car-rentals::invoice.billing_modes.' . $distanceBillingMode);
    @endphp

    <x-core::card size="lg">
        <x-core::card.body>
            <div class="row">
                <div class="col-6 offset-6 text-end">
                    <p class="h3">{{ trans('plugins/car-rentals::invoice.heading') }}</p>
                    <p class="mb-1">{{ $invoice->customer_name }}</p>
                    <p class="mb-1">{{ $invoice->customer_email }}</p>
                    <p class="mb-1">{{ $invoice->customer_phone }}</p>
                </div>
            </div>

            <div class="my-5">
                <div class="row">
                    <div class="col-lg-3">
                        <strong>{{ trans('plugins/car-rentals::invoice.code') }}:</strong>
                        #{{ $invoice->code }}
                    </div>
                    <div class="col-lg-3">
                        <strong>{{ trans('plugins/car-rentals::invoice.status') }}:</strong>
                        {!! $invoice->status->toHtml() !!}
                    </div>
                    <div class="col-lg-3">
                        <strong>{{ trans('plugins/car-rentals::invoice.purchase_at') }}:</strong>
                        {{ $invoice->created_at->translatedFormat('j F, Y') }}
                    </div>

                    @if ($invoice->payment)
                        <div class="col-lg-3">
                            <strong>{{ trans('plugins/car-rentals::invoice.payment_method') }}:</strong>
                            {{ $invoice->payment->payment_channel->label() }}
                        </div>
                    @endif
                </div>
            </div>

            <x-core::table class="table-transparent" :striped="false" :hover="false">
                <x-core::table.header>
                    <x-core::table.header.cell>
                        {{ trans('plugins/car-rentals::invoice.item.name') }}
                    </x-core::table.header.cell>
                    <x-core::table.header.cell>
                        {{ trans('plugins/car-rentals::invoice.item.qty') }}
                    </x-core::table.header.cell>
                    <x-core::table.header.cell class="text-center">
                        {{ trans('plugins/car-rentals::invoice.amount') }}
                    </x-core::table.header.cell>
                </x-core::table.header>

                <x-core::table.body>
                    @foreach ($invoice->items as $item)
                        <x-core::table.body.row>
                            <x-core::table.body.cell>
                                <p class="mb-0">{{ $item->name }}</p>
                                @if ($item->description)
                                    <small>{{ $item->description }}</small>
                                @endif
                            </x-core::table.body.cell>
                            <td>{{ number_format($item->qty) }}</td>
                            <td class="text-center">
                                <strong>{{ format_price($item->sub_total, $invoice->currency_id) }}</strong>
                            </td>
                        </x-core::table.body.row>
                    @endforeach

                    <x-core::table.body.row>
                        <x-core::table.body.cell class="text-end" colspan="2">
                            {{ trans('plugins/car-rentals::invoice.sub_total') }}:
                        </x-core::table.body.cell>
                        <x-core::table.body.cell class="text-center">
                            <strong>{{ format_price($invoice->sub_total, $invoice->currency_id) }}</strong>
                        </x-core::table.body.cell>
                    </x-core::table.body.row>
                    <x-core::table.body.row>
                        <x-core::table.body.cell class="text-end" colspan="2">
                            {{ trans('plugins/car-rentals::invoice.tax_amount') }}:
                        </x-core::table.body.cell>
                        <x-core::table.body.cell class="text-center">
                            <strong>{{ format_price($invoice->tax_amount, $invoice->currency_id) }}</strong>
                        </x-core::table.body.cell>
                    </x-core::table.body.row>
                    @if ($bookingReference && $bookingReference->fee_amount > 0)
                        <x-core::table.body.row>
                            <x-core::table.body.cell class="text-end" colspan="2">
                                {{ $bookingReference->fee_name ?: __('Service Fee') }}:
                            </x-core::table.body.cell>
                            <x-core::table.body.cell class="text-center">
                                <strong>{{ format_price($bookingReference->fee_amount, $invoice->currency_id) }}</strong>
                            </x-core::table.body.cell>
                        </x-core::table.body.row>
                    @endif
                    @if ($bookingReference && $bookingReference->deposit_amount > 0)
                        <x-core::table.body.row>
                            <x-core::table.body.cell class="text-end" colspan="2">
                                {{ __('Refundable Deposit') }}
                                {{ $bookingReference->deposit_type === 'fixed' ? '(' . __('Fixed') . ')' : '(' . (float) ($bookingReference->deposit_rate ?? 0) . '%)' }}:
                            </x-core::table.body.cell>
                            <x-core::table.body.cell class="text-center">
                                <strong>{{ format_price($bookingReference->deposit_amount, $invoice->currency_id) }}</strong>
                            </x-core::table.body.cell>
                        </x-core::table.body.row>
                    @endif
                    @if ($invoice->discount_amount > 0)
                        <x-core::table.body.row>
                            <x-core::table.body.cell class="text-end" colspan="2">
                                {{ trans('plugins/car-rentals::invoice.discount_amount') }}:
                            </x-core::table.body.cell>
                            <x-core::table.body.cell class="text-center">
                                <strong>{{ format_price($invoice->discount_amount, $invoice->currency_id) }}</strong>
                                <p>({{ $invoice->reference->coupon_code }})</p>
                            </x-core::table.body.cell>
                        </x-core::table.body.row>
                    @endif
                    <x-core::table.body.row>
                        <x-core::table.body.cell class="text-end" colspan="2">
                            {{ trans('plugins/car-rentals::invoice.total_amount') }}:
                        </x-core::table.body.cell>
                        <x-core::table.body.cell class="text-center">
                            <strong>{{ format_price($invoice->amount, $invoice->currency_id) }}</strong>
                        </x-core::table.body.cell>
                    </x-core::table.body.row>
                </x-core::table.body>
            </x-core::table>
        </x-core::card.body>
    </x-core::card>

    @if ($bookingReference)
        <x-core::card size="lg" class="mt-3">
            <x-core::card.header>
                <x-core::card.title>
                    {{ trans('plugins/car-rentals::invoice.pricing_details') }}
                </x-core::card.title>
            </x-core::card.header>
            <x-core::card.body>
                <div class="row g-3">
                    <div class="col-md-6">
                        <h5 class="mb-2">{{ trans('plugins/car-rentals::invoice.discount_breakdown') }}</h5>
                        <x-core::table :striped="false" :hover="false">
                            <x-core::table.body>
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>{{ trans('plugins/car-rentals::invoice.discount_source') }}</x-core::table.body.cell>
                                    <x-core::table.body.cell class="text-end">{{ $policyDiscountSourceLabel }}</x-core::table.body.cell>
                                </x-core::table.body.row>
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>{{ trans('plugins/car-rentals::invoice.discount_cap') }}</x-core::table.body.cell>
                                    <x-core::table.body.cell class="text-end">
                                        {{ array_key_exists('policy_discount_cap_percent', $priceSnapshot) && $priceSnapshot['policy_discount_cap_percent'] !== null
                                            ? (float) $priceSnapshot['policy_discount_cap_percent'] . '%'
                                            : trans('plugins/car-rentals::invoice.na') }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>{{ trans('plugins/car-rentals::invoice.policy_discount_amount') }}</x-core::table.body.cell>
                                    <x-core::table.body.cell class="text-end">
                                        {{ format_price((float) ($priceSnapshot['policy_discount_amount'] ?? 0), $invoice->currency_id) }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                            </x-core::table.body>
                        </x-core::table>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-2">{{ trans('plugins/car-rentals::invoice.mileage_policy') }}</h5>
                        <x-core::table :striped="false" :hover="false">
                            <x-core::table.body>
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>{{ trans('plugins/car-rentals::booking.included_distance_limit') }}</x-core::table.body.cell>
                                    <x-core::table.body.cell class="text-end">
                                        {{ $bookingReference->included_distance_limit !== null
                                            ? ((int) $bookingReference->included_distance_limit . ' ' . ($bookingReference->distance_unit ?: 'km'))
                                            : trans('plugins/car-rentals::invoice.na') }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>{{ trans('plugins/car-rentals::invoice.extra_distance_rate') }}</x-core::table.body.cell>
                                    <x-core::table.body.cell class="text-end">
                                        {{ format_price((float) ($bookingReference->extra_distance_unit_price ?? 0), $invoice->currency_id) }}/{{ $bookingReference->distance_unit ?: 'km' }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>{{ trans('plugins/car-rentals::invoice.billing_mode') }}</x-core::table.body.cell>
                                    <x-core::table.body.cell class="text-end">{{ $distanceBillingModeLabel }}</x-core::table.body.cell>
                                </x-core::table.body.row>
                            </x-core::table.body>
                        </x-core::table>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-2">{{ trans('plugins/car-rentals::invoice.trip_mileage_summary') }}</h5>
                        <x-core::table :striped="false" :hover="false">
                            <x-core::table.body>
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>{{ trans('plugins/car-rentals::booking.start_mileage') }}</x-core::table.body.cell>
                                    <x-core::table.body.cell class="text-end">
                                        {{ $bookingReference->start_mileage_snapshot ?? $bookingReference->start_mileage ?? trans('plugins/car-rentals::invoice.na') }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>{{ trans('plugins/car-rentals::booking.distance_travelled') }}</x-core::table.body.cell>
                                    <x-core::table.body.cell class="text-end">
                                        {{ (int) ($bookingReference->distance_travelled ?? 0) }} {{ $bookingReference->distance_unit ?: 'km' }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>{{ trans('plugins/car-rentals::booking.distance_overage_units') }}</x-core::table.body.cell>
                                    <x-core::table.body.cell class="text-end">
                                        {{ (int) ($bookingReference->distance_overage_units ?? 0) }} {{ $bookingReference->distance_unit ?: 'km' }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>{{ trans('plugins/car-rentals::booking.distance_overage_amount') }}</x-core::table.body.cell>
                                    <x-core::table.body.cell class="text-end">
                                        {{ format_price((float) ($bookingReference->distance_overage_amount ?? 0), $invoice->currency_id) }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                            </x-core::table.body>
                        </x-core::table>
                    </div>

                    <div class="col-md-6">
                        <h5 class="mb-2">{{ trans('plugins/car-rentals::invoice.deposit_breakdown') }}</h5>
                        <x-core::table :striped="false" :hover="false">
                            <x-core::table.body>
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>{{ trans('plugins/car-rentals::invoice.deposit_type') }}</x-core::table.body.cell>
                                    <x-core::table.body.cell class="text-end">
                                        {{ $bookingReference->deposit_type === 'fixed' ? __('Fixed') : ((float) ($bookingReference->deposit_rate ?? 0) . '%') }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>{{ trans('plugins/car-rentals::invoice.deposit_base_amount') }}</x-core::table.body.cell>
                                    <x-core::table.body.cell class="text-end">
                                        {{ format_price((float) ($bookingReference->deposit_base_amount ?? 0), $invoice->currency_id) }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>{{ trans('plugins/car-rentals::booking.deposit_hold_status') }}</x-core::table.body.cell>
                                    <x-core::table.body.cell class="text-end">{{ $bookingReference->deposit_hold_status ?: trans('plugins/car-rentals::invoice.na') }}</x-core::table.body.cell>
                                </x-core::table.body.row>
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>{{ trans('plugins/car-rentals::invoice.deposit_authorized_amount') }}</x-core::table.body.cell>
                                    <x-core::table.body.cell class="text-end">
                                        {{ format_price((float) ($bookingReference->deposit_hold_amount ?? 0), $invoice->currency_id) }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>{{ trans('plugins/car-rentals::invoice.deposit_captured_amount') }}</x-core::table.body.cell>
                                    <x-core::table.body.cell class="text-end">
                                        {{ format_price((float) ($bookingReference->deposit_captured_amount ?? 0), $invoice->currency_id) }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                                <x-core::table.body.row>
                                    <x-core::table.body.cell>{{ trans('plugins/car-rentals::invoice.deposit_released_amount') }}</x-core::table.body.cell>
                                    <x-core::table.body.cell class="text-end">
                                        {{ format_price((float) ($bookingReference->deposit_released_amount ?? 0), $invoice->currency_id) }}
                                    </x-core::table.body.cell>
                                </x-core::table.body.row>
                            </x-core::table.body>
                        </x-core::table>
                    </div>
                </div>
            </x-core::card.body>
        </x-core::card>
    @endif
@stop
