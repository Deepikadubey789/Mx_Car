@php
    $style = request()->query('style') ?: theme_option('car_detail_style', 'style-1');

    $style = in_array($style, ['style-1', 'style-2', 'style-3', 'style-4']) ? $style : 'style-1';
    $detailStyleClass = "car-detail-modern car-detail-modern--$style";
@endphp

{!! apply_filters('ads_render', null, 'car_before', ['class' => 'mb-2']) !!}

<div class="{{ $detailStyleClass }}">
    @include(Theme::getThemeNamespace('views.car-rentals.car-detail.styles.' . $style), compact('car', 'reviews'))
</div>

{!! apply_filters('ads_render', null, 'car_before', ['class' => 'mt-2']) !!}
