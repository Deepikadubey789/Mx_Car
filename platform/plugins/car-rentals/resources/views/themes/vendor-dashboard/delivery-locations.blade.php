@extends(CarRentalsHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')
<div class="row">
    {{-- Left Side: The List of Locations --}}
    <div class="col-md-8">
        <div class="card shadow-sm border-0" style="border-radius: 1rem;">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="card-title mb-0 fw-bold">
                    <i class="ti ti-map-pin text-primary me-2"></i>{{ __('My Delivery Zones') }}
                </h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-vcenter mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">{{ __('Location Name') }}</th>
                            <th class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">{{ __('Type') }}</th>
                            <th class="text-uppercase text-muted fw-bold" style="font-size: 0.75rem;">{{ __('Delivery Fee') }}</th>
                            <th class="text-uppercase text-muted fw-bold text-end" style="font-size: 0.75rem;">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($locations as $location)
                            <tr>
                                <td class="fw-medium text-dark">{{ $location->name }}</td>
                                <td>
                                    <span class="badge bg-secondary-lt text-capitalize">
                                        {{ str_replace('_', ' ', $location->type) }}
                                    </span>
                                </td>
                                <td class="text-success fw-bold">
                                    {{ get_application_currency()->symbol }}{{ number_format($location->fee_amount, 2) }}
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('car-rentals.vendor.delivery-locations.destroy', $location->id) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this delivery zone?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light text-danger border" style="border-radius: 6px;">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="ti ti-map-2 opacity-50 mb-2" style="font-size: 2rem;"></i><br>
                                    {{ __('You have not created any delivery zones yet.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right Side: The Add Form --}}
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-primary-lt" style="border-radius: 1rem;">
            <div class="card-body">
                <h5 class="fw-bold mb-3">{{ __('Add New Location') }}</h5>
                <form action="{{ route('car-rentals.vendor.delivery-locations.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Location Name') }}</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. LAX Airport" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ __('Location Type') }}</label>
                        <select name="type" class="form-select" required>
                            <option value="airport">{{ __('Airport') }}</option>
                            <option value="hotel">{{ __('Hotel') }}</option>
                            <option value="custom_zone">{{ __('Custom Zone / City') }}</option>
                            <option value="custom_address">{{ __('Specific Address') }}</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">{{ __('Delivery Fee') }}</label>
                        <div class="input-group">
                            <span class="input-group-text">{{ get_application_currency()->symbol }}</span>
                            <input type="number" step="0.01" min="0" name="fee_amount" class="form-control" placeholder="50.00" required>
                        </div>
                        <small class="text-muted">{{ __('Amount charged to the guest at checkout.') }}</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" style="border-radius: 8px;">
                        <i class="ti ti-plus me-1"></i> {{ __('Save Location') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop