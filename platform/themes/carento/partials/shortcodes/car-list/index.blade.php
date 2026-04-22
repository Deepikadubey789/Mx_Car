@php
    Theme::asset()->container('footer')->usePath()->add('no-ui-slider', 'js/noUISlider.js');

    $enableFilter = CarRentalsHelper::isEnabledCarFilter() ? $shortcode->enable_filter : 'no';
    $defaultLayout = $shortcode->default_layout;
    $layoutCol = $shortcode->layout_col;
    $mobileDetect = new \Detection\MobileDetect();
    $isMobileDevice = $mobileDetect->isMobile() && ! $mobileDetect->isTablet();
@endphp

<section {!! $shortcode->htmlAttributes() !!}>
    <style>
        /* ============================================================
           MXCar Brand Car-List — Dynamic CSS Variables
           All colors use Bootstrap theme variables for full dark mode support
        ============================================================ */

        /* 1. Dynamic CSS Variables using Bootstrap theme colors */
        .car-list-modern--bold {
            --car-bold-shell:           var(--bs-gray-100) !important;
            --car-bold-panel:           var(--bs-body-bg) !important;
            --car-bold-panel-alt:       var(--bs-gray-100) !important;
            --car-bold-ink:             var(--bs-body-color) !important;
            --car-bold-muted:           var(--bs-secondary-color) !important;
            --car-bold-border:          var(--bs-border-color) !important;
            --car-bold-border-strong:   var(--bs-border-color-translucent) !important;
            --car-bold-highlight:       rgba(var(--bs-primary-rgb), 0.08) !important;
            --car-bold-highlight-border: rgba(var(--bs-primary-rgb), 0.2) !important;
            --car-bold-shadow:          0 20px 42px rgba(0, 0, 0, 0.08) !important;
            --car-bold-shadow-soft:     0 10px 24px rgba(0, 0, 0, 0.04) !important;
        }

        /* Force all text to body color (black) */
        .car-list-modern--bold,
        .car-list-modern--bold * {
            color: var(--bs-body-color) !important;
        }

        /* Exception: keep button text colors */
        .car-list-modern--bold .btn-primary,
        .car-list-modern--bold .btn-book-now {
            color: var(--bs-contrast-color, #ffffff) !important;
        }

        /* 2. Page background: kill the blue radial-gradient overlay */
        .car-list-modern--bold .box-section.block-content-tourlist::before {
            background: none !important;
        }

        /* 3. Hero box */
        .car-list-modern--bold .car-list-hero {
            background: var(--bs-body-bg) !important;
        }

        /* 4. Hero inventory panel */
        .car-list-modern--bold .car-list-hero__panel {
            background: var(--bs-primary) !important;
            border: none !important;
            box-shadow: none !important;
        }

        /* 5. Filter toolbar box */
        .car-list-modern--bold .cars-listing-modern .box-filters-modern {
            background: var(--bs-body-bg) !important;
        }
        .car-list-modern--bold .cars-listing-modern .box-filters-modern::after {
            display: none !important;
        }

        /* 6. Hero before-blob: hide blue radial */
        .car-list-modern--bold .car-list-hero::before {
            display: none !important;
        }

        /* --- Hero Panel (inventory count) — */
        .car-list-hero,
        .car-list-hero-wrap .car-list-hero {
            background-color: var(--bs-body-bg) !important;
            border: 1px solid var(--bs-border-color) !important;
            border-radius: 20px !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04) !important;
            padding: 32px !important;
        }

        .car-list-hero__panel {
            background-color: var(--bs-primary) !important;
            color: var(--bs-contrast-color, #ffffff) !important;
            border-radius: 16px !important;
            padding: 24px !important;
            border: none !important;
        }

        .car-list-hero__panel-value {
            color: var(--bs-contrast-color, #ffffff) !important;
            font-size: 3rem !important;
            font-weight: 800 !important;
            line-height: 1 !important;
        }

        .car-list-hero__panel-label,
        .car-list-hero__panel-text {
            color: rgba(var(--bs-contrast-color-rgb, 255, 255, 255), 0.85) !important;
        }

        .car-list-hero__panel-meta {
            border-top: 1px solid rgba(var(--bs-contrast-color-rgb, 255, 255, 255), 0.2) !important;
            padding-top: 12px !important;
            margin-top: 12px !important;
            color: rgba(var(--bs-contrast-color-rgb, 255, 255, 255), 0.7) !important;
            display: flex;
            gap: 16px;
        }

        .car-list-hero__title {
            color: var(--bs-body-color) !important;
            font-weight: 800 !important;
        }

        .car-list-hero__eyebrow {
            color: var(--bs-body-color) !important;
            font-weight: 600 !important;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .car-list-hero__badge {
            background-color: rgba(var(--bs-primary-rgb), 0.08) !important;
            color: var(--bs-body-color) !important;
            border-radius: 20px !important;
            padding: 6px 14px !important;
            font-size: 0.8rem !important;
            font-weight: 600 !important;
            border: 1px solid rgba(var(--bs-primary-rgb), 0.15) !important;
        }

        /* --- Filter Toolbar Bar --- */
        .box-filters-modern,
        .car-list-stage {
            background-color: var(--bs-body-bg) !important;
            border: 1px solid var(--bs-border-color) !important;
            border-radius: 16px !important;
            padding: 24px !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03) !important;
        }

        .car-list-stage__eyebrow {
            color: var(--bs-body-color) !important;
            font-weight: 600 !important;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
        }

        .car-list-stage__title {
            color: var(--bs-body-color) !important;
            font-weight: 800 !important;
        }

        .car-list-stage__mode {
            color: var(--bs-body-color) !important;
            font-weight: 700 !important;
        }

        /* Toolbar (Layout + Sort + Per page) */
        .car-list-toolbar {
            background-color: var(--bs-gray-100) !important;
            border-radius: 10px !important;
            padding: 10px 16px !important;
            margin-top: 16px !important;
        }

        .car-list-toolbar__label {
            color: var(--bs-secondary-color) !important;
            font-size: 0.8rem;
        }

        .layout-switcher-group a.active svg path {
            fill: var(--bs-primary) !important;
        }

        /* --- Sidebar Filter Panel: Two-Card Layout --- */

        /* Outer shell — transparent stack container */
        .car-filters-modern {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        .car-filters-shell {
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            display: flex !important;
            flex-direction: column !important;
            gap: 16px !important;
        }

        /* Card 1 — Intro header */
        .car-filters-shell__intro {
            background-color: var(--bs-body-bg) !important;
            border: 1px solid var(--bs-border-color) !important;
            border-radius: 20px !important;
            padding: 24px 28px !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.03) !important;
        }

        /* Filter controls wrapper (no parent card) */
        .filter-section.filter-section--desktop {
            background: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            padding: 0 !important;
            box-shadow: none !important;
        }

        .car-filters-shell__eyebrow {
            color: var(--bs-body-color) !important;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .car-filters-shell__title {
            color: var(--bs-body-color) !important;
            font-weight: 800 !important;
        }

        .car-filters-shell__text {
            color: var(--bs-secondary-color) !important;
        }

        /* Filter widgets inside Card 2 — each filter is its own block card */
        .filter-section.filter-section--desktop .filter-widget {
            background-color: #f4f5f7 !important;
            border: 1px solid #e6e8ec !important;
            border-radius: 18px !important;
            padding: 18px 18px 16px !important;
            margin-bottom: 18px !important;
            box-shadow: none !important;
        }

        .filter-section.filter-section--desktop .filter-widget::before,
        .filter-section.filter-section--desktop .filter-widget::after {
            display: none !important;
        }

        .filter-section.filter-section--desktop .filter-widget-header {
            border-bottom: 1px solid #d7dce3 !important;
            padding-bottom: 10px !important;
            margin-bottom: 14px !important;
        }

        .filter-section.filter-section--desktop .filter-icon,
        .filter-section.filter-section--desktop .filter-widget-header .filter-icon {
            display: none !important;
        }

        .filter-section.filter-section--desktop .filter-title {
            color: var(--bs-body-color) !important;
            font-weight: 800 !important;
            font-size: 0.75rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.12em !important;
            margin: 0 !important;
        }

        .filter-section.filter-section--desktop .form-control,
        .filter-section.filter-section--desktop .form-select {
            background-color: var(--bs-body-bg) !important;
            border: 1px solid #d5dbe3 !important;
            border-radius: 12px !important;
        }

        /* --- Car Cards --- */
        .car-card-grid,
        .car-card-list,
        .card-journey-small {
            background-color: var(--bs-body-bg) !important;
            border: none !important;
            border-radius: 20px !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04) !important;
            transition: all 0.3s ease !important;
            overflow: hidden !important;
        }

        .car-card-grid:hover,
        .car-card-list:hover {
            border-color: transparent !important;
            box-shadow: 0 12px 32px rgba(var(--bs-primary-rgb), 0.1) !important;
            transform: translateY(-4px) !important;
        }

        /* Chips */
        .car-card-grid__chip {
            background-color: rgba(var(--bs-primary-rgb), 0.08) !important;
            color: var(--bs-body-color) !important;
            border: none !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
        }

        /* Price text */
        .car-card-grid__cta .price,
        .car-card-grid__cta .text-xl-bold {
            color: var(--bs-body-color) !important;
            font-weight: 800 !important;
        }

        /* Book Now button */
        .car-card-grid__cta .btn-primary,
        .btn-book-now,
        .car-list-modern .btn-primary {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
            color: var(--bs-contrast-color, #ffffff) !important;
            border-radius: 8px !important;
            font-weight: 700 !important;
        }
        .car-card-grid__cta .btn-primary:hover,
        .btn-book-now:hover {
            background-color: color-mix(in srgb, var(--bs-primary) 80%, black) !important;
            box-shadow: 0 6px 20px rgba(var(--bs-primary-rgb), 0.3) !important;
        }

        /* Form checkboxes & sliders */
        .form-check-input:checked {
            background-color: var(--bs-primary) !important;
            border-color: var(--bs-primary) !important;
        }
        .noUi-connect {
            background: var(--bs-primary) !important;
        }

        /* --- Dark Mode --- */
        [data-bs-theme="dark"] body,
        [data-bs-theme="dark"] .background-body {
            background-color: var(--bs-body-bg) !important;
        }
        [data-bs-theme="dark"] .car-list-hero,
        [data-bs-theme="dark"] .box-filters-modern,
        [data-bs-theme="dark"] .car-filters-shell,
        [data-bs-theme="dark"] .car-card-grid,
        [data-bs-theme="dark"] .car-card-list {
            background-color: var(--bs-body-bg) !important;
            border-color: var(--bs-border-color) !important;
        }
        [data-bs-theme="dark"] .car-list-toolbar {
            background-color: var(--bs-gray-800) !important;
        }
        [data-bs-theme="dark"] .car-card-grid:hover,
        [data-bs-theme="dark"] .car-card-list:hover {
            border-color: var(--bs-primary) !important;
        }
        [data-bs-theme="dark"] .car-list-hero__title,
        [data-bs-theme="dark"] .car-list-stage__title,
        [data-bs-theme="dark"] .car-filters-shell__title {
            color: var(--bs-body-color) !important;
        }
    </style>
    <div class="car-list-modern car-list-modern--bold">
    <section @class([
        'box-section block-content-tourlist background-body',
        'pt-0 pt-lg-50' => ! $shortcode->title && ! $shortcode->subtitle
    ])>
        <div class="container">
            <div class="box-content-main pt-20">
                @if($enableFilter === 'yes')
                    @include(Theme::getThemeNamespace('views.car-rentals.car-list.partials.filters'), [
                        'defaultLayout' => $defaultLayout,
                        'layoutCol' => $layoutCol,
                        'enableFilter' => $enableFilter,
                        'renderAsOffcanvas' => $isMobileDevice,
                    ])
                @endif

                @include(Theme::getThemeNamespace('views.car-rentals.car-list.partials.car-items',[
                        'cars' => $cars,
                        'defaultLayout' => $defaultLayout,
                        'perPages' => $perPages,
                        'layoutCol' => $layoutCol,
                        'enableFilter' => $enableFilter,
                        'renderAsOffcanvas' => $isMobileDevice,
                    ])
                )
            </div>
        </div>
    </section>

    </div>

</section>
