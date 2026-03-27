@php
    Theme::asset()->container('footer')->usePath()->add('no-ui-slider', 'js/noUISlider.js');

    $enableFilter = CarRentalsHelper::isEnabledCarFilter() ? $shortcode->enable_filter : 'no';
    $defaultLayout = $shortcode->default_layout;
    $layoutCol = $shortcode->layout_col;
    $mobileDetect = new \Detection\MobileDetect();
    $isMobileDevice = $mobileDetect->isMobile() && ! $mobileDetect->isTablet();
@endphp

<section {!! $shortcode->htmlAttributes() !!}>
    @if ($shortcode->title || $shortcode->subtitle)
        <section class="section-box pt-0 pt-lg-50 background-body">
            <div class="container">
                <div class="row align-items-end">
                    <div class="col-md-9 mb-30 wow fadeInUp">
                        @if($shortcode->title)
                            <h4 class="title-svg shortcode-title mb-15">{{ BaseHelper::clean($shortcode->title) }}</h4>
                        @endif
                        @if($shortcode->subtitle)
                            <p class="text-lg-medium text-bold shortcode-subtitle">{{ BaseHelper::clean($shortcode->subtitle) }}</p>
                        @endif
                    </div>
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

</section>
