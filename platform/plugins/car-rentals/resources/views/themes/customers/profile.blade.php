@extends(CarRentalsHelper::viewPath('customers.layouts.master'))

@section('content')
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/car-rentals/css/overview-custom.css') }}?v={{ filemtime(public_path('vendor/core/plugins/car-rentals/css/overview-custom.css')) }}">

    @php
        $customer = auth('customer')->user();
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
                </div>
            </div>
        </div>
    </div>
@endsection
