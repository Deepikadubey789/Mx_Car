@extends(CarRentalsHelper::viewPath('vendor-dashboard.layouts.master'))

@section('content')

{{-- DASHBOARD CUSTOM CSS OVERRIDES (MERGED WITH ADMIN SOFT UI) --}}
<style>
    /* --- COMPLETE SOFT UI TRANSFORMATION (FINAL POLISH) --- */
    :root {
        --soft-ui-radius: 1rem;
        --soft-ui-shadow: 0 20px 27px 0 rgba(0, 0, 0, 0.05);
    }

    /* 1. GLOBAL THEME VARIABLES (Bulletproof Light Mode) */
    :root,
    html[data-bs-theme="light"],
    body[data-bs-theme="light"],
    html[data-theme="light"],
    body[data-theme="light"],
    body:not([data-bs-theme="dark"]):not([data-theme="dark"]) {
        --soft-body-bg: #f8f9fa;       
        --soft-card-bg: #ffffff;       
        --soft-card-border: rgba(0, 0, 0, 0.05);
        --soft-text-main: #252f40;     
        --soft-text-muted: #67748e;    
        --soft-input-bg: #ffffff;      
        
        /* GLASSMORPHISM - Light Mode Navigation */
        --soft-nav-bg: rgba(255, 255, 255, 0.65); 
        --soft-nav-text: #67748e; 
        --soft-nav-text-active: #252f40; 
        --soft-nav-icon-bg: #ffffff;
        --soft-nav-icon-color: #252f40;
        --soft-nav-border: rgba(255, 255, 255, 0.9); 
        --soft-nav-shadow: 0 8px 24px 0 rgba(0, 0, 0, 0.04); 
    }

    /* Dark Mode Variables */
    html[data-bs-theme="dark"],
    body[data-bs-theme="dark"],
    html[data-theme="dark"],
    body[data-theme="dark"] {
        --soft-body-bg: #111c26;       
        --soft-card-bg: rgba(27, 37, 49, 0.6); 
        --soft-card-border: rgba(255, 255, 255, 0.05);
        --soft-text-main: #ffffff;     
        --soft-text-muted: #a0aec0;    
        --soft-input-bg: rgba(0, 0, 0, 0.2); 
        
        /* GLASSMORPHISM - Dark Mode Navigation */
        --soft-nav-bg: rgba(27, 37, 49, 0.65);
        --soft-nav-text: rgba(255, 255, 255, 0.7);
        --soft-nav-text-active: #ffffff;
        --soft-nav-icon-bg: rgba(0,0,0,0.25);
        --soft-nav-icon-color: #ffffff;
        --soft-nav-border: rgba(255, 255, 255, 0.05);
        --soft-nav-shadow: 0 8px 24px 0 rgba(0, 0, 0, 0.15);
    }

    /* 2. APPLY BODY BACKGROUND */
    body, 
    .page-wrapper, 
    #main {
        background-color: var(--soft-body-bg) !important;
    }

    /* =========================================
       3. THEME-AWARE FLOATING SIDEBAR 
    ========================================= */
    .navbar-vertical, 
    aside.navbar-vertical,
    .navbar-vertical.navbar-dark {
        background-color: var(--soft-nav-bg) !important; 
        
        backdrop-filter: blur(12px) saturate(200%) !important;
        -webkit-backdrop-filter: blur(12px) saturate(200%) !important;
        
        background-image: none !important;
        border: 1px solid var(--soft-nav-border) !important;
        border-radius: 1rem !important;
        box-shadow: var(--soft-nav-shadow) !important; 
        
        margin: 1rem 0 1rem 1rem !important; 
        
        min-height: calc(100vh - 2rem) !important; 
        height: auto !important; 
        overflow-y: auto !important; 
        overflow-x: hidden !important;
        z-index: 1040 !important;
    }

    .navbar-vertical::-webkit-scrollbar { width: 4px; }
    .navbar-vertical::-webkit-scrollbar-track { background: transparent; }
    .navbar-vertical::-webkit-scrollbar-thumb { background: rgba(103, 116, 142, 0.2); border-radius: 10px; }
    html[data-bs-theme="dark"] .navbar-vertical::-webkit-scrollbar-thumb { background: rgba(255, 255, 255, 0.2); }

    .navbar-vertical .navbar-brand,
    .navbar-brand span,
    .navbar-brand-text,
    .navbar-brand b {
        color: var(--soft-nav-text-active) !important;
    }

    .navbar-vertical .nav-item {
        margin: 4px 15px !important;
    }

    .navbar-vertical .nav-link,
    .navbar-dark .navbar-nav .nav-link {
        border-radius: 0.75rem !important;
        padding: 10px 15px !important;
        font-weight: 500 !important;
        color: var(--soft-nav-text) !important;
        transition: all 0.2s ease;
    }

    .navbar-vertical .nav-link:hover,
    .navbar-dark .navbar-nav .nav-link:hover {
        color: var(--soft-nav-text-active) !important;
        background-color: rgba(203, 12, 159, 0.05) !important; 
    }

    .navbar-vertical .nav-link-icon,
    .navbar-vertical .nav-link i,
    .navbar-dark .navbar-nav .nav-link i {
        background: var(--soft-nav-icon-bg) !important; 
        color: var(--soft-nav-icon-color) !important;
        width: 32px;
        height: 32px;
        display: inline-flex !important;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        margin-right: 12px !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    }

    .navbar-vertical .nav-item.active > .nav-link,
    .navbar-dark .navbar-nav .show > .nav-link,
    .navbar-dark .navbar-nav .active > .nav-link {
        background-color: var(--soft-card-bg) !important; 
        color: var(--soft-nav-text-active) !important;
        font-weight: 700 !important;
        box-shadow: var(--soft-ui-shadow) !important; 
    }

    .navbar-vertical .nav-item.active .nav-link-icon,
    .navbar-vertical .nav-item.active .nav-link i {
        background: linear-gradient(310deg, var(--primary-color, #cb0c9f), var(--secondary-color, #7928ca)) !important;
        color: #fff !important;
        box-shadow: 0 4px 6px -1px rgba(203, 12, 159, 0.2) !important;
    }

    /* =========================================
       4. THEME-AWARE FLOATING TOP NAVBAR 
    ========================================= */
    header.navbar, 
    .page-header, 
    .navbar-custom,
    header.navbar.navbar-dark,
    .navbar.sticky-top {
        background-color: var(--soft-nav-bg) !important; 
        
        backdrop-filter: blur(12px) saturate(200%) !important;
        -webkit-backdrop-filter: blur(12px) saturate(200%) !important;
        
        border-radius: 1rem !important; 
        border: 1px solid var(--soft-nav-border) !important;
        box-shadow: var(--soft-nav-shadow) !important; 
        
        margin: 1rem 1.5rem 1.5rem 1.5rem !important; 
        padding: 0.5rem 1.5rem !important;
        width: auto !important;
        
        position: relative !important;
        z-index: 1050 !important; 
    }

    .page-title,
    .navbar .navbar-brand {
        color: var(--soft-nav-text-active) !important;
        font-weight: 700 !important;
        font-size: 1.15rem !important;
        letter-spacing: -0.025rem;
    }

    /* ========================================================
       5. TOP STAT BLOCKS (SOLID COLORS + SOFT UI ICONS)
       ======================================================== */
    .solid-stat-card {
        border-radius: var(--soft-ui-radius); 
        box-shadow: var(--soft-ui-shadow);
        padding: 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 120px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
        border: none;
    }
    .solid-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
    }
    
    /* Original Theme Colors */
    .bg-brand-red { background-color: #da3b36 !important; }
    .bg-brand-green { background-color: #2ab651 !important; }
    .bg-brand-blue { background-color: #3b8de7 !important; }
    
    .solid-stat-content {
        display: flex;
        flex-direction: column;
        z-index: 2; 
    }
    
    .solid-stat-label {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.05rem;
        font-weight: 500;
        margin-bottom: 4px;
    }
    
    .solid-stat-value {
        color: #ffffff;
        font-size: 3.2rem;
        font-weight: 600;
        line-height: 1;
    }
    
    .solid-stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background-color: rgba(255, 255, 255, 0.2); 
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        z-index: 2;
    }
    
    .solid-stat-icon svg,
    .solid-stat-icon i {
        width: 30px !important;
        height: 30px !important;
        font-size: 30px !important;
        color: #ffffff !important; 
        stroke: #ffffff !important;
        stroke-width: 1.5;
        fill: none;
    }

    /* =========================================
       6. DASHBOARD CARDS & STAT WIDGETS (SOFT UI)
    ========================================= */
    .card {
        background-color: var(--soft-card-bg) !important; 
        backdrop-filter: blur(10px) saturate(180%);
        border: 1px solid var(--soft-card-border) !important;
        border-radius: var(--soft-ui-radius) !important;
        box-shadow: var(--soft-ui-shadow) !important;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease-in-out;
        overflow: hidden;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 27px 0 rgba(0, 0, 0, 0.1) !important;
    }

    .card .card-header,
    .card-header {
        background: transparent !important;
        border-bottom: 1px solid var(--soft-card-border) !important;
        padding: 1.25rem 1.5rem !important;
    }

    .card .card-header h6, 
    .card .card-header .card-title,
    .card-title { 
        font-weight: 700 !important;
        color: var(--soft-text-main) !important;
        font-size: 1.1rem !important;
        letter-spacing: -0.025rem;
    }

    .card-body {
        padding: 1.5rem !important;
    }

    /* =========================================
       7. SLEEK SOFT UI TABLES & LISTS 
    ========================================= */
    .list-group-item {
        border-color: var(--soft-card-border) !important;
        padding: 1rem 1.5rem !important;
        background-color: transparent !important;
        color: var(--soft-text-main) !important;
    }
    .list-group-item:first-child { border-top: none; }
    
    .table th {
        background: transparent !important;
        border-bottom: 1px solid var(--soft-card-border) !important;
        color: var(--soft-text-muted) !important;
        text-transform: uppercase;
        font-size: 0.65rem !important;
        font-weight: 700 !important;
        letter-spacing: 0.05rem;
        padding: 0.75rem 1.25rem !important;
        opacity: 0.7;
    }

    .table td {
        vertical-align: middle;
        padding: 1rem 1.5rem !important;
        border-bottom: 1px solid var(--soft-card-border) !important;
        font-size: 0.875rem !important;
        color: var(--soft-text-main) !important;
    }

    .table tbody tr:last-child td { border-bottom: none !important; }

    /* =========================================
       8. BUTTONS & INPUTS
    ========================================= */
    .d-grid .btn {
        border-radius: 8px !important;
        font-weight: 600 !important;
        margin-bottom: 0.5rem;
        text-align: left;
        padding: 0.75rem 1.25rem !important;
        transition: transform 0.2s ease;
    }
    .d-grid .btn:hover {
        transform: translateX(4px); 
    }
    .d-grid .btn svg {
        margin-right: 8px;
    }

    .btn-primary {
        background: linear-gradient(310deg, var(--primary-color, #cb0c9f), var(--secondary-color, #7928ca)) !important;
        border: none !important;
        border-radius: 2rem !important; 
        box-shadow: 0 4px 6px rgba(50, 50, 93, 0.11), 0 1px 3px rgba(0, 0, 0, 0.08) !important;
        font-weight: 600 !important;
        letter-spacing: 0.5px !important;
        transition: all 0.2s ease;
        color: #fff !important;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08) !important;
    }
</style>

    @if($totalCars == 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border:none !important; background: linear-gradient(135deg, #d84a38, #b93d2e) !important;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center mb-3">
                                <div class="bg-white rounded-circle p-3 me-3 position-relative">
                                    <x-core::icon name="ti ti-car" class="text-primary" style="width: 2.5rem; height: 2.5rem; color: #d84a38 !important;" />
                                    <span class="position-absolute" style="bottom: 8px; right: 8px;">
                                        <x-core::icon name="ti ti-plus" class="text-success bg-white rounded-circle" style="width: 1.2rem; height: 1.2rem;" />
                                    </span>
                                </div>
                                <h2 class="mb-0 text-white">{{ __('Welcome to Your Car Rental Dashboard!') }}</h2>
                            </div>
                            <p class="fs-5 mb-4 text-white opacity-75">{{ __('You haven\'t added any cars to your fleet yet. Start your journey by adding your first car and begin earning revenue from rentals.') }}</p>
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('car-rentals.vendor.cars.create') }}" class="btn btn-lg btn-light text-dark" style="border-radius: 8px !important; font-weight: 600;">
                                    <x-core::icon name="ti ti-plus" class="me-1" /> {{ __('Add Your First Car') }}
                                </a>
                                <a href="{{ route('car-rentals.vendor.settings.index') }}" class="btn btn-lg btn-outline-light" style="border-radius: 8px !important; font-weight: 600;">
                                    <x-core::icon name="ti ti-settings" class="me-1" /> {{ __('Complete Your Profile') }}
                                </a>
                            </div>
                        </div>
                        <div class="col-md-4 d-none d-md-block text-center">
                            <x-core::icon name="ti ti-car" class="text-white opacity-25" style="width: 12rem; height: 12rem;" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <div class="solid-stat-card bg-brand-red">
                <div class="solid-stat-content">
                    <div class="solid-stat-label">{{ __('Cars') }}</div>
                    <div class="solid-stat-value">{{ $totalCars }}</div>
                </div>
                <div class="solid-stat-icon">
                    <x-core::icon name="ti ti-car" />
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <div class="solid-stat-card bg-brand-green">
                <div class="solid-stat-content">
                    <div class="solid-stat-label">{{ __('Bookings') }}</div>
                    <div class="solid-stat-value">{{ $totalBookings }}</div>
                </div>
                <div class="solid-stat-icon">
                    <x-core::icon name="ti ti-calendar-event" />
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <div class="solid-stat-card bg-brand-blue">
                <div class="solid-stat-content">
                    <div class="solid-stat-label">{{ __('Revenue') }}</div>
                    <div class="solid-stat-value">{{ format_price($data['revenue']['amount'] ?? 0) }}</div>
                </div>
                <div class="solid-stat-icon">
                    <x-core::icon name="ti ti-wallet" />
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
            <div class="solid-stat-card bg-brand-red">
                <div class="solid-stat-content">
                    <div class="solid-stat-label">{{ __('Messages') }}</div>
                    <div class="solid-stat-value">{{ $totalMessages }}</div>
                </div>
                <div class="solid-stat-icon">
                    <x-core::icon name="ti ti-mail-check" />
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8 mb-3 mb-lg-0">
            <x-core::card class="shadow-sm h-100">
                <x-core::card.header class="d-flex justify-content-between align-items-center">
                    <div>
                        <x-core::card.title>
                            <x-core::icon name="ti ti-chart-area-line" class="me-1 text-primary" />
                            {{ __('Revenue & Bookings Overview') }}
                        </x-core::card.title>
                    </div>
                    <select class="form-select form-select-sm text-muted shadow-none" style="width: auto;">
                        <option value="7">{{ __('Last 7 Days') }}</option>
                        <option value="30" selected>{{ __('Last 30 Days') }}</option>
                        <option value="year">{{ __('This Year') }}</option>
                    </select>
                </x-core::card.header>
                <x-core::card.body>
                    <div id="modern-revenue-chart" style="height: 350px;"></div>
                </x-core::card.body>
            </x-core::card>
        </div>

        <div class="col-lg-4">
            <x-core::card class="shadow-sm h-100">
                <x-core::card.header>
                    <div>
                        <x-core::card.title>
                            <x-core::icon name="ti ti-steering-wheel" class="me-1 text-warning" />
                            {{ __('Revenue by Car') }}
                        </x-core::card.title>
                    </div>
                </x-core::card.header>
                <x-core::card.body class="d-flex align-items-center justify-content-center">
                    <div id="top-cars-chart" style="width: 100%;"></div>
                </x-core::card.body>
            </x-core::card>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-3 col-md-4 mb-3">
            <x-core::card class="h-100 shadow-sm">
                <x-core::card.header>
                    <div>
                        <x-core::card.title>
                            <x-core::icon name="ti ti-bolt" class="me-1 text-warning" />
                            {{ __('Quick Actions') }}
                        </x-core::card.title>
                    </div>
                </x-core::card.header>
                <x-core::card.body>
                    <div class="d-grid gap-2">
                        <a href="{{ route('car-rentals.vendor.cars.create') }}" class="btn btn-primary">
                            <x-core::icon name="ti ti-plus" /> {{ __('Add New Car') }}
                        </a>
                        <a href="{{ route('car-rentals.vendor.bookings.index') }}" class="btn btn-info text-white">
                            <x-core::icon name="ti ti-calendar" /> {{ __('Manage Bookings') }}
                        </a>
                        <a href="{{ route('car-rentals.vendor.settings.index') }}" class="btn btn-secondary mt-2">
                            <x-core::icon name="ti ti-settings" /> {{ __('Account Settings') }}
                        </a>
                    </div>
                </x-core::card.body>
            </x-core::card>
        </div>

        <div class="col-lg-4 col-md-8 mb-3">
            <x-core::card class="h-100 shadow-sm">
                <x-core::card.header>
                    <div>
                        <x-core::card.title>
                            <x-core::icon name="ti ti-alert-triangle" class="me-1 text-danger" />
                            {{ __('Maintenance Alerts') }}
                        </x-core::card.title>
                    </div>
                </x-core::card.header>
                <x-core::card.body class="p-0">
                    <div class="list-group list-group-flush">
                        @if(isset($data['maintenanceAlerts']) && count($data['maintenanceAlerts']) > 0)
                            @foreach($data['maintenanceAlerts'] as $alert)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between align-items-center">
                                        <h6 class="mb-1 text-truncate fw-bold">
                                            <x-core::icon name="ti ti-car" class="me-1" />
                                            {{ $alert->car ? ($alert->car->name ?? $alert->car->license_plate ?? __('Car #:id', ['id' => $alert->car->id])) : __('Unknown Car') }}
                                        </h6>
                                        <span class="badge text-white bg-{{ $alert->priority == 'high' ? 'danger' : ($alert->priority == 'medium' ? 'warning' : 'info') }}">
                                            {{ ucfirst($alert->priority ?? __('low')) }}
                                        </span>
                                    </div>
                                    <p class="mb-1 small">{{ $alert->message ?? __('This car may need maintenance.') }}</p>
                                    <small><x-core::icon name="ti ti-clock" size="14"/> {{ __('Last maintenance') }}: {{ $alert->last_maintenance ? $alert->last_maintenance->format('M d, Y') : __('Never') }}</small>
                                </div>
                            @endforeach
                        @else
                            <div class="empty py-4 text-center">
                                <div class="empty-icon mb-3">
                                    <x-core::icon name="ti ti-circle-check" class="text-success" style="width: 3rem; height: 3rem;" />
                                </div>
                                <p class="empty-title h5 fw-bold">{{ __('No maintenance alerts') }}</p>
                                <p class="empty-subtitle">
                                    {{ __('All your cars are in great condition!') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </x-core::card.body>
            </x-core::card>
        </div>

        <div class="col-lg-5 col-md-12 mb-3">
            <x-core::card class="h-100 shadow-sm">
                <x-core::card.header class="d-flex justify-content-between align-items-center">
                    <div>
                        <x-core::card.title>
                            <x-core::icon name="ti ti-star" class="me-1 text-warning" />
                            {{ __('Recent Reviews') }}
                        </x-core::card.title>
                    </div>
                    <a href="{{ route('car-rentals.vendor.reviews.index') }}" class="btn btn-sm btn-outline-secondary border-0" style="background: transparent !important;">
                        {{ __('View All') }}
                        <x-core::icon name="ti ti-arrow-right" />
                    </a>
                </x-core::card.header>
                <x-core::card.body class="p-0">
                    <div class="list-group list-group-flush">
                        @if(isset($data['recentReviews']) && count($data['recentReviews']) > 0)
                            @foreach($data['recentReviews'] as $review)
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0 text-truncate fw-bold">
                                            @if($review->car)
                                                {{ $review->car->name ?? $review->car->license_plate ?? __('Car #:id', ['id' => $review->car->id]) }}
                                            @else
                                                {{ __('Unknown Car') }}
                                            @endif
                                        </h6>
                                        <div class="rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="ti {{ $i <= ($review->star ?? 0) ? 'ti-star-filled text-warning' : 'ti-star opacity-25' }}" style="font-size: 14px;"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="mb-2 small">{{ Str::limit($review->content ?? __('No content'), 80) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="fw-semibold"><x-core::icon name="ti ti-user" size="14"/> {{ $review->customer ? $review->customer->name : __('Unknown Customer') }}</small>
                                        <small>{{ $review->created_at ? $review->created_at->diffForHumans() : __('Unknown time') }}</small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty py-4 text-center">
                                <div class="empty-icon mb-3">
                                    <x-core::icon name="ti ti-message-circle" class="opacity-50" style="width: 3rem; height: 3rem;" />
                                </div>
                                <p class="empty-title h5 fw-bold">{{ __('No reviews yet') }}</p>
                                <p class="empty-subtitle">
                                    {{ __('Reviews will appear here when customers leave feedback.') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </x-core::card.body>
            </x-core::card>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-lg-8 col-md-7 mb-3">
            <x-core::card class="shadow-sm h-100">
                <x-core::card.header class="d-flex justify-content-between align-items-center">
                    <div>
                        <x-core::card.title>
                            <x-core::icon name="ti ti-chart-bar" class="me-1 text-primary" />
                            {{ __('Top Performing Cars Table') }}
                        </x-core::card.title>
                    </div>
                    <a href="{{ route('car-rentals.vendor.cars.index') }}" class="btn btn-sm btn-outline-secondary border-0" style="background: transparent !important;">
                        {{ __('View All') }}
                        <x-core::icon name="ti ti-arrow-right" />
                    </a>
                </x-core::card.header>
                <x-core::card.body class="p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('Car Details') }}</th>
                                    <th class="text-center">{{ __('Bookings') }}</th>
                                    <th class="text-end">{{ __('Revenue') }}</th>
                                    <th class="text-center">{{ __('Status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($data['topCars']) && count($data['topCars']) > 0)
                                    @foreach($data['topCars'] as $car)
                                        <tr>
                                            <td>
                                                <a href="{{ route('car-rentals.vendor.cars.edit', $car->id) }}" class="text-decoration-none fw-bold d-flex align-items-center">
                                                    <div class="rounded p-2 me-3" style="background: rgba(0,0,0,0.05);">
                                                        <x-core::icon name="ti ti-car" />
                                                    </div>
                                                    {{ $car->name ?? $car->license_plate ?? __('Car #:id', ['id' => $car->id]) }}
                                                </a>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge border text-dark">{{ $car->bookings_count ?? 0 }}</span>
                                            </td>
                                            <td class="text-end fw-bold text-success">{{ format_price($car->revenue ?? 0) }}</td>
                                            <td class="text-center">
                                                @if(isset($car->status) && method_exists($car->status, 'toHtml'))
                                                    {!! $car->status->toHtml() !!}
                                                @else
                                                    <span class="badge bg-secondary">{{ __('Unknown') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="empty">
                                                <div class="empty-icon mb-3">
                                                    <x-core::icon name="ti ti-car-off" class="opacity-50" style="width: 3rem; height: 3rem;" />
                                                </div>
                                                <p class="empty-title h4 fw-bold">{{ __('No cars in your fleet yet') }}</p>
                                                <p class="empty-subtitle">
                                                    {{ __('Start by adding your first car to begin receiving bookings and earning revenue.') }}
                                                </p>
                                                <div class="empty-action mt-3">
                                                    <a href="{{ route('car-rentals.vendor.cars.create') }}" class="btn btn-primary">
                                                        <x-core::icon name="ti ti-plus" class="me-1" /> {{ __('Add Your First Car') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </x-core::card.body>
            </x-core::card>
        </div>

        <div class="col-lg-4 col-md-5 mb-3">
            <x-core::card class="shadow-sm h-100">
                <x-core::card.header>
                    <div>
                        <x-core::card.title>
                            <x-core::icon name="ti ti-report-money" class="me-1 text-success" />
                            {{ __('Revenue Summary') }}
                        </x-core::card.title>
                        <x-core::card.subtitle class="mt-1">
                            {{ __('Period: :label', ['label' => $data['predefinedRange'] ?? __('Last 30 days')]) }}
                        </x-core::card.subtitle>
                    </div>
                </x-core::card.header>
                <x-core::card.body>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="rounded p-3 text-center h-100" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05);">
                                <div class="small fw-semibold mb-1">{{ __('Gross Earnings') }}</div>
                                <div class="h5 mb-0 fw-bold">{{ format_price($data['revenue']['sub_amount'] ?? 0) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rounded p-3 text-center h-100" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05);">
                                <div class="small fw-semibold mb-1">{{ __('Net Revenue') }}</div>
                                <div class="h5 mb-0 fw-bold text-success">{{ format_price(($data['revenue']['sub_amount'] ?? 0) - ($data['revenue']['fee'] ?? 0)) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rounded p-3 text-center h-100" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05);">
                                <div class="small fw-semibold mb-1">{{ __('Platform Fees') }}</div>
                                <div class="h5 mb-0 fw-bold text-danger">{{ format_price($data['revenue']['fee'] ?? 0) }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="rounded p-3 text-center h-100" style="background: rgba(0,0,0,0.02); border: 1px solid rgba(0,0,0,0.05);">
                                <div class="small fw-semibold mb-1">{{ __('Current Balance') }}</div>
                                <div class="h5 mb-0 fw-bold text-primary">{{ format_price(auth('customer')->user()->balance ?? 0) }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('car-rentals.vendor.withdrawals.create') }}" class="btn btn-primary">
                            <x-core::icon name="ti ti-cash" /> {{ __('Request Withdrawal') }}
                        </a>
                        <a href="{{ route('car-rentals.vendor.revenues.index') }}" class="btn btn-secondary mt-1">
                            <x-core::icon name="ti ti-chart-line" /> {{ __('View Detailed Reports') }}
                        </a>
                    </div>
                </x-core::card.body>
            </x-core::card>
        </div>
    </div>

{{-- SCRIPT INCLUDES FOR APEXCHARTS --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        
        // --- 1. REVENUE & BOOKINGS CHART (DYNAMIC) ---
        const chartDates = {!! json_encode($chartData['dates'] ?? ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']) !!};
        const revenueData = {!! json_encode($chartData['revenue'] ?? [0, 0, 0, 0, 0, 0, 0]) !!};
        const bookingsData = {!! json_encode($chartData['bookings'] ?? [0, 0, 0, 0, 0, 0, 0]) !!};

        var revenueOptions = {
            series: [{
                name: '{{ __('Revenue') }}',
                data: revenueData 
            }, {
                name: '{{ __('Bookings') }}',
                data: bookingsData 
            }],
            chart: {
                height: 330,
                type: 'area',
                fontFamily: 'inherit',
                toolbar: { show: false },
                background: 'transparent'
            },
            colors: ['#cb0c9f', '#d84a38'], 
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: [2, 2] },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.05, stops: [0, 90, 100] }
            },
            xaxis: {
                categories: chartDates,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: { style: { colors: 'var(--soft-text-muted)' } }
            },
            yaxis: [
                {
                    title: { text: '{{ __('Revenue') }}', style: { color: 'var(--soft-text-muted)', fontWeight: 600 } },
                    labels: { style: { colors: 'var(--soft-text-muted)' } }
                },
                {
                    opposite: true,
                    title: { text: '{{ __('Bookings') }}', style: { color: 'var(--soft-text-muted)', fontWeight: 600 } },
                    labels: { style: { colors: 'var(--soft-text-muted)' } }
                }
            ],
            legend: { position: 'top', horizontalAlign: 'right', markers: { radius: 12 }, labels: { colors: 'var(--soft-text-main)' } },
            grid: { borderColor: 'var(--soft-card-border)', strokeDashArray: 4, yaxis: { lines: { show: true } } },
            theme: { mode: document.documentElement.getAttribute('data-bs-theme') || 'light' }
        };

        var revenueChart = new ApexCharts(document.querySelector("#modern-revenue-chart"), revenueOptions);
        revenueChart.render();


        // --- 2. TOP CARS DONUT CHART (DYNAMIC) ---
        const topCarsSeries = {!! json_encode($topCarsChart['revenues'] ?? [1]) !!};
        const topCarsLabels = {!! json_encode($topCarsChart['labels'] ?? ['No Data']) !!};

        var topCarsOptions = {
            series: topCarsSeries, 
            labels: topCarsLabels, 
            chart: {
                type: 'donut',
                height: 350,
                fontFamily: 'inherit',
                background: 'transparent'
            },
            colors: ['#cb0c9f', '#d84a38', '#f59e0b', '#17ad37', '#2152ff'], 
            dataLabels: { enabled: false },
            stroke: { width: 0 },
            plotOptions: {
                pie: {
                    donut: {
                        size: '75%',
                        labels: {
                            show: true,
                            name: { show: true, color: 'var(--soft-text-muted)', fontSize: '14px', fontWeight: 600 },
                            value: {
                                show: true, color: 'var(--soft-text-main)', fontSize: '24px', fontWeight: 800,
                                formatter: function (val) { return "$" + val; }
                            },
                            total: {
                                show: true, label: '{{ __('Top Earner') }}', color: 'var(--soft-text-muted)',
                                formatter: function (w) {
                                    return w.globals.seriesTotals[0] === 1 && topCarsLabels[0] === 'No Data' ? "$0" : "$" + Math.max(...w.globals.seriesTotals);
                                }
                            }
                        }
                    }
                }
            },
            legend: { position: 'bottom', markers: { radius: 12 }, itemMargin: { horizontal: 10, vertical: 5 }, labels: { colors: 'var(--soft-text-main)' } },
            theme: { mode: document.documentElement.getAttribute('data-bs-theme') || 'light' }
        };

        var topCarsChart = new ApexCharts(document.querySelector("#top-cars-chart"), topCarsOptions);
        topCarsChart.render();
    });
</script>

@stop