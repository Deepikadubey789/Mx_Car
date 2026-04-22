@php
    $customer = auth('customer')->user();
@endphp

<style>
    .ps-sidebar .ps-sidebar__top {
        padding-right: 24px;
        margin-bottom: 36px;
    }

    .ps-sidebar .ps-sidebar__top .vendor-profile-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }

    .ps-sidebar .ps-sidebar__top .vendor-profile-head .ps-block__right {
        flex: 1;
        min-width: 0;
    }

    .ps-sidebar .ps-sidebar__top .vendor-profile-head .ps-block__right p {
        font-size: 32px;
        line-height: 1.2;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ps-sidebar .ps-sidebar__top .vendor-profile-meta-card {
        border: 1px solid #d7dce3;
        border-radius: 12px;
        background: #f8fafc;
        padding: 10px 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }

    .ps-sidebar .ps-sidebar__top .vendor-profile-meta-card p {
        margin: 0;
        font-size: 16px;
        line-height: 1.25;
        color: #0f172a;
        font-weight: 600;
    }

    .ps-sidebar .ps-sidebar__top .vendor-profile-meta-card small {
        display: block;
        margin-top: 2px;
        font-size: 13px;
        color: #64748b;
    }

    .ps-sidebar .ps-sidebar__top .vendor-profile-meta-card .icon {
        width: 14px;
        height: 14px;
        color: #94a3b8;
    }

    .ps-sidebar .ps-sidebar__top .vendor-profile-dropdown-menu {
        min-width: 100%;
        border-radius: 12px;
        border: 1px solid #d7dce3;
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.08);
        padding: 6px 0;
        margin-top: 6px !important;
    }

    .ps-sidebar .ps-sidebar__top .vendor-profile-dropdown-menu .dropdown-item {
        padding: 10px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .ps-sidebar .ps-sidebar__top .vendor-profile-dropdown-menu .dropdown-item.text-danger {
        color: #dc2626 !important;
    }
</style>

<header class="header--mobile">
    <div class="header__left">
        <button class="ps-drawer-toggle">
            <x-core::icon name="ti ti-menu-2" />
        </button>
    </div>
    <div class="header__center">
        <a
            class="ps-logo"
            href="{{ route('car-rentals.vendor.dashboard') }}"
        >
            @php $logo = theme_option('logo_vendor_dashboard', theme_option('logo')); @endphp
            @if ($logo)
                <img
                    src="{{ RvMedia::getImageUrl($logo) }}"
                    alt="{{ theme_option('site_title') }}"
                >
            @endif
        </a>
    </div>
    <div class="header__right">
        <a class="header__site-link" href="{{ route('customer.logout') }}">
            <x-core::icon name="ti ti-logout" />
        </a>
    </div>
</header>
<aside class="ps-drawer--mobile">
    <div class="ps-drawer__header">
        <h4 class="fs-3 mb-0">Menu</h4>
        <button class="ps-drawer__close">
            <x-core::icon name="ti ti-x" />
        </button>
    </div>
    <div class="ps-drawer__content">
        @include(CarRentalsHelper::viewPath('vendor-dashboard.layouts.menu'))
    </div>
</aside>
<div class="ps-site-overlay"></div>
<main class="ps-main">
    <div class="ps-main__sidebar">
        <div class="ps-sidebar">
            <div class="ps-sidebar__top">
                <div class="ps-block--user-wellcome vendor-profile-head">
                    <div class="ps-block__left">
                        <img
                            src="{{ $customer->avatar_url }}"
                            alt="{{ $customer->name }}"
                            class="avatar avatar-lg"
                        />
                    </div>
                    <div class="ps-block__right">
                        <p>{{ $customer->name }}</p>
                    </div>
                </div>
                <div class="dropdown">
                    <button type="button" class="vendor-profile-meta-card dropdown-toggle" id="vendorProfileMenu" data-bs-toggle="dropdown" aria-expanded="false">
                        <div>
                            <p>{{ __('Hello') }}, {{ $customer->name }} {!! $customer->badge !!}</p>
                            <small>{{ __('Joined on :date', ['date' => $customer->created_at->translatedFormat('M d, Y')]) }}</small>
                        </div>
                    </button>
                    <div class="dropdown-menu vendor-profile-dropdown-menu" aria-labelledby="vendorProfileMenu">
                        <a href="{{ route('car-rentals.vendor.settings.index') }}" class="dropdown-item">
                            <x-core::icon name="ti ti-settings" />
                            <span>{{ __('Settings') }}</span>
                        </a>
                        <a href="{{ route('customer.logout') }}" class="dropdown-item text-danger">
                            <x-core::icon name="ti ti-logout" />
                            <span>{{ __('Logout') }}</span>
                        </a>
                    </div>
                </div>
                <div class="ps-block--earning-count">
                    <small>{{ __('Balance') }}</small>
                    <h3 class="mt-1">{{ format_price($customer->balance) }}</h3>
                </div>
            </div>
            <div class="ps-sidebar__content">
                <div class="ps-sidebar__center">
                    @include(CarRentalsHelper::viewPath('vendor-dashboard.layouts.menu'))
                </div>
                <div class="ps-sidebar__footer">
                    <div class="ps-copyright">
                        @if ($logo)
                            <a href="{{ BaseHelper::getHomepageUrl() }}" title="{{ $siteTitle = theme_option('site_title') }}">
                                <img
                                    src="{{ RvMedia::getImageUrl($logo) }}"
                                    alt="{{ $siteTitle }}"
                                    style="max-height: 40px;"
                                >
                            </a>
                        @endif
                        <p>{!! BaseHelper::clean(str_replace('%Y', Carbon\Carbon::now()->year, theme_option('copyright'))) !!}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div
        class="ps-main__wrapper"
        id="vendor-dashboard"
    >
        <header class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="fs-1 mb-0 text-truncate me-3">{{ page_title()->getTitle(false) }}</h3>
            <div class="d-flex align-items-center gap-4">
                <div class="d-none d-md-inline-block">
                    <a href="{{ BaseHelper::getHomepageUrl() }}" target="_blank" class="text-uppercase d-block">
                        <span>{{ __('Go to homepage') }}</span>
                        <x-core::icon name="ti ti-arrow-right" />
                    </a>
                </div>
            </div>
        </header>

        <div id="app" class="page-body">
            @yield('content')
        </div>
    </div>
</main>
