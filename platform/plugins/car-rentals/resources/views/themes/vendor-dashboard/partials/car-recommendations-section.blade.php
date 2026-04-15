@if ($carRecommendationCount > 0)
    <div class="card mb-3 border-info">
        <div class="card-header bg-info bg-opacity-10 border-info">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="card-title mb-0">
                    <x-core::icon name="ti ti-trend-up" class="me-2 text-info" />
                    {{ __('Pending Demand Pricing Recommendations') }}
                </h6>
                <span class="badge bg-info">{{ $carRecommendationCount }}</span>
            </div>
        </div>
        <div class="card-body">
            @if ($carRecommendationCount > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead>
                            <tr class="text-muted text-uppercase">
                                <th style="width: 20%;">{{ __('Date Range') }}</th>
                                <th style="width: 15%;">{{ __('Current Price') }}</th>
                                <th style="width: 15%;">{{ __('Suggested Price') }}</th>
                                <th style="width: 15%;">{{ __('Confidence') }}</th>
                                <th style="width: 20%;">{{ __('Revenue Impact') }}</th>
                                <th style="width: 15%;" class="text-end">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($carRecommendations as $rec)
                                <tr>
                                    <td>
                                        <small class="text-muted">
                                            {{ \Carbon\Carbon::parse($rec['from_date'])->format('M d') }} -
                                            {{ \Carbon\Carbon::parse($rec['to_date'])->format('M d, Y') }}
                                        </small>
                                    </td>
                                    <td>
                                        <strong>{{ format_price($rec['current_price'] ?? $rec['local_baseline_price'] ?? 0) }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-success text-white">
                                            {{ format_price($rec['recommended_price']) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $confidence = $rec['confidence_score'] ?? 0;
                                            $confColor = $confidence >= 0.80 ? 'success' : ($confidence >= 0.60 ? 'info' : 'warning');
                                        @endphp
                                        <span class="badge bg-{{ $confColor }}">{{ round($confidence * 100) }}%</span>
                                    </td>
                                    <td>
                                        <small>
                                            @if (isset($rec['estimated_revenue_impact']) && $rec['estimated_revenue_impact'] > 0)
                                                <span class="text-success">+{{ format_price($rec['estimated_revenue_impact']) }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('car-rentals.vendor.demand-pricing.recommendations.index') }}"
                                           class="btn btn-sm btn-info"
                                           data-bs-toggle="tooltip"
                                           title="{{ __('View & Manage Recommendations') }}">
                                            <x-core::icon name="ti ti-arrow-right" />
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3 text-center">
                    <a href="{{ route('car-rentals.vendor.demand-pricing.recommendations.index', ['car' => $model->id]) }}"
                       class="btn btn-info btn-sm">
                        <x-core::icon name="ti ti-external-link" class="me-1" />
                        {{ __('Manage All Recommendations for This Car') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif
