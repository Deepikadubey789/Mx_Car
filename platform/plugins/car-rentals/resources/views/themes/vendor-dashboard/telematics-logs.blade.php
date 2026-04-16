@extends(CarRentalsHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
<div class="card shadow-sm border-0" style="border-radius: 1rem;">
    <div class="card-header bg-transparent border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title mb-0 fw-bold">
            <i class="ti ti-history text-primary me-2"></i>{{ __('Vehicle Activity Logs') }}
        </h5>
    </div>

    {{-- Filters --}}
    <div class="card-body border-bottom bg-light bg-opacity-50">
        <form method="GET" action="{{ route('car-rentals.vendor.telematics-logs') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">{{ __('Filter by Car') }}</label>
                <select name="car_id" class="form-select shadow-none border-0">
                    <option value="">{{ __('All Monitored Cars') }}</option>
                    @foreach($cars as $id => $name)
                        <option value="{{ $id }}" @selected(request('car_id') == $id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted">{{ __('Event Type') }}</label>
                <select name="event_type" class="form-select shadow-none border-0">
                    <option value="">{{ __('All Events') }}</option>
                    <option value="driving" @selected(request('event_type') == 'driving')>{{ __('Standard Driving') }}</option>
                    <option value="speeding" @selected(request('event_type') == 'speeding')>{{ __('Speeding Alerts') }}</option>
                    <option value="geofence_exit" @selected(request('event_type') == 'geofence_exit')>{{ __('Geofence Breaches') }}</option>
                    <option value="hard_braking" @selected(request('event_type') == 'hard_braking')>{{ __('Hard Braking') }}</option>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100 shadow-sm" style="border-radius: 8px;">
                    <i class="ti ti-filter me-1"></i> {{ __('Apply Filters') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="table-responsive">
        <table class="table table-hover table-vcenter text-nowrap mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">{{ __('Timestamp') }}</th>
                    <th class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">{{ __('Vehicle') }}</th>
                    <th class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">{{ __('Event') }}</th>
                    <th class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">{{ __('Speed') }}</th>
                    <th class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">{{ __('Odometer') }}</th>
                    <th class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">{{ __('Location') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="text-secondary">
                            <div class="fw-semibold text-dark">{{ $log->created_at->format('M d, Y') }}</div>
                            <div class="small">{{ $log->created_at->format('h:i:s A') }}</div>
                        </td>
                        <td class="fw-medium text-dark">
                            {{ $log->car->name ?? 'Unknown Car' }}<br>
                            <small class="text-muted">{{ $log->car->license_plate ?? '' }}</small>
                        </td>
                        <td>
                            @if(in_array($log->event_type, ['speeding', 'hard_braking']))
                                <span class="badge bg-warning text-dark border"><i class="ti ti-alert-triangle me-1"></i>{{ ucfirst(str_replace('_', ' ', $log->event_type)) }}</span>
                            @elseif($log->event_type == 'geofence_exit')
                                <span class="badge bg-danger text-white"><i class="ti ti-shield-x me-1"></i>{{ __('Boundary Exit') }}</span>
                            @else
                                <span class="badge bg-light text-secondary border"><i class="ti ti-point-filled text-success"></i>{{ ucfirst(str_replace('_', ' ', $log->event_type)) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="{{ $log->speed_mph > 80 ? 'text-danger fw-bold' : 'text-dark' }}">
                                {{ number_format($log->speed_mph, 1) }} mph
                            </span>
                        </td>
                        <td class="text-muted">{{ number_format($log->odometer_miles) }} mi</td>
                        <td>
                            @if($log->latitude && $log->longitude)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $log->latitude }},{{ $log->longitude }}" target="_blank" class="btn btn-sm btn-light border" style="border-radius: 6px;">
                                    <i class="ti ti-map-pin text-danger"></i> {{ __('View Map') }}
                                </a>
                            @else
                                <span class="text-muted small">{{ __('No GPS Data') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="ti ti-history opacity-50 mb-2" style="font-size: 2rem;"></i><br>
                            {{ __('No telematics logs found matching your criteria.') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($logs->hasPages())
        <div class="card-footer bg-transparent border-top">
            {{ $logs->links() }}
        </div>
    @endif
</div>
@stop