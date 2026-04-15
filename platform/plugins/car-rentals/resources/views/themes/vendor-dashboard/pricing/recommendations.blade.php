@extends('plugins/car-rentals::themes.vendor-dashboard.layouts.master')

@section('content')
    <div class="page-wrapper">
        <div class="container-xl">
            <!-- Page title -->
            <div class="page-header d-print-none">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title">
                            <i class="ti ti-trending-up me-2"></i> Demand Pricing Recommendations
                        </h2>
                        <div class="text-muted mt-1">Review and apply AI-powered pricing suggestions for maximum revenue</div>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-warning me-2">
                            <i class="ti ti-alert-circle me-1"></i> {{ $pendingCount }} Pending
                        </span>
                        <span class="badge bg-success">
                            <i class="ti ti-check me-1"></i> {{ $appliedCount }} Applied
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-body">
            <div class="container-xl">
                <!-- View Toggle & Filters -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="btn-group" role="group">
                            <a href="{{ route('car-rentals.vendor.demand-pricing.recommendations.index', ['view' => 'car']) }}"
                               class="btn btn-{{ $view === 'car' ? 'primary' : 'outline-primary' }}">
                                <i class="ti ti-car me-1"></i> By Car
                            </a>
                            <a href="{{ route('car-rentals.vendor.demand-pricing.recommendations.index', ['view' => 'date']) }}"
                               class="btn btn-{{ $view === 'date' ? 'primary' : 'outline-primary' }}">
                                <i class="ti ti-calendar me-1"></i> By Date
                            </a>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <form action="" method="GET" class="d-flex gap-2">
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending Only</option>
                                <option value="applied" {{ $status === 'applied' ? 'selected' : '' }}>Applied</option>
                                <option value="dismissed" {{ $status === 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                                <option value="" {{ !$status ? 'selected' : '' }}>All</option>
                            </select>
                        </form>
                    </div>
                </div>

                <!-- Recommendations List -->
                @if ($view === 'car')
                    <!-- Car View -->
                    <div class="row">
                        @forelse ($recommendations as $carId => $carRecommendations)
                            @php
                                $car = $cars->find($carId);
                            @endphp
                            <div class="col-md-12 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="ti ti-car me-2"></i> {{ $car?->name ?? 'Car #' . $carId }}
                                        </h3>
                                        <span class="badge bg-info ms-2">{{ $carRecommendations->count() }}</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-vcenter card-table">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Current Price</th>
                                                    <th>Recommended</th>
                                                    <th>Confidence</th>
                                                    <th>Est. Impact</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($carRecommendations as $rec)
                                                    @include('plugins/car-rentals::themes.vendor-dashboard.pricing.recommendation-card', ['recommendation' => $rec])
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info" role="alert">
                                    <i class="ti ti-info-circle me-2"></i>
                                    No recommendations found. Check back tomorrow for new suggestions!
                                </div>
                            </div>
                        @endforelse
                    </div>
                @else
                    <!-- Date View -->
                    <div class="row">
                        @forelse ($recommendations as $date => $dateRecommendations)
                            <div class="col-md-12 mb-3">
                                <div class="card">
                                    <div class="card-header">
                                        <h3 class="card-title">
                                            <i class="ti ti-calendar-event me-2"></i>
                                            {{ \Carbon\Carbon::parse($date)->format('l, F j, Y') }}
                                        </h3>
                                        <span class="badge bg-info ms-2">{{ $dateRecommendations->count() }}</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-vcenter card-table">
                                            <thead>
                                                <tr>
                                                    <th>Car</th>
                                                    <th>Current Price</th>
                                                    <th>Recommended</th>
                                                    <th>Confidence</th>
                                                    <th>Est. Impact</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($dateRecommendations as $rec)
                                                    <tr>
                                                        <td><strong>{{ $rec->car->name ?? 'N/A' }}</strong></td>
                                                        <td>${{ number_format($rec->local_baseline_price ?? 150, 2) }}</td>
                                                        <td><strong>${{ number_format($rec->recommended_value, 2) }}</strong></td>
                                                        <td>
                                                            <span class="badge bg-{{ $rec->confidence_label['color'] }}">
                                                                {{ intval($rec->confidence_score * 100) }}%
                                                            </span>
                                                        </td>
                                                        <td>${{ number_format($rec->estimated_revenue_impact ?? 0, 2) }}</td>
                                                        <td>
                                                            @include('plugins/car-rentals::themes.vendor-dashboard.pricing.recommendation-actions', ['recommendation' => $rec])
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="alert alert-info" role="alert">
                                    <i class="ti ti-info-circle me-2"></i>
                                    No recommendations found for the selected period.
                                </div>
                            </div>
                        @endforelse
                    </div>
                @endif

                <!-- Info Section -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <h5 class="alert-heading">
                                <i class="ti ti-bulb me-2"></i> How It Works
                            </h5>
                            <p class="mb-0">
                                Our AI analyzes real-time demand signals (views, bookings, supply pressure, weekends) to suggest optimal prices.
                                Each recommendation shows confidence level based on data strength.
                            </p>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="alert alert-warning">
                            <h5 class="alert-heading">
                                <i class="ti ti-clock me-2"></i> Time Limit
                            </h5>
                            <p class="mb-0">
                                Recommendations expire after 24 hours. Act quickly to capture the demand opportunity!
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
