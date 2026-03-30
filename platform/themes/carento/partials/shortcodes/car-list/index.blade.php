@php
    Theme::asset()->container('footer')->usePath()->add('no-ui-slider', 'js/noUISlider.js');

    $enableFilter = CarRentalsHelper::isEnabledCarFilter() ? $shortcode->enable_filter : 'no';
    $defaultLayout = $shortcode->default_layout;
    $layoutCol = $shortcode->layout_col;
    $mobileDetect = new \Detection\MobileDetect();
    $isMobileDevice = $mobileDetect->isMobile() && ! $mobileDetect->isTablet();
@endphp

<section {!! $shortcode->htmlAttributes() !!}>
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
