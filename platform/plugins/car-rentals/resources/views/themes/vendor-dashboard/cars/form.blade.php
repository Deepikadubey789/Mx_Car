@php
    $layout = CarRentalsHelper::viewPath('vendor-dashboard.layouts.master');
@endphp

@extends('plugins/car-rentals::cars.form')

@section('content')
    @parent

    @php
        $model = $form->getModel();
        $isCarForm = $model instanceof \Botble\CarRentals\Models\Car;
    @endphp

    {{-- Demand Pricing Recommendations Section --}}
    @if ($isCarForm && $model?->id)
        <div id="demand-pricing-card">
            <hr class="my-4" />
            <div class="row mb-4">
                <div class="col-12">
                    @include('plugins/car-rentals::themes.vendor-dashboard.partials.car-recommendations-section', [
                        'model' => $model,
                        'carRecommendations' => $carRecommendations ?? [],
                        'carRecommendationCount' => $carRecommendationCount ?? 0,
                    ])
                </div>
            </div>
        </div>
    @endif

    {{-- NEW: Delivery Settings Section --}}
    @php
        $car = $isCarForm ? $model : null;
    @endphp
    
    @if ($car && $car->id)
        <div id="delivery-settings-card" class="card shadow-sm border-0 mb-4" style="border-radius: 1rem;">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="ti ti-truck-delivery text-primary me-2"></i>{{ __('Delivery Settings') }}
                </h5>
            </div>
            <div class="card-body">
                
                {{-- Enable Delivery Toggle --}}
                <div class="form-check form-switch mb-4">
                    <input class="form-check-input" type="checkbox" role="switch" id="is_delivery_enabled" name="is_delivery_enabled" value="1" @checked(old('is_delivery_enabled', $car->is_delivery_enabled ?? false))>
                    <label class="form-check-label fw-bold" for="is_delivery_enabled">{{ __('Offer Delivery for this Car') }}</label>
                </div>

                <div id="delivery-settings-wrapper" style="display: {{ old('is_delivery_enabled', $car->is_delivery_enabled ?? false) ? 'block' : 'none' }};">
                    
                    <div class="row mb-4">
                        {{-- Free Delivery Threshold --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('Free Delivery Threshold (Days)') }}</label>
                            <div class="input-group">
                                <input type="number" min="1" name="free_delivery_days_threshold" class="form-control" value="{{ old('free_delivery_days_threshold', $car->free_delivery_days_threshold ?? '') }}" placeholder="e.g. 7">
                                <span class="input-group-text">{{ __('Days') }}</span>
                            </div>
                            <small class="text-muted">{{ __('Waive delivery fees if the trip is longer than this.') }}</small>
                        </div>

                        {{-- Max Distance --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">{{ __('Max Delivery Distance') }}</label>
                            <div class="input-group">
                                <input type="number" min="1" name="max_delivery_distance_miles" class="form-control" value="{{ old('max_delivery_distance_miles', $car->max_delivery_distance_miles ?? '') }}" placeholder="e.g. 25">
                                <span class="input-group-text">{{ __('Miles') }}</span>
                            </div>
                            <small class="text-muted">{{ __('For custom address deliveries.') }}</small>
                        </div>
                    </div>

                   {{-- Select Delivery Zones --}}
                    <h6 class="fw-bold mb-3">{{ __('Supported Delivery Zones') }}</h6>
                    <div class="row g-3">
                        @php
                            $availableLocations = \Botble\CarRentals\Models\DeliveryLocation::where('vendor_id', auth('customer')->id())->get();
                            $selectedLocations = isset($car) ? $car->deliveryLocations->pluck('id')->toArray() : [];
                        @endphp
                        
                        @forelse($availableLocations as $location)
                            <div class="col-md-6">
                                {{-- FIX: Replaced floats with d-flex and flex-grow --}}
                                <div class="form-check border rounded p-3 bg-light d-flex align-items-center mb-0">
                                    <input class="form-check-input m-0" type="checkbox" name="delivery_locations[]" value="{{ $location->id }}" id="loc-{{ $location->id }}" @checked(in_array($location->id, old('delivery_locations', $selectedLocations))) style="width: 1.25em; height: 1.25em;">
                                    <label class="form-check-label d-flex justify-content-between flex-grow-1 ms-3" for="loc-{{ $location->id }}" style="cursor: pointer;">
                                        <strong>{{ $location->name }}</strong>
                                        <span class="text-success fw-bold">+{{ get_application_currency()->symbol }}{{ number_format($location->fee_amount, 2) }}</span>
                                    </label>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-warning mb-0">
                                    {{ __('You haven\'t created any delivery zones yet.') }} 
                                    <a href="{{ route('car-rentals.vendor.delivery-locations.index') }}">{{ __('Create one here') }}</a>.
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // 1. Show/Hide settings when toggle is clicked
                const toggle = document.getElementById('is_delivery_enabled');
                const wrapper = document.getElementById('delivery-settings-wrapper');
                
                if(toggle && wrapper) {
                    toggle.addEventListener('change', function() {
                        wrapper.style.display = this.checked ? 'block' : 'none';
                    });
                }

                // 2. DOM Repositioning Logic
                const deliveryCard = document.getElementById('delivery-settings-card');
                
                // Find all headers on the page
                const headers = Array.from(document.querySelectorAll('h4, h5, .card-title, .title, .meta-box-title'));
                
                // Look for the Maintenance Histories header
                const maintenanceHeader = headers.find(el => el.textContent.includes('Maintenance Histories'));

                if (deliveryCard && maintenanceHeader) {
                    // Find the main parent container of the maintenance section
                    const maintenanceCard = maintenanceHeader.closest('.card, .meta-box, .wrap');
                    if (maintenanceCard && maintenanceCard.parentNode) {
                        // Insert the delivery card right before the maintenance card
                        maintenanceCard.parentNode.insertBefore(deliveryCard, maintenanceCard);
                    }
                }
            });
        </script>
    @endif
@stop