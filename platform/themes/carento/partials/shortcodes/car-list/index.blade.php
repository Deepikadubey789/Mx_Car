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
           MXCar Brand Car-List — Full CSS Variable Override
           Root cause: .car-list-modern--bold uses CSS vars for all blues
           Solution: Override the vars + beat hardcoded backgrounds
           Palette: Red #B03A2E | Black #111111 | White #FFFFFF
        ============================================================ */

        /* 1. Override CSS Custom Properties to swap out ALL blues at source */
        .car-list-modern--bold {
            --car-bold-shell:           #F4F6F8 !important;  /* was: #edf4fb (blue shell) */
            --car-bold-panel:           #ffffff !important;  /* was: #ffffff — panel bg */
            --car-bold-panel-alt:       #F4F6F8 !important;  /* was: #f5f9fd (blue tint) */
            --car-bold-ink:             #111111 !important;  /* was: #17263a (dark blue) */
            --car-bold-muted:           #6c757d !important;  /* was: #617a95 (blue grey) */
            --car-bold-border:          #E9ECEF !important;  /* was: #cfdded (blue border) */
            --car-bold-border-strong:   #DEE2E6 !important;  /* was: #b8cbdf (strong blue border) */
            --car-bold-highlight:       rgba(176, 58, 46, 0.08) !important; /* was: primary-color (green) */
            --car-bold-highlight-border: rgba(176, 58, 46, 0.2) !important; /* was: primary-color (green) */
            --car-bold-shadow:          0 20px 42px rgba(0, 0, 0, 0.08) !important;
            --car-bold-shadow-soft:     0 10px 24px rgba(0, 0, 0, 0.04) !important;
        }

        /* 2. Page background: kill the blue radial-gradient overlay */
        .car-list-modern--bold .box-section.block-content-tourlist::before {
            background: none !important;
        }

        /* 3. Hero box: kill the blue gradient background */
        .car-list-modern--bold .car-list-hero {
            background: #ffffff !important;
        }

        /* 4. Hero inventory panel: kill the HARDCODED dark navy gradient */
        .car-list-modern--bold .car-list-hero__panel {
            background: #B03A2E !important;
            border: none !important;
            box-shadow: none !important;
        }

        /* 5. Filter toolbar box: kill blue gradient */
        .car-list-modern--bold .cars-listing-modern .box-filters-modern {
            background: #ffffff !important;
        }
        .car-list-modern--bold .cars-listing-modern .box-filters-modern::after {
            display: none !important;
        }

        /* 6. Hero before-blob: hide blue radial */
        .car-list-modern--bold .car-list-hero::before {
            display: none !important;
        }

        /* --- Hero Panel (inventory count) — was dark navy --- */
        .car-list-hero,
        .car-list-hero-wrap .car-list-hero {
            background-color: #ffffff !important;
            border: 1px solid #E9ECEF !important;
            border-radius: 20px !important;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04) !important;
            padding: 32px !important;
        }

        .car-list-hero__panel {
            background-color: #B03A2E !important;
            color: #ffffff !important;
            border-radius: 16px !important;
            padding: 24px !important;
            border: none !important;
        }

        .car-list-hero__panel-value {
            color: #ffffff !important;
            font-size: 3rem !important;
            font-weight: 800 !important;
            line-height: 1 !important;
        }

        .car-list-hero__panel-label,
        .car-list-hero__panel-text {
            color: rgba(255,255,255,0.85) !important;
        }

        .car-list-hero__panel-meta {
            border-top: 1px solid rgba(255,255,255,0.2) !important;
            padding-top: 12px !important;
            margin-top: 12px !important;
            color: rgba(255,255,255,0.7) !important;
            display: flex;
            gap: 16px;
        }

        .car-list-hero__title {
            color: #111111 !important;
            font-weight: 800 !important;
        }

        .car-list-hero__eyebrow {
            color: #B03A2E !important;
            font-weight: 600 !important;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .car-list-hero__badge {
            background-color: rgba(176, 58, 46, 0.08) !important;
            color: #B03A2E !important;
            border-radius: 20px !important;
            padding: 6px 14px !important;
            font-size: 0.8rem !important;
            font-weight: 600 !important;
            border: 1px solid rgba(176, 58, 46, 0.15) !important;
        }

        /* --- Filter Toolbar Bar --- */
        .box-filters-modern,
        .car-list-stage {
            background-color: #ffffff !important;
            border: 1px solid #E9ECEF !important;
            border-radius: 16px !important;
            padding: 24px !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03) !important;
        }

        .car-list-stage__eyebrow {
            color: #B03A2E !important;
            font-weight: 600 !important;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 1px;
        }

        .car-list-stage__title {
            color: #111111 !important;
            font-weight: 800 !important;
        }

        .car-list-stage__mode {
            color: #B03A2E !important;
            font-weight: 700 !important;
        }

        /* Toolbar (Layout + Sort + Per page) */
        .car-list-toolbar {
            background-color: #F4F6F8 !important;
            border-radius: 10px !important;
            padding: 10px 16px !important;
            margin-top: 16px !important;
        }

        .car-list-toolbar__label {
            color: #6c757d !important;
            font-size: 0.8rem;
        }

        .layout-switcher-group a.active svg path {
            fill: #B03A2E !important;
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
            background-color: #ffffff !important;
            border: 1px solid #E9ECEF !important;
            border-radius: 20px !important;
            padding: 24px 28px !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.03) !important;
        }

        /* Card 2 — Filter controls (scrollable) */
        .filter-section.filter-section--desktop {
            background-color: #ffffff !important;
            border: 1px solid #E9ECEF !important;
            border-radius: 20px !important;
            padding: 24px 28px !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.03) !important;
            max-height: calc(100vh - 180px) !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
        }

        /* Thin brand-red scrollbar */
        .filter-section.filter-section--desktop::-webkit-scrollbar {
            width: 4px;
        }
        .filter-section.filter-section--desktop::-webkit-scrollbar-thumb {
            background: rgba(176, 58, 46, 0.35);
            border-radius: 999px;
        }
        .filter-section.filter-section--desktop::-webkit-scrollbar-track {
            background: transparent;
        }

        .car-filters-shell__eyebrow {
            color: #B03A2E !important;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .car-filters-shell__title {
            color: #111111 !important;
            font-weight: 800 !important;
        }

        .car-filters-shell__text {
            color: #6c757d !important;
        }

        /* Filter widgets inside Card 2 — AJAX-safe separator style */
        /* NOTE: We do NOT give filter-widgets their own border/card because the AJAX handler
           re-injects them directly into .filter-section, bypassing the shell flex wrapper.
           Using simple dividers instead keeps appearance consistent on both initial load and AJAX. */
        .filter-section.filter-section--desktop .filter-widget {
            background-color: transparent !important;
            border: none !important;
            border-radius: 0 !important;
            border-bottom: 1px solid #F0F0F0 !important;
            padding: 16px 0 !important;
            margin-bottom: 0 !important;
            box-shadow: none !important;
        }

        .filter-section.filter-section--desktop .filter-widget:first-child {
            padding-top: 8px !important;
        }

        .filter-section.filter-section--desktop .filter-widget:last-child {
            border-bottom: none !important;
            padding-bottom: 8px !important;
        }

        .filter-section.filter-section--desktop .filter-widget-header {
            border-bottom: none !important;
            padding-bottom: 8px !important;
            margin-bottom: 10px !important;
        }

        .filter-section.filter-section--desktop .filter-icon,
        .filter-section.filter-section--desktop .filter-widget-header .filter-icon {
            color: #B03A2E !important;
        }

        .filter-section.filter-section--desktop .filter-title {
            color: #111111 !important;
            font-weight: 700 !important;
            font-size: 0.8rem !important;
            text-transform: uppercase !important;
            letter-spacing: 0.04em !important;
        }

        .filter-section.filter-section--desktop .form-control,
        .filter-section.filter-section--desktop .form-select {
            background-color: #F8F9FA !important;
            border-color: #E9ECEF !important;
            border-radius: 10px !important;
        }

        /* --- Car Cards --- */
        .car-card-grid,
        .car-card-list,
        .card-journey-small {
            background-color: #ffffff !important;
            border: 1px solid #E9ECEF !important;
            border-radius: 20px !important;
            box-shadow: 0 4px 16px rgba(0,0,0,0.04) !important;
            transition: all 0.3s ease !important;
            overflow: hidden !important;
        }

        .car-card-grid:hover,
        .car-card-list:hover {
            border-color: #B03A2E !important;
            box-shadow: 0 12px 32px rgba(176, 58, 46, 0.1) !important;
            transform: translateY(-4px) !important;
        }

        /* Chips */
        .car-card-grid__chip {
            background-color: rgba(176, 58, 46, 0.08) !important;
            color: #B03A2E !important;
            border: none !important;
            font-weight: 700 !important;
            border-radius: 8px !important;
        }

        /* Price text */
        .car-card-grid__cta .price,
        .car-card-grid__cta .text-xl-bold {
            color: #111111 !important;
            font-weight: 800 !important;
        }

        /* Book Now button */
        .car-card-grid__cta .btn-primary,
        .btn-book-now,
        .car-list-modern .btn-primary {
            background-color: #B03A2E !important;
            border-color: #B03A2E !important;
            color: #ffffff !important;
            border-radius: 8px !important;
            font-weight: 700 !important;
        }
        .car-card-grid__cta .btn-primary:hover,
        .btn-book-now:hover {
            background-color: #8E2B21 !important;
            box-shadow: 0 6px 20px rgba(176, 58, 46, 0.3) !important;
        }

        /* Form checkboxes & sliders */
        .form-check-input:checked {
            background-color: #B03A2E !important;
            border-color: #B03A2E !important;
        }
        .noUi-connect {
            background: #B03A2E !important;
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
            background-color: #1e1e1e !important;
            border-color: #2e2e2e !important;
        }
        [data-bs-theme="dark"] .car-list-toolbar {
            background-color: #2a2a2a !important;
        }
        [data-bs-theme="dark"] .car-card-grid:hover,
        [data-bs-theme="dark"] .car-card-list:hover {
            border-color: #B03A2E !important;
        }
        [data-bs-theme="dark"] .car-list-hero__title,
        [data-bs-theme="dark"] .car-list-stage__title,
        [data-bs-theme="dark"] .car-filters-shell__title {
            color: #f1f1f1 !important;
        }
    </style>
    <div class="car-list-modern car-list-modern--bold">
    @if ($shortcode->title || $shortcode->subtitle)
        <section class="section-box pt-0 pt-lg-50 background-body car-list-hero-wrap">
            <div class="container">
                <div class="car-list-hero wow fadeInUp">
                    <div class="car-list-hero__content">
                        <p class="car-list-hero__eyebrow mb-0">{{ __('Premium Car Collection') }}</p>

                        @if($shortcode->title)
                            <h2 class="car-list-hero__title shortcode-title mb-0">{{ BaseHelper::clean($shortcode->title) }}</h2>
                        @endif

                        @if($shortcode->subtitle)
                            <p class="car-list-hero__subtitle shortcode-subtitle mb-0">{{ BaseHelper::clean($shortcode->subtitle) }}</p>
                        @endif

                        <div class="car-list-hero__badges">
                            <span class="car-list-hero__badge">{{ __('Flexible booking') }}</span>
                            <span class="car-list-hero__badge">{{ __('Instant availability') }}</span>
                            <span class="car-list-hero__badge">{{ __('Premium support') }}</span>
                        </div>
                    </div>

                    <aside class="car-list-hero__panel" aria-label="{{ __('Inventory overview') }}">
                        <p class="car-list-hero__panel-label mb-0">{{ __('Inventory overview') }}</p>
                        <p class="car-list-hero__panel-value mb-0">{{ number_format($cars->total()) }}</p>
                        <p class="car-list-hero__panel-text mb-0">{{ __('cars currently available') }}</p>

                        <div class="car-list-hero__panel-meta">
                            <span>{{ __('Page :page', ['page' => $cars->currentPage()]) }}</span>
                            <span>{{ __('Per page :perPage', ['perPage' => $cars->perPage()]) }}</span>
                        </div>
                    </aside>
                    </div>
                </div>
        </section>
    @endif

    <section @class([
        'box-section block-content-tourlist background-body',
        'pt-0 pt-lg-50' => ! $shortcode->title && ! $shortcode->subtitle
    ])>
        <div class="container">
            <div class="box-content-main pt-20">
                @include(Theme::getThemeNamespace('views.car-rentals.car-list.partials.car-items',[
                        'cars' => $cars,
                        'defaultLayout' => $defaultLayout,
                        'perPages' => $perPages,
                        'layoutCol' => $layoutCol,
                        'enableFilter' => $enableFilter,
                        'renderAsOffcanvas' => $isMobileDevice,
                    ])
                )

                @if($enableFilter === 'yes')
                    @include(Theme::getThemeNamespace('views.car-rentals.car-list.partials.filters'), [
                        'defaultLayout' => $defaultLayout,
                        'layoutCol' => $layoutCol,
                        'enableFilter' => $enableFilter,
                        'renderAsOffcanvas' => $isMobileDevice,
                    ])
                @endif
            </div>
        </div>
    </section>

    </div>

