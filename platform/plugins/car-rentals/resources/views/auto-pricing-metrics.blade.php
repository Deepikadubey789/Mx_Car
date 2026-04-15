@extends('core/base::layouts.master')

@section('content')
    <div class="page-wrapper">
        <div class="container-xl">
            <!-- Page title -->
            <div class="page-header d-print-none">
                <div class="row align-items-center">
                    <div class="col">
                        <h2 class="page-title">
                            <i class="ti ti-chart-line me-2"></i> Auto-Pricing Metrics
                        </h2>
                        <div class="text-muted mt-1">Real-time analytics for demand-aware pricing auto-application</div>
                    </div>
                    <div class="col-auto">
                        @if ($globalPaused)
                            <span class="badge bg-danger me-2">
                                <i class="ti ti-alert-circle me-1"></i> GLOBALLY PAUSED
                            </span>
                        @else
                            <span class="badge bg-success">
                                <i class="ti ti-circle-check me-1"></i> ACTIVE
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="page-body">
            <div class="container-xl">
                <!-- Global Controls -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title mb-3">
                                    <i class="ti ti-player-pause me-2"></i> Emergency Controls
                                </h5>
                                <form action="{{ route('car-rentals.auto-pricing.toggle-pause') }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    <input type="hidden" name="paused" value="{{ !$globalPaused ? '1' : '0' }}">
                                    <button type="submit" class="btn btn-{{ $globalPaused ? 'success' : 'warning' }}">
                                        <i class="ti ti-{{ $globalPaused ? 'player-play' : 'player-pause' }} me-1"></i>
                                        {{ $globalPaused ? 'Resume Auto-Apply' : 'Pause Auto-Apply' }}
                                    </button>
                                </form>
                                <small class="text-muted d-block mt-2">
                                    {{ $globalPaused 
                                        ? 'Auto-apply is currently paused. No recommendations will be auto-applied globally.' 
                                        : 'Auto-apply is running normally. Pausing will stop all new auto-applications.' }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Date Range Filter -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('car-rentals.auto-pricing.metrics') }}" method="GET" class="row g-2">
                                    <div class="col-md-5">
                                        <label class="form-label">From Date</label>
                                        <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label">To Date</label>
                                        <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="ti ti-filter me-1"></i> Filter
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Metrics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-truncate">
                                    <h3 class="card-title text-muted font-weight-normal">Recommendations Applied</h3>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <div class="h1 me-2">{{ $metrics['applied_count'] }}</div>
                                    <div class="text-muted">// auto-applied</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-truncate">
                                    <h3 class="card-title text-muted font-weight-normal">Avg Confidence</h3>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <div class="h1 me-2">{{ number_format($metrics['avg_confidence'], 2) }}</div>
                                    <div class="text-muted">// 0-1 scale</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-truncate">
                                    <h3 class="card-title text-muted font-weight-normal">Total Value Applied</h3>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <div class="h1 me-2">${{ number_format($metrics['total_value_applied'], 2) }}</div>
                                    <div class="text-muted">// sum of prices</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-truncate">
                                    <h3 class="card-title text-muted font-weight-normal">Success Rate</h3>
                                </div>
                                <div class="d-flex align-items-baseline">
                                    <div class="h1 me-2">{{ $metrics['success_rate'] }}%</div>
                                    <div class="text-muted">// of pending</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cars Status -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title mb-3">
                                    <i class="ti ti-status-change me-2"></i> Auto-Apply Status
                                </h3>
                                <div class="mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span>Enabled & Active</span>
                                        <span class="badge bg-success">{{ $metrics['cars_with_auto_apply'] }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span>Paused</span>
                                        <span class="badge bg-warning">{{ $metrics['cars_paused'] }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <span>Disabled</span>
                                        <span class="badge bg-secondary">{{ $metrics['cars_disabled'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title mb-3">
                                    <i class="ti ti-list me-2"></i> Recommendation Reasons
                                </h3>
                                @if ($metrics['reason_codes'])
                                    <div class="mb-3">
                                        @foreach ($metrics['reason_codes'] as $reason => $count)
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="text-truncate">{{ ucfirst(str_replace('_', ' ', $reason)) }}</span>
                                                <span class="badge bg-info">{{ $count }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">No recommendations applied yet</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h3 class="card-title mb-3">
                                    <i class="ti ti-trending-up me-2"></i> Est. Revenue Impact
                                </h3>
                                <div class="mb-2">
                                    <div class="h2 text-success">${{ number_format($metrics['estimated_revenue_impact'], 2) }}</div>
                                    <small class="text-muted">Estimated additional revenue from auto-applied pricing</small>
                                </div>
                                <div class="alert alert-info mt-3 mb-0">
                                    <i class="ti ti-info-circle me-1"></i>
                                    <small>This is a rough estimate based on count × $150/day × 70% occupancy</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Latest Applied Recommendations -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="ti ti-history me-2"></i> Latest Applied Recommendations
                                </h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-vcenter table-nowrap card-table">
                                    <thead>
                                        <tr>
                                            <th>Car</th>
                                            <th>Applied At</th>
                                            <th>Price</th>
                                            <th>Confidence</th>
                                            <th>Reasons</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($latestApplications as $rec)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('car-rentals.cars.edit', $rec->car_id) }}">
                                                        {{ $rec->car->name ?? 'N/A' }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $rec->applied_at?->format('M d, Y H:i') }}</small>
                                                </td>
                                                <td>
                                                    <strong>${{ number_format($rec->recommended_value, 2) }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $rec->confidence_score >= 0.7 ? 'success' : 'warning' }}">
                                                        {{ number_format($rec->confidence_score, 2) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if ($rec->reason_codes)
                                                        @foreach ($rec->reason_codes as $reason)
                                                            <span class="badge bg-info me-1">{{ ucfirst(str_replace('_', ' ', $reason)) }}</span>
                                                        @endforeach
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    No auto-applied recommendations yet
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Per-Car Stats -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="ti ti-car me-2"></i> Per-Car Auto-Apply Activity
                                </h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-vcenter table-striped card-table">
                                    <thead>
                                        <tr>
                                            <th>Car</th>
                                            <th>Applied Count</th>
                                            <th>Auto-Apply</th>
                                            <th>Status</th>
                                            <th>Confidence Threshold</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($perCarMetrics as $car)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('car-rentals.cars.edit', $car['car_id']) }}">
                                                        {{ $car['car_name'] }}
                                                    </a>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $car['applied_count'] }}</span>
                                                </td>
                                                <td>
                                                    @if ($car['auto_apply_enabled'])
                                                        <span class="badge bg-success">Enabled</span>
                                                    @else
                                                        <span class="badge bg-secondary">Disabled</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if ($car['is_paused'])
                                                        <span class="badge bg-warning">Paused</span>
                                                    @else
                                                        <span class="badge bg-success">Active</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ number_format($car['confidence_threshold'], 2) }} ({{ intval($car['confidence_threshold'] * 100) }}%)
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">
                                                    No cars with auto-apply activity
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
