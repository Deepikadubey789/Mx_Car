@extends('plugins/car-rentals::themes.vendor-dashboard.layouts.master')

@section('content')
    <div class="page-wrapper">
        <div class="container-xl">
            <div class="page-header">
                <h2 class="page-title">Performance Report</h2>
            </div>
        </div>

        <div class="page-body">
            <div class="container-xl">
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-muted">Total Applied</div>
                                <div class="h2">{{ $stats['total_applied'] }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-muted">Revenue Impact</div>
                                <div class="h2 text-success">${{ number_format($stats['total_revenue_impact'], 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-muted">Avg Confidence</div>
                                <div class="h2">{{ intval($stats['avg_confidence'] * 100) }}%</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card">
                            <div class="card-body">
                                <div class="text-muted">Cars</div>
                                <div class="h2">{{ $stats['distribution_by_car']->count() }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History Table -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Applied Recommendations</h3>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Car</th>
                                    <th>Date</th>
                                    <th>Price Applied</th>
                                    <th>Confidence</th>
                                    <th>Applied At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($history as $rec)
                                    <tr>
                                        <td>{{ $rec->car->name }}</td>
                                        <td>{{ $rec->recommendation_date->format('M d, Y') }}</td>
                                        <td>${{ number_format($rec->recommended_value, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $rec->confidence_score >= 0.75 ? 'success' : 'info' }}">
                                                {{ intval($rec->confidence_score * 100) }}%
                                            </span>
                                        </td>
                                        <td>{{ $rec->applied_at?->format('M d, Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No recommendations applied yet</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
