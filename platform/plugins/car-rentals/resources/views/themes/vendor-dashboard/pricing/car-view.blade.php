@extends('plugins/car-rentals::themes.vendor-dashboard.layouts.master')

@section('content')
    <div class="page-wrapper">
        <div class="container-xl">
            <div class="page-header">
                <h2 class="page-title">{{ $car->name ?? 'Car' }} - Recommendations</h2>
            </div>
        </div>

        <div class="page-body">
            <div class="container-xl">
                <div class="card">
                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Recommended Price</th>
                                    <th>Confidence</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($recommendations as $carId => $recs)
                                    @foreach ($recs as $rec)
                                        <tr>
                                            <td>{{ $rec->recommendation_date->format('M d, Y') }}</td>
                                            <td>${{ number_format($rec->recommended_value, 2) }}</td>
                                            <td>
                                                <span class="badge bg-success">{{ intval($rec->confidence_score * 100) }}%</span>
                                            </td>
                                            <td>
                                                @include('plugins/car-rentals::themes.vendor-dashboard.pricing.recommendation-actions', ['recommendation' => $rec])
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
