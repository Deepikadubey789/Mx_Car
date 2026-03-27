{{--
    Rental Period Information Component

    Props:
    - $startDate: Carbon instance for rental start date/time
    - $endDate: Carbon instance for rental end date/time
    - $minWidth: (optional) Minimum width for labels, default '75px'
--}}

@php
    $minWidth = $minWidth ?? '75px';
@endphp

<div class="rental-period-info">
    <div class="d-flex align-items-start mb-1">
        <span class="text-muted me-2" style="min-width: {{ $minWidth }};">{{ __('Pick-up:') }}</span>
        <span class="fw-medium">
            {{ $startDate->format('M d, Y') }}
            <span class="text-primary">{{ $startDate->format('H:i') }}</span>
        </span>
    </div>
    <div class="d-flex align-items-start">
        <span class="text-muted me-2" style="min-width: {{ $minWidth }};">{{ __('Drop-off:') }}</span>
        <span class="fw-medium">
            {{ $endDate->format('M d, Y') }}
            <span class="text-primary">{{ $endDate->format('H:i') }}</span>
        </span>
    </div>
</div>
