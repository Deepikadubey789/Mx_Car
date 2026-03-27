<p class="text-muted">
    @if($isActivated)
        {{ trans('plugins/car-rentals::vendor.control.block_help') }}
    @else
        {!! BaseHelper::clean(trans('plugins/car-rentals::vendor.control.blocked_help', ['reason' => "<strong>$blockReason</strong>"])) !!}
    @endif
</p>

<x-core::button
    type="button"
    :color="$isActivated ? 'danger' : 'info'"
    size="sm"
    data-bs-toggle="modal"
    data-bs-target="#vendor-control-modal"
>
    @if($isActivated)
        {{ trans('plugins/car-rentals::vendor.control.block') }}
    @else
        {{ trans('plugins/car-rentals::vendor.control.unblock') }}
    @endif
</x-core::button>
