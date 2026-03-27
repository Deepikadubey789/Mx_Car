@if (! auth()->check() || auth()->user()->hasPermission('car-rentals.car-maintenance-histories.create'))
    <x-core::button data-bs-toggle="modal" :data-bs-target="$modalTarget">
        {{ $label }}
    </x-core::button>
@endif
