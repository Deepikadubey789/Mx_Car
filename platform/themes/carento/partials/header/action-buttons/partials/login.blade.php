@if (is_plugin_active('car-rentals'))
    @php
        $auth = auth('customer');
    @endphp

    @if (! $auth->check())
        <a href="{{ route('customer.login') }}" class="btn btn-signin neutral-1000 header-login-btn me-3">
            <x-core::icon name="ti ti-user" style="width: 20px; height: 20px;" />
            {{ __('Sign in') }}
        </a>
    @else
        @php $customer = $auth->user(); @endphp

        <div class="dropdown dropdown-login me-3">
            <a href="#" class="btn-login neutral-1000 dropdown-toggle" id="shareDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                <div class="wrapper-avatar-ratio">
                    <div class="wrapper-avatar">
                        {{ RvMedia::image($customer->avatar_url, $customer->name, 'thumb', attributes: ['class' => 'avatar']) }}
                    </div>
                </div>
                <span class="user-name d-none d-sm-inline text-truncate">{{ $customer->name }}</span>
            </a>

            <ul class="dropdown-menu" aria-labelledby="shareDropdown">
                @if (CarRentalsHelper::isMultiVendorEnabled() && $customer->is_vendor)
                    <li>
                        <a
                            href="{{ route('car-rentals.vendor.dashboard') }}"
                            class="dropdown-item"
                        >
                            <span>{{ __('Vendor Dashboard') }}</span>
                        </a>
                    </li>
                @endif
                <li>
                    <a
                        href="{{ route('customer.bookings') }}"
                        class="dropdown-item"
                    >
                        <span>{{ __('My Bookings') }}</span>
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('customer.profile') }}"
                        class="dropdown-item"
                    >
                        <span>{{ __('Profile') }}</span>
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('customer.change-password') }}"
                        class="dropdown-item"
                    >
                        <span>{{ __('Change Password') }}</span>
                    </a>
                </li>
                <li>
                    <a
                        href="{{ route('customer.logout') }}"
                        class="dropdown-item"
                    >
                        <span>{{ __('Logout') }}</span>
                    </a>
                </li>
            </ul>
        </div>
    @endif

    @if (CarRentalsHelper::isMultiVendorEnabled())
        <a class="btn btn-signin bg-white text-dark" href="{{ auth('customer')->check() ? route('car-rentals.vendor.cars.create') : route('customer.login') }}">{{ __('Add Listing') }}</a>
    @endif
@endif
