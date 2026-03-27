@extends(CarRentalsHelper::viewPath('customers.layouts.master'))

@section('content')
    <style>
        .card-title {
            font-size: 22px !important;
        }
    </style>

    <!-- Introduction -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ __('Become a Car Rental Vendor') }}</h5>
            <p class="card-text">{{ __('Start earning money by renting out your vehicles on our platform. Join thousands of successful vendors who are already making profits with their car fleet.') }}</p>
        </div>
    </div>

    <!-- Benefits -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-4">{{ __('Vendor Benefits') }}</h5>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded">
                        <div class="d-flex">
                            <div class="me-3 text-primary">
                                <x-core::icon name="ti ti-circle-check" />
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1" style="font-size: 0.9rem;">{{ __('List Your Vehicles') }}</h6>
                                <p class="text-muted small mb-0">{{ __('Add unlimited cars with photos, specifications, and pricing') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded">
                        <div class="d-flex">
                            <div class="me-3 text-primary">
                                <x-core::icon name="ti ti-circle-check" />
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1" style="font-size: 0.9rem;">{{ __('Manage Bookings') }}</h6>
                                <p class="text-muted small mb-0">{{ __('Accept or decline bookings, manage schedules easily') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded">
                        <div class="d-flex">
                            <div class="me-3 text-primary">
                                <x-core::icon name="ti ti-circle-check" />
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1" style="font-size: 0.9rem;">{{ __('Track Earnings') }}</h6>
                                <p class="text-muted small mb-0">{{ __('Monitor your income with detailed reports and analytics') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded">
                        <div class="d-flex">
                            <div class="me-3 text-primary">
                                <x-core::icon name="ti ti-circle-check" />
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1" style="font-size: 0.9rem;">{{ __('Customer Reviews') }}</h6>
                                <p class="text-muted small mb-0">{{ __('Build trust with ratings and feedback from renters') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded">
                        <div class="d-flex">
                            <div class="me-3 text-primary">
                                <x-core::icon name="ti ti-circle-check" />
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1" style="font-size: 0.9rem;">{{ __('Set Your Prices') }}</h6>
                                <p class="text-muted small mb-0">{{ __('Full control over pricing and special offers') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded">
                        <div class="d-flex">
                            <div class="me-3 text-primary">
                                <x-core::icon name="ti ti-circle-check" />
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1" style="font-size: 0.9rem;">{{ __('Vendor Dashboard') }}</h6>
                                <p class="text-muted small mb-0">{{ __('Access dedicated tools and management features') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upgrade Confirmation -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">{{ __('Ready to Become a Vendor?') }}</h5>

            <form action="{{ route('customer.upgrade-to-vendor.post') }}" method="POST" id="upgrade-form">
                @csrf

                <div class="alert alert-warning mb-3" role="alert">
                    <strong>{{ __('Please Note:') }}</strong> {{ __('By upgrading to a vendor account, you will gain access to the vendor dashboard where you can manage your car listings, bookings, and earnings. This action cannot be reversed.') }}
                </div>

                <div class="form-group mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="agree-terms" name="agree_terms" value="1" required>
                        <label class="form-check-label text-sm-medium neutral-1000" for="agree-terms">
                            {{ __('I understand and agree to become a vendor') }}
                        </label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary ms-0" id="upgrade-button">
                        {{ __('Upgrade to Vendor') }}
                    </button>
                    <a href="{{ route('customer.overview') }}" class="btn btn-secondary">
                        {{ __('Cancel') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection