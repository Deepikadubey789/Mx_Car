@php
    $claims = $booking?->claims ?? collect();
    // Public claims view: no internal admin notes/assignment, only safe status + amounts.
@endphp

<div class="mt-4">
    <h6 class="mb-3">{{ __('plugins/car-rentals::disputes.tab_claims') }}</h6>

    @if ($claims->isEmpty())
        <div class="text-muted small">
            {{ __('plugins/car-rentals::disputes.claims_none') }}
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>{{ __('plugins/car-rentals::disputes.claim_category') }}</th>
                        <th>{{ __('plugins/car-rentals::disputes.claim_status') }}</th>
                        <th>{{ __('plugins/car-rentals::disputes.claim_priority') }}</th>
                        <th class="text-end">{{ __('plugins/car-rentals::disputes.claim_claimed_amount') }}</th>
                        <th class="text-end">{{ __('plugins/car-rentals::disputes.claim_approved_amount') }}</th>
                        <th>{{ __('plugins/car-rentals::disputes.outcome_action') }}</th>
                        <th>{{ __('plugins/car-rentals::disputes.claim_resolution_due') }}</th>
                        <th>{{ __('plugins/car-rentals::disputes.updated_at') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($claims as $claim)
                        @php
                            $statusLabel = str($claim->status)->replace('_', ' ')->title();
                            $priorityLabel = trans('plugins/car-rentals::disputes.priority_' . ($claim->priority ?: 'normal'));
                            $outcomeLabel = $claim->outcome_action
                                ? trans('plugins/car-rentals::disputes.outcome_' . $claim->outcome_action)
                                : '—';
                        @endphp
                        <tr>
                            <td class="text-nowrap">{{ $claim->category ?: '—' }}</td>
                            <td class="text-nowrap">
                                <span class="badge bg-secondary text-white">{{ $statusLabel }}</span>
                            </td>
                            <td class="text-nowrap">{{ $priorityLabel }}</td>
                            <td class="text-end text-nowrap">
                                {{ $claim->claimed_amount !== null ? format_price($claim->claimed_amount, $booking->currency_id) : '—' }}
                            </td>
                            <td class="text-end text-nowrap">
                                {{ $claim->approved_amount !== null ? format_price($claim->approved_amount, $booking->currency_id) : '—' }}
                            </td>
                            <td class="text-nowrap">{{ $outcomeLabel }}</td>
                            <td class="text-nowrap">
                                {{ $claim->resolution_due_at ? $claim->resolution_due_at->format('M j, Y g:i A') : '—' }}
                            </td>
                            <td class="text-nowrap">
                                {{ $claim->updated_at ? $claim->updated_at->format('M j, Y g:i A') : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

