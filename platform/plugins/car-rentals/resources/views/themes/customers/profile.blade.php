@extends(CarRentalsHelper::viewPath('customers.layouts.master'))

@section('content')
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/car-rentals/css/overview-custom.css') }}?v={{ filemtime(public_path('vendor/core/plugins/car-rentals/css/overview-custom.css')) }}">

    @php
        $customer = auth('customer')->user();
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
        Home &rsaquo; Account &rsaquo; <span>Profile</span>
    </div>

    <div class="content">
        <div class="two-col">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ __('Profile Settings') }}</div>
                    <div class="card-action"><a href="{{ route('customer.overview') }}">{{ __('Back to overview') }}</a></div>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        {!! $form->renderForm() !!}
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{ __('Account') }}</div>
                </div>
                <div style="display:flex;flex-direction:column;gap:10px;padding:10px;">
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <div style="font-size:13px;color:#555;">{{ __('Member Since') }}</div>
                        <div style="font-size:12px;font-weight:500;">{{ $customer->created_at->format('M d, Y') }}</div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <div style="font-size:13px;color:#555;">{{ __('Phone') }}</div>
                        <div style="font-size:12px;font-weight:500;">{{ $customer->phone ?? '-' }}</div>
                    </div>
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <div style="font-size:13px;color:#555;">{{ __('KYC status') }}</div>
                        <div style="font-size:12px;font-weight:500;background:{{ $kycDisplay['bg'] }};color:{{ $kycDisplay['color'] }};padding:3px 10px;border-radius:20px;">
                            {{ $kycDisplay['label'] }}
                        </div>
                    </div>
                    <div style="font-size:12px;color:#666;">{{ $kycDisplay['note'] }}</div>
                    <div style="display:flex;align-items:center;justify-content:space-between;">
                        <div style="font-size:13px;color:#555;">{{ __('KYC level') }}</div>
                        <div style="font-size:12px;font-weight:500;">{{ $customer->kyc_level ?: '-' }}</div>
                    </div>
                    <a href="{{ route('customer.kyc') }}" class="btn btn-primary btn-sm" style="margin-top:4px;">
                        {{ $kycDisplay['show_verify'] ? __('Verify') : __('View KYC') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
