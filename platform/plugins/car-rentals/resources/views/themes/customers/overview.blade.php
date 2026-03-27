@extends(CarRentalsHelper::viewPath('customers.layouts.master'))

@section('content')
    <style>
        .card-title {
            font-size: 22px !important;
        }
        .btn-outline-primary {
            border-radius: 12px;
            border-color: var(--bs-brand-2);
            color: var(--bs-brand-2) !important;
        }
        .btn-outline-primary:hover {
            background-color: var(--bs-brand-2);
            border-color: var(--bs-brand-2);
            color: #101010 !important;
        }
    </style>

    @php
        $customer = auth('customer')->user();
        $totalBookings = \Botble\CarRentals\Models\Booking::query()->where('customer_id', $customer->id)->count();
        $totalReviews = \Botble\CarRentals\Models\CarReview::query()->where('customer_id', $customer->id)->count();
        $recentBookings = \Botble\CarRentals\Models\Booking::query()
            ->where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
    @endphp

    <!-- Welcome Section -->
    <div class="card mb-4">
        <div class="card-body">
            <h4 class="mb-1">
                {{ __('Welcome back') }}, {{ $customer->name }}{!! $customer->badge !!}!
            </h4>
            <p class="text-muted mb-0">{{ __('Here\'s an overview of your account and recent activity') }}</p>
        </div>
    </div>

    <!-- Stats Section -->
    <div class="mb-4">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1 fw-bold" style="font-size: 1.4rem;">{{ $totalBookings }}</h4>
                                <p class="mb-0 text-muted">{{ __('Total Bookings') }}</p>
                            </div>
                            <div>
                                <x-core::icon name="ti ti-calendar" class="text-muted" style="font-size: 28px;" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1 fw-bold" style="font-size: 1.4rem;">{{ $totalReviews }}</h4>
                                <p class="mb-0 text-muted">{{ __('Total Reviews') }}</p>
                            </div>
                            <div>
                                <x-core::icon name="ti ti-star" class="text-muted" style="font-size: 28px;" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h4 class="mb-1 fw-bold" style="font-size: 1.4rem;">{{ $customer->created_at->diffForHumans(null, true) }}</h4>
                                <p class="mb-0 text-muted">{{ __('Member Since') }}</p>
                            </div>
                            <div>
                                <x-core::icon name="ti ti-user" class="text-muted" style="font-size: 28px;" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upgrade to Vendor Section -->
    @if(!$customer->is_vendor)
    <div class="mb-4">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h5 class="card-title mb-2">
                            {{ __('Become a Car Rental Vendor') }}
                        </h5>
                        <p class="card-text text-muted mb-3 mb-lg-0">
                            {{ __('Upgrade your account to vendor status and start renting out your vehicles. Earn money by listing your cars on our platform.') }}
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="{{ route('customer.upgrade-to-vendor') }}" class="btn btn-primary ms-0">
                            {{ __('Learn More & Upgrade') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Personal Information Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">{{ __('Personal Information') }}</h5>
                <a href="{{ route('customer.profile') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('Edit') }}
                </a>
            </div>
            <div class="row g-3">
                <div class="col-md-3">
                    <div>
                        <p class="text-muted small mb-1">{{ __('Name') }}</p>
                        <p class="mb-0">{{ $customer->name }}{!! $customer->badge !!}</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div>
                        <p class="text-muted small mb-1">{{ __('Email') }}</p>
                        <p class="mb-0">{{ $customer->email }}</p>
                    </div>
                </div>
                @if ($customer->phone)
                    <div class="col-md-3">
                        <div>
                            <p class="text-muted small mb-1">{{ __('Phone') }}</p>
                            <p class="mb-0">{{ $customer->phone }}</p>
                        </div>
                    </div>
                @endif
                <div class="col-md-3">
                    <div>
                        <p class="text-muted small mb-1">{{ __('Member Since') }}</p>
                        <p class="mb-0">{{ $customer->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Bookings Section -->
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">{{ __('Recent Bookings') }}</h5>
                <a href="{{ route('customer.bookings') }}" class="btn btn-sm btn-outline-primary">
                    {{ __('View All') }}
                </a>
            </div>
            @if ($recentBookings->isNotEmpty())
                <div>
                    @foreach ($recentBookings as $booking)
                        <div class="pb-3 mb-3 border-bottom">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <span class="badge bg-light text-muted p-2">
                                                <x-core::icon name="ti ti-car" />
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="mb-1" style="font-size: 0.95rem;">{{ $booking->car->name }}</h6>
                                            <p class="text-muted small mb-0">
                                                {{ __('Booking ID') }}: {{ $booking->booking_number }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div>
                                        <p class="text-muted small mb-1">{{ __('Rental Period') }}</p>
                                        <p class="mb-0 small">
                                            {{ Carbon\Carbon::parse($booking->start_date)->format('M d') }} -
                                            {{ Carbon\Carbon::parse($booking->end_date)->format('M d, Y') }}
                                        </p>
                                        <p class="text-muted small mb-0">
                                            ({{ Carbon\Carbon::parse($booking->start_date)->diffInDays($booking->end_date) }} {{ __('days') }})
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div>
                                        <p class="text-muted small mb-1">{{ __('Total') }}</p>
                                        <p class="mb-0 fw-semibold">{{ format_price($booking->amount) }}</p>
                                        <div class="mt-1">
                                            {!! $booking->status->toHtml() !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-1 text-end">
                                    <a href="{{ route('customer.bookings.show', $booking->transaction_id) }}"
                                       class="btn btn-sm btn-link text-muted p-2"
                                       title="{{ __('View Details') }}">
                                        <x-core::icon name="ti ti-arrow-right" />
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4">
                    <x-core::icon name="ti ti-calendar-off" size="lg" class="text-muted mb-3" style="font-size: 48px;" />
                    <h6>{{ __('No Bookings Yet') }}</h6>
                    <p class="text-muted small mb-3">{{ __("You haven't made any bookings yet.") }}</p>
                    <a href="{{ route('public.cars') }}" class="btn btn-sm btn-primary ms-0">
                        {{ __('Explore Cars') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection
