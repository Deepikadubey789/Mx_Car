<!-- Demand Pricing Recommendations Widget -->
@if ($pendingCount > 0)
    <div class="col-md-12 mb-3">
        <div class="card border-info">
            <div class="card-header bg-info">
                <div class="row align-items-center">
                    <div class="col">
                        <h3 class="card-title text-white">
                            <i class="ti ti-trending-up me-2"></i> Demand Pricing Recommendations
                        </h3>
                    </div>
                    <div class="col-auto">
                        <span class="badge bg-white text-info">{{ $pendingCount }} Pending</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted mb-3">
                    <i class="ti ti-info-circle me-1"></i>
                    AI-powered pricing suggestions based on real-time demand signals to maximize your revenue.
                </p>

                @if ($pendingRecommendations->isNotEmpty())
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Car</th>
                                    <th>Current</th>
                                    <th>Suggested</th>
                                    <th>Confidence</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendingRecommendations as $rec)
                                    <tr>
                                        <td>
                                            <strong>{{ $rec->car->name ?? 'Car #' . $rec->car_id }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $rec->recommendation_date->format('M d, Y') }}</small>
                                        </td>
                                        <td>${{ number_format($rec->local_baseline_price ?? 150, 2) }}</td>
                                        <td>
                                            <strong class="text-success">${{ number_format($rec->recommended_value, 2) }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                @php
                                                    $basePrice = $rec->local_baseline_price ?? 150;
                                                    $priceDiff = (($rec->recommended_value - $basePrice) / $basePrice) * 100;
                                                @endphp
                                                @if ($priceDiff > 0)
                                                    ↑ {{ number_format($priceDiff, 1) }}%
                                                @else
                                                    ↓ {{ number_format(abs($priceDiff), 1) }}%
                                                @endif
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $rec->confidence_score >= 0.75 ? 'success' : 'info' }}">
                                                {{ intval($rec->confidence_score * 100) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('car-rentals.vendor.demand-pricing.recommendations.index') }}"
                                               class="btn btn-sm btn-icon btn-outline-primary"
                                               title="View recommendation details">
                                                <i class="ti ti-arrow-right"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('car-rentals.vendor.demand-pricing.recommendations.index') }}" class="btn btn-info btn-sm w-100">
                            <i class="ti ti-external-link me-1"></i> View All Recommendations ({{ $pendingCount }})
                        </a>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="ti ti-info-circle me-2"></i>
                        No pending recommendations at this time. Check back tomorrow!
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif
