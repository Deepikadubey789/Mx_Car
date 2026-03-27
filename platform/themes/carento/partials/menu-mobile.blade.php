<div class="mobile-header-active mobile-header-wrapper-style perfect-scrollbar button-bg-2">
    <div class="mobile-header-wrapper-inner">
        <div class="mobile-header-logo">
            {!! Theme::partial('logo') !!}
            <div class="burger-icon burger-icon-white"></div>
        </div>

        <div class="mobile-header-content-area">
                @if (is_plugin_active('car-rentals'))
                    @php
                        $auth = auth('customer');
                    @endphp

                    @if (CarRentalsHelper::isMultiVendorEnabled())
                        <div class="mobile-add-listing-wrapper">
                            <a href="{{ $auth->check() ? route('car-rentals.vendor.cars.create') : route('customer.login') }}" class="mobile-add-listing-btn">
                                <div class="add-listing-icon-wrapper">
                                    <x-core::icon name="ti ti-plus" />
                                </div>
                                <div class="add-listing-text-wrapper">
                                    <span class="add-listing-title">{{ __('Add Listing') }}</span>
                                    <span class="add-listing-subtitle">{{ __('List your car for rent') }}</span>
                                </div>
                                <x-core::icon name="ti ti-arrow-right" class="add-listing-arrow" />
                            </a>
                        </div>
                    @endif

                    @if (! $auth->check())
                        <div class="mobile-signin-wrapper">
                            <a href="{{ route('customer.login') }}" class="mobile-signin-btn">
                                <div class="signin-icon-wrapper">
                                    <x-core::icon name="ti ti-user" />
                                </div>
                                <div class="signin-text-wrapper">
                                    <span class="signin-title">{{ __('Sign in') }}</span>
                                    <span class="signin-subtitle">{{ __('Access your account') }}</span>
                                </div>
                                <x-core::icon name="ti ti-chevron-right" class="signin-arrow" />
                            </a>
                        </div>
                    @else
                        @php $customer = $auth->user(); @endphp
                        <div class="mobile-menu-wrap mobile-customer-menu-wrap">
                            <nav>
                                <ul class="mobile-menu font-heading">
                                    <li class="mobile-customer-info">
                                        <div class="d-flex align-items-center p-3" style="background-color: rgba(0, 0, 0, 0.05); border-radius: 8px;">
                                            <div class="wrapper-avatar-ratio me-2">
                                                <div class="wrapper-avatar">
                                                    {{ RvMedia::image($customer->avatar_url, $customer->name, 'thumb', attributes: ['class' => 'avatar', 'style' => 'width: 40px; height: 40px; border-radius: 50%;']) }}
                                                </div>
                                            </div>
                                            <span class="user-name text-truncate">{{ $customer->name }}</span>
                                        </div>
                                    </li>
                                    @if (CarRentalsHelper::isMultiVendorEnabled() && $customer->is_vendor)
                                        <li>
                                            <a href="{{ route('car-rentals.vendor.dashboard') }}">
                                                {{ __('Vendor Dashboard') }}
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a href="{{ route('customer.bookings') }}">
                                            {{ __('My Bookings') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('customer.profile') }}">
                                            {{ __('Profile') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('customer.change-password') }}">
                                            {{ __('Change Password') }}
                                        </a>
                                    </li>
                                    <li class="mobile-logout-item">
                                        <a href="{{ route('customer.logout') }}" class="mobile-logout-btn">
                                            <x-core::icon name="ti ti-logout" />
                                            <span>{{ __('Logout') }}</span>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    @endif
                @endif

                <div class="mobile-menu-wrap mobile-header-border">
                    <nav>
                        {!!
                            Menu::renderMenuLocation('main-menu', [
                                'options' => ['class' => 'mobile-menu font-heading'],
                                'view'    => 'main-menu',
                            ])
                        !!}
                    </nav>
                </div>

                <div class="mobile-menu-wrap mobile-header-border">
                    <nav>
                        {!! Theme::partial('currency-switcher-mobile') !!}
                        {!! Theme::partial('language-switcher-mobile') !!}
                    </nav>
                </div>
            </div>
    </div>
</div>
