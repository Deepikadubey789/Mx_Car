@php
    $layout = CarRentalsHelper::viewPath('vendor-dashboard.layouts.master');
@endphp

@extends('plugins/car-rentals::cars.form')

@section('content')
    @parent

    {{-- Demand Pricing Recommendations Section --}}
    @if ($form->getModel()?->id)
        <hr class="my-4" />
        <div class="row mb-4">
            <div class="col-12">
                @include('plugins/car-rentals::themes.vendor-dashboard.partials.car-recommendations-section', [
                    'model' => $form->getModel(),
                    'carRecommendations' => $carRecommendations ?? [],
                    'carRecommendationCount' => $carRecommendationCount ?? 0,
                ])
            </div>
        </div>
    @endif
@stop
