@extends('core/base::layouts.master')

@section('content')
<div class="container-xl">
    <div class="page-wrapper">
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col">
                    <h2 class="page-title">WhatsApp Dashboard</h2>
                </div>
                <div class="col-auto ms-auto d-print-none">
                    <a href="{{ route('car-rentals.whatsapp.send') }}" class="btn btn-primary">
                        Send Message
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="page-wrapper">
        <div class="container-xl">
            <!-- Statistics Cards -->
            <div class="row row-deck row-cards mb-4">
                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-truncate">
                                <h3 class="card-title card-title-sm text-muted">Total Messages</h3>
                                <div class="h2 mb-0">{{ number_format($stats['total_messages']) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-truncate">
                                <h3 class="card-title card-title-sm text-muted">Accepted Today</h3>
                                <div class="h2 mb-0 text-primary">{{ $stats['accepted_today'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-truncate">
                                <h3 class="card-title card-title-sm text-muted">Delivered Today</h3>
                                <div class="h2 mb-0 text-success">{{ $stats['delivered_today'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-lg-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-truncate">
                                <h3 class="card-title card-title-sm text-muted">Failed Today</h3>
                                <div class="h2 mb-0 text-danger">{{ $stats['failed_today'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info" role="alert">
                API acceptance is not final delivery. Final outcomes come from WhatsApp status callbacks.
                Delivered this month: <strong>{{ $stats['delivered_this_month'] }}</strong>
            </div>

            <!-- Messages by Event Type -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Messages by Event Type</h3>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-vcenter card-table">
                                <thead>
                                    <tr>
                                        <th>Event Type</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Accepted</th>
                                        <th class="text-end">Delivered/Read</th>
                                        <th class="text-end">Failed</th>
                                        <th class="text-end">Delivery Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($stats['by_event_type'] as $event)
                                        @php
                                            $total = $event->count;
                                            $delivered = $event->delivered_count;
                                            $rate = $total > 0 ? round(($delivered / $total) * 100, 1) : 0;
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="badge bg-blue-lt">{{ ucfirst(str_replace('_', ' ', $event->event_type)) }}</span>
                                            </td>
                                            <td class="text-end">{{ $total }}</td>
                                            <td class="text-end">{{ $event->accepted_count }}</td>
                                            <td class="text-end">{{ $event->delivered_count }}</td>
                                            <td class="text-end">{{ $event->failed_count }}</td>
                                            <td class="text-end">
                                                <div class="progress progress-sm" style="width: 100px; display: inline-block;">
                                                    <div class="progress-bar @if($rate >= 80) bg-success @elseif($rate >= 50) bg-warning @else bg-danger @endif" role="progressbar" style="width: {{ $rate }}%" aria-valuenow="{{ $rate }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                {{ $rate }}%
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                No messages sent yet.
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
