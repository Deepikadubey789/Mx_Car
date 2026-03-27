@php
    $bgImage = $shortcode->background_image ? RvMedia::getImageUrl($shortcode->background_image) : null;
    $bgImageTablet = $shortcode->background_image_tablet ? RvMedia::getImageUrl($shortcode->background_image_tablet) : null;
    $bgImageMobile = $shortcode->background_image_mobile ? RvMedia::getImageUrl($shortcode->background_image_mobile) : null;
    $variablesStyle = [
        "--background-image: url($bgImage)" => $bgImage,
        "--background-image-tablet: url(" . ($bgImageTablet ?: $bgImage) . ")" => $bgImageTablet || $bgImage,
        "--background-image-mobile: url(" . ($bgImageMobile ?: $bgImageTablet ?: $bgImage) . ")" => $bgImageMobile || $bgImageTablet || $bgImage,
    ];
@endphp

<section {!! $shortcode->htmlAttributes(['style' => $variablesStyle]) !!}
    class="shortcode-hero-banner box-section block-banner-home1 position-relative"
>
    <div class="container position-relative z-1">
        <p class="text-primary text-md-bold wow fadeInUp">{{ __('Find Your Perfect Car')  }}</p>
        @if ($title = $shortcode->title)
            <h1 class="color-white mb-35 wow fadeInUp heading-1 shortcode-title">{!! BaseHelper::clean($title) !!}</h1>
        @endif
        <ul class="list-ticks-green">
            @foreach($tabs as $tab)
                @continue(! $content = Arr::get($tab, 'content'))
                <li class="wow fadeInUp" data-wow-delay="0.2s">
                    <span class="me-1">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="currentColor"  class="icon icon-tabler icons-tabler-filled icon-tabler-circle-check"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" /></svg>
                    </span>
                    {!! BaseHelper::clean($content) !!}
                </li>
            @endforeach
        </ul>
    </div>
    <div class="bg-shape z-0"></div>
</section>
