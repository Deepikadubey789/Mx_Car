<span class="rating {{ $cssClass ?? '' }}">
    <x-core::icon name="ti ti-star" size="16" class="icon icon-tabler icons-tabler-filled icon-tabler-star" />
    <span class="rating-count">{{ round($car->reviews_sum_star / ($car->reviews_count ?: 1), 2) }}</span>
    <span class="text-sm-medium neutral-500">({{ $car->reviews_count == 1 ? __(':number review', ['number' => $car->reviews_count]) : __(':number reviews', ['number' => $car->reviews_count]) }})</span>
</span>
