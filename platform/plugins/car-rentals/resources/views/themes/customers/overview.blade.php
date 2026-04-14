@extends(CarRentalsHelper::viewPath('customers.layouts.master'))

@section('content')
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/car-rentals/css/overview-custom.css') }}?v={{ filemtime(public_path('vendor/core/plugins/car-rentals/css/overview-custom.css')) }}">

    @php
        $customer = auth('customer')->user();
        $totalBookings = \Botble\CarRentals\Models\Booking::query()->where('customer_id', $customer->id)->count();
        $totalReviews = \Botble\CarRentals\Models\CarReview::query()->where('customer_id', $customer->id)->count();
        $recentBookings = \Botble\CarRentals\Models\Booking::query()
            ->where('customer_id', $customer->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        $kycDisplay = match ((string) $customer->kyc_status) {
            'verified' => [
                'label' => __('Verified'),
                'note' => __('Your identity has been verified and your KYC is complete.'),
                'bg' => '#e8f5e9',
                'color' => '#2e7d32',
                'show_verify' => false,
            ],
            'pending', 'manual_review' => [
                'label' => __('Under review'),
                'note' => __('Your KYC request is in review. You will be notified after approval.'),
                'bg' => '#fff3e0',
                'color' => '#e65100',
                'show_verify' => false,
            ],
            'failed' => [
                'label' => __('Verification failed'),
                'note' => __('Please verify again with clear license and selfie images.'),
                'bg' => '#fce4ec',
                'color' => '#c62828',
                'show_verify' => true,
            ],
            default => [
                'label' => __('Not started'),
                'note' => __('Verify your account to complete KYC and unlock access.'),
                'bg' => '#eceff1',
                'color' => '#37474f',
                'show_verify' => true,
            ],
        };
    @endphp

    <div class="breadcrumb">
        Home &rsaquo; Account &rsaquo; <span>Overview</span>
    </div>

    <div class="content">
        <div class="welcome-bar">
            <div>
                <h1>Welcome back, {{ $customer->name }}!</h1>
                <p>Here's a snapshot of your account and recent activity.</p>
            </div>
            <div class="date-tag">{{ now()->format('M j, Y') }}</div>
        </div>

        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/></svg>
                </div>
                <div>
                    <div class="stat-label">{{ __('Total Bookings') }}</div>
                    <div class="stat-value">{{ $totalBookings }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                </div>
                <div>
                    <div class="stat-label">{{ __('Total Reviews') }}</div>
                    <div class="stat-value">{{ $totalReviews }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/></svg>
                </div>
                <div>
                    <div class="stat-label">{{ __('Member Since') }}</div>
                    <div class="stat-value" style="font-size:16px;margin-top:2px;">{{ $customer->created_at->diffForHumans(null, true) }}</div>
                </div>
            </div>
        </div>

        <div class="two-col" style="margin-top:1rem;">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ __('Personal Information') }}</div>
                    <div class="card-action"><a href="{{ route('customer.profile') }}">{{ __('Edit') }}</a></div>
                </div>
                <div class="info-grid">
                    <div class="info-field">
                        <label>{{ __('Name') }}</label>
                        <span>{{ $customer->name }}{!! $customer->badge !!}</span>
                    </div>
                    <div class="info-field">
                        <label>{{ __('Email') }}</label>
                        <span>{{ $customer->email }}</span>
                    </div>
                    @if ($customer->phone)
                    <div class="info-field">
                        <label>{{ __('Phone') }}</label>
                        <span>{{ $customer->phone }}</span>
                    </div>
                    @endif
                    <div class="info-field">
                        <label>{{ __('Member Since') }}</label>
                        <span>{{ $customer->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ __('Account Status') }}</div>
                </div>
                <div style="display:flex;flex-direction:column;gap:10px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px;background:#f5f3ef;border-radius:10px;">
                        <div style="font-size:13px;color:#555;">{{ __('Account type') }}</div>
                        <div style="font-size:12px;font-weight:500;background:#e8f5e9;color:#2e7d32;padding:3px 10px;border-radius:20px;">Standard</div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px;background:#f5f3ef;border-radius:10px;">
                        <div style="font-size:13px;color:#555;">{{ __('Vendor status') }}</div>
                        <div style="font-size:12px;font-weight:500;background:#fff3e0;color:#e65100;padding:3px 10px;border-radius:20px;">{{ $customer->is_vendor ? __('Active') : __('Not active') }}</div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px;background:#f5f3ef;border-radius:10px;">
                        <div style="font-size:13px;color:#555;">{{ __('KYC status') }}</div>
                        <div style="font-size:12px;font-weight:500;background:{{ $kycDisplay['bg'] }};color:{{ $kycDisplay['color'] }};padding:3px 10px;border-radius:20px;">
                            {{ $kycDisplay['label'] }}
                        </div>
                    </div>
                    <div style="font-size:12px;color:#666;padding:0 4px;">{{ $kycDisplay['note'] }}</div>
                    <div style="padding:0 4px;">
                        <a href="{{ route('customer.kyc') }}" class="btn btn-primary btn-sm">
                            {{ $kycDisplay['show_verify'] ? __('Verify') : __('View KYC') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card" style="margin-top:1rem;">
            <div class="card-header">
                <div class="card-title">{{ __('Recent Bookings') }}</div>
                <div class="card-action"><a href="{{ route('customer.bookings') }}">{{ __('View all') }}</a></div>
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
                <div class="empty-state">
                    <div class="empty-icon">
                        <x-core::icon name="ti ti-calendar-off" size="lg" class="text-muted mb-3" style="font-size: 24px;" />
                    </div>
                    <h3>{{ __('No bookings yet') }}</h3>
                    <p>{{ __("You haven't made any bookings yet. Start exploring vehicles.") }}</p>
                    <a href="{{ route('public.cars') }}" class="btn-primary" style="margin-top:4px;padding:9px 22px;">{{ __('Explore Cars') }}</a>
                </div>
            @endif
        </div>

        <div class="subscribe-bar">
            <div>
                <h3>{{ __('Subscribe for secret deals') }}</h3>
                <p>{{ __('Prices drop the moment you sign up.') }}</p>
            </div>
            <div class="sub-form">
                <form action="#" method="post" onsubmit="return false;">
                    <input class="sub-input" type="email" placeholder="{{ __('Enter your email') }}" />
                    <button class="btn-primary">{{ __('Subscribe') }}</button>
                </form>
            </div>
        </div>
    </div>

@endsection
