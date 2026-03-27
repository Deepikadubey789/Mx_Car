@use(Botble\Theme\Supports\Youtube;)

@php
    Theme::set('breadcrumbs', false);
    Theme::layout('full-width');

    $youtubeUrl = $car->getMetaData('youtube_video_url', true);

    $youtubeId = $youtubeUrl ? Youtube::getYoutubeVideoID($youtubeUrl) : null;

    $images = $car->getImages();
@endphp

<div class="car-detail-page">
    @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.breadcrumbs'), compact('car'))

    <section class="section-box box-banner-home2 cars-details4 background-body">
        <div class="container">
            <div class="container-banner-activities">
                <div class="box-banner-activities">
                    <div class="banner-activities-detail">
                        @foreach($images as $image)
                            <div class="banner-slide-activity px-3">
                                {{ RvMedia::image($image, $car->name, 'large-rectangle') }}
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4 mt-sm-0">
                        @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.gallery-buttons'), ['car' => $car])
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="box-section box-content-tour-detail background-body">
        <div class="container">
            <div class="tour-header">
                @if ($car->reviews_count)
                    <div class="tour-rate">
                        <div class="rate-element">
                            @include(Theme::getThemeNamespace('views.car-rentals.rating'), ['car' => $car])
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-lg-8">
                        <div class="tour-title-main">
                            <h4 class="neutral-1000">{{ $car->name }}</h4>
                        </div>
                    </div>
                </div>
                <div class="tour-metas">
                    <div class="tour-meta-left">
                        @if($car->current_location)
                            <p class="text-md-medium neutral-1000 mr-20 tour-location">
                                {!! BaseHelper::renderIcon('ti ti-map-pin') !!}

                                {!! BaseHelper::clean($car->current_location) !!}
                            </p>
                            <a class="text-md-medium neutral-1000 mr-30" href="https://maps.google.com/maps?q={{ addslashes($car->current_location) }}">{{ __('Show on map') }}</a>
                        @endif
                    </div>
                    <div>
                        @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.share-button'), compact('car'))
                    </div>
                </div>
            </div>

            <div class="row mt-20">
                <div class="col-lg-8">
                    @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.attributes'), compact('car'))

                    @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.additional-info'), compact('car'))

                    @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.amenities'), compact('car'))

                    <div class="box-collapse-expand">
                        @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.content'), compact('car'))

                        @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.owner-info'), compact('car'))

                        @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.faqs'), compact('car'))

                        @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.reviews'), compact('car', 'reviews'))
                    </div>
                </div>
                <div class="col-lg-4">
                    @if($car->is_for_sale)
                        @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.sale-info'), compact('car'))
                    @else
                        @include(Theme::getThemeNamespace('views.car-rentals.car-detail.includes.booking-form'), compact('car'))
                    @endif

                    @include(Theme::getThemeNamespace('views.car-rentals.message-form'), compact('car'))
                </div>
            </div>
        </div>
    </div>
</div>
