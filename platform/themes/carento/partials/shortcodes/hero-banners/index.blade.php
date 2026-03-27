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

{{-- FRONTEND CUSTOM CSS SCOPED ONLY TO THIS BANNER --}}
<style>
    /* Spacing around the new contained banner */
    .custom-hero-wrapper {
        padding-top: 2rem;
        padding-bottom: 2rem;
    }

    .custom-hero-v2 {
        background-size: cover !important;
        background-position: center center !important;
        background-repeat: no-repeat !important;
        min-height: 500px; /* Reduced from 75vh for a sleeker, Turo-like height */
        display: flex;
        align-items: center;
        justify-content: center;
        
        /* The Turo "Contained" Effect */
        border-radius: 1.5rem; 
        overflow: hidden; /* Keeps the gradient inside the rounded corners */
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1); /* Soft floating shadow */
        
        padding-top: 40px;
        padding-bottom: 120px; /* Leaves room for your floating search box below */
    }

    /* Dark gradient overlay to ensure text is always readable over any image */
    .hero-gradient-overlay {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.8) 0%, rgba(15, 23, 42, 0.4) 100%);
        z-index: 0;
    }

    .hero-badge {
        display: inline-block;
        background-color: #d84a38; /* Your theme's primary red */
        color: #ffffff;
        padding: 0.5rem 1.5rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(216, 74, 56, 0.3);
    }

    .hero-main-title {
        font-size: clamp(2.5rem, 5vw, 4rem);
        font-weight: 800;
        color: #ffffff;
        line-height: 1.1;
        margin-bottom: 2rem;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    /* Convert the list into modern Glassmorphism pills */
    .hero-features-wrap {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
        margin-top: 1rem;
        padding: 0;
        list-style: none;
    }

    .hero-feature-pill {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        padding: 0.75rem 1.5rem;
        border-radius: 50px;
        color: #ffffff;
        font-weight: 500;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .hero-feature-pill:hover {
        background: rgba(255, 255, 255, 0.2);
        transform: translateY(-3px);
        border-color: rgba(255, 255, 255, 0.4);
    }

    .hero-feature-pill svg {
        color: #d84a38; /* Red checkmarks to match the badge */
    }
</style>

<div class="container custom-hero-wrapper">
    <section {!! $shortcode->htmlAttributes(['style' => $variablesStyle]) !!}
        class="custom-hero-v2 position-relative"
    >
        <div class="hero-gradient-overlay"></div>

        <div class="container position-relative z-1 text-center">
            
            <div class="wow fadeInUp" data-wow-delay="0.1s">
                <span class="hero-badge">{{ __('Find Your Perfect Car')  }}</span>
            </div>

            @if ($title = $shortcode->title)
                <h1 class="hero-main-title wow fadeInUp" data-wow-delay="0.2s">
                    {!! BaseHelper::clean($title) !!}
                </h1>
            @endif

            <ul class="hero-features-wrap">
                @foreach($tabs as $tab)
                    @continue(! $content = Arr::get($tab, 'content'))
                    <li class="hero-feature-pill wow fadeInUp" data-wow-delay="0.3s">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-circle-check">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" />
                        </svg>
                        {!! BaseHelper::clean($content) !!}
                    </li>
                @endforeach
            </ul>

        </div>
    </section>
</div>