</section>

<script>
/**
 * MXCar Filter AJAX Patch
 * -------------------------
 * The theme's custom.js injects `filters_html` (the entire filters.blade.php html)
 * into `.filter-section--desktop` (our Card 2), which creates a double-wrapped mess.
 *
 * Fix: After the CarListApp AJAX runs, we swap the WHOLE `.car-filters-modern` element
 * with the fresh server HTML instead of nesting it inside Card 2.
 */
(function ($) {
    'use strict';

    // Wait for the DOM + CarListApp to be ready
    $(document).ready(function () {

        // Intercept every AJAX call to the cars filter endpoint
        $(document).ajaxComplete(function (event, xhr, settings) {
            // Only process the car-listing AJAX
            if (!settings.url || settings.url.indexOf('/ajax/cars') === -1) return;

            try {
                var response = JSON.parse(xhr.responseText);
                if (!response || response.error || !response.additional || !response.additional.filters_html) return;

                var freshFiltersHtml = response.additional.filters_html;
                var $fresh = $(freshFiltersHtml);

                // The fresh HTML is the entire .content-left.car-filters-modern wrapper.
                // Replace the existing wrapper on the page with the fresh version.
                var $existing = $('.car-filters-modern').not('.car-filters-offcanvas');
                if ($existing.length && $fresh.length) {
                    $existing.replaceWith($fresh);

                    // Re-initialize filter widgets (sliders, select2, etc.) in the new DOM
                    if (window.__carListApp && typeof window.__carListApp.carsFilter === 'function') {
                        // Re-bind the filter form (the new HTML has a fresh #cars-filter-form)
                        window.__carListApp.$formSearch = $('#cars-filter-form');
                    }
                }
            } catch (e) {
                // Silently fail — the theme's own handler will still work as fallback
            }
        });
    });
})(jQuery);
</script>
