@php
    $carsChunkSize = $cars->chunk($shortcode->number_rows);
@endphp

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-cars car-style-lasted section-box box-flights background-body">
    <div class="container">
        <div class="row align-items-end">
            <div class="col-md-9 wow fadeInUp">
                @if ($title)
                    <h2 class="heading-3 title-svg shortcode-title mb-5">{!! BaseHelper::clean($title) !!}</h2>
                @endif

                @if ($subtitle)
                    <p class="text-lg-medium text-bold shortcode-subtitle">{!! BaseHelper::clean($subtitle) !!}</p>
                @endif
            </div>
            <div class="col-md-3 position-relative mb-30 wow fadeInUp">
                <div class="box-button-slider box-button-slider-team justify-content-end">
                    <div class="swiper-button-prev swiper-button-prev-style-1 swiper-button-prev-2">
                        <x-core::icon name="ti ti-arrow-left" />
                    </div>
                    <div class="swiper-button-next swiper-button-next-style-1 swiper-button-next-2">
                        <x-core::icon name="ti ti-arrow-right" />
                    </div>
                </div>
            </div>
        </div>
        <div class="block-flights wow fadeInUp">
            <div class="box-swiper mt-30">
                <div class="swiper-container swiper-group-3 swiper-group-journey">
                    <div class="swiper-wrapper">
                        @foreach($carsChunkSize as $cars)
                            @foreach($cars as $car)
                                <div class="swiper-slide">
                                    <div class="card-journey-small background-card hover-up">
                                        <div class="card-image">
                                            <a href="{{ $car->url }}">
                                                {{ RvMedia::image($car->image, $car->name, 'medium-rectangle') }}
                                            </a>
                                        </div>
                                        <div class="card-info">
                                            <div class="card-rating">
                                                <div class="card-left"></div>
                                                <div class="card-right">
                                                    @include(Theme::getThemeNamespace('views.car-rentals.rating'), ['car' => $car])
                                                </div>
                                            </div>
                                            <div class="card-title"><a class="heading-6 neutral-1000 text-ellipsis-2-lines" href="{{ $car->url }}">{!! BaseHelper::clean($car->name) !!}</a></div>
                                            <div class="card-program">
                                                @if($car->location)
                                                    <div class="card-location">
                                                        <p class="text-location text-md-medium neutral-500 text-truncate" title="{{ $car->location }}">
                                                            {!! BaseHelper::renderIcon('ti ti-map-pin') !!}
                                                            {{ BaseHelper::clean($car->location) }}
                                                        </p>
                                                    </div>
                                                @endif

                                                @include(Theme::getThemeNamespace('views.car-rentals.car-facilities'), ['car' => $car])

                                                <div class="endtime">
                                                    @include(Theme::getThemeNamespace('views.car-rentals.price'), ['car' => $car])
                                                    @include(Theme::getThemeNamespace('views.car-rentals.book-now-button'), ['car' => $car])
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @if(empty($buttonLabel) === false)
            <div class="d-flex justify-content-center">
                <a class="btn btn-primary wow fadeInUp" href="{{ $buttonUrl }}">
                    {!! BaseHelper::clean($buttonLabel) !!}
                    <x-core::icon name="ti ti-arrow-right" class="svg-icon-arrow" />
                </a>
            </div>
        @endif
    </div>
</section>
