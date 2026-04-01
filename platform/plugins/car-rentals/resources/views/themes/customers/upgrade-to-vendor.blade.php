@extends(CarRentalsHelper::viewPath('customers.layouts.master'))

@section('content')
    <div class="breadcrumb">
        Home &rsaquo; Account &rsaquo; <span>{{ __('Upgrade to Vendor') }}</span>
    </div>

    <div class="upgrade-hero card mb-4">
        <div class="card-body">
            <h5 class="card-title">{{ __('Become a Car Rental Vendor') }}</h5>
            <p class="card-text text-muted">{{ __('Start earning money by renting out your vehicles on our platform. Join thousands of successful vendors who are already making profits with their car fleet.') }}</p>
        </div>
    </div>

    <div class="upgrade-benefits-card card mb-4">
        <div class="card-body">
            <h5 class="card-title mb-4">{{ __('Vendor Benefits') }}</h5>
            <div class="row g-3 upgrade-benefits-grid">
                @foreach([
                    ['title' => __('List Your Vehicles'), 'description' => __('Add unlimited cars with photos, specifications, and pricing')],
                    ['title' => __('Manage Bookings'), 'description' => __('Accept or decline bookings, manage schedules easily')],
                    ['title' => __('Track Earnings'), 'description' => __('Monitor your income with detailed reports and analytics')],
                    ['title' => __('Customer Reviews'), 'description' => __('Build trust with ratings and feedback from renters')],
                    ['title' => __('Set Your Prices'), 'description' => __('Full control over pricing and special offers')],
                    ['title' => __('Vendor Dashboard'), 'description' => __('Access dedicated tools and management features')],
                ] as $benefit)
                    <div class="col-md-6">
                        <div class="upgrade-benefit">
                            <div class="d-flex">
                                <div class="me-3 text-primary">
                                    <x-core::icon name="ti ti-circle-check" />
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $benefit['title'] }}</h6>
                                    <p class="text-muted small mb-0">{{ $benefit['description'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="upgrade-confirmation-card card">
        <div class="card-body">
            <h5 class="card-title mb-4">{{ __('Ready to Become a Vendor?') }}</h5>

            <form action="{{ route('customer.upgrade-to-vendor.post') }}" method="POST" id="upgrade-form" class="upgrade-form">
                @csrf

                <div class="upgrade-alert alert alert-warning mb-3" role="alert">
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

                <div class="upgrade-actions d-flex gap-2">
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