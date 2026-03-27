<x-core::button
    tag="a"
    href="{{ route('car-rentals.withdrawal.invoice', $withdrawal->getKey()) }}?type=print"
    target="_blank"
    icon="ti ti-printer"
>
    {{ trans('plugins/car-rentals::withdrawal.print_invoice') }}
</x-core::button>
<x-core::button
    tag="a"
    :href="route('car-rentals.withdrawal.invoice', $withdrawal->getKey())"
    target="_blank"
    icon="ti ti-download"
>
    {{ trans('plugins/car-rentals::withdrawal.download_invoice') }}
</x-core::button>
