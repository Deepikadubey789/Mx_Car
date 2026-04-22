
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

<style>
    .custom-hero-wrapper {
        padding-top: 2rem;
        padding-bottom: 9rem;
        padding-left: 0 !important;
        padding-right: 0 !important;
        max-width: 90% !important;
    }

    .custom-hero-v2 {
        position: relative;
        min-height: 500px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 1.5rem;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        padding-top: 40px;
        padding-bottom: 160px;
    }

    .hero-bg {
        position: absolute;
        inset: 0;
        background-image: var(--background-image);
        background-size: cover;
        background-position: center center;
        background-repeat: no-repeat;
        z-index: 0;
    }

    @media (max-width: 768px) {
        .hero-bg {
            background-image: var(--background-image-tablet);
        }
    }

    @media (max-width: 480px) {
        .hero-bg {
            background-image: var(--background-image-mobile);
        }
    }

    .hero-gradient-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.80) 0%, rgba(15, 23, 42, 0.40) 100%);
        z-index: 1;
        opacity: 1;
    }

    .hero-badge {
        display: inline-block;
        background-color: #d84a38;
        color: #ffffff;
        padding: 0.45rem 1.4rem;
        border-radius: 50px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 1.2rem;
        box-shadow: 0 4px 15px rgba(216, 74, 56, 0.35);
    }

    .custom-hero-v2 h1.hero-main-title {
        position: relative;
        display: inline-block;
        font-size: clamp(1.4rem, 2.5vw, 2rem) !important;
        font-weight: 700 !important;
        color: #ffffff !important;
        -webkit-text-fill-color: #ffffff !important;
        line-height: 1.25 !important;
        text-shadow: 0 2px 12px rgba(0, 0, 0, 0.3) !important;
        margin-bottom: 1.6rem !important;
        letter-spacing: 0.02em !important;
    }

    .custom-hero-v2 h1.hero-main-title::before,
    .custom-hero-v2 h1.hero-main-title::after {
        content: none;
        display: none;
    }

    .custom-hero-v2 h1.hero-main-title *,
    .custom-hero-v2 .hero-main-title p,
    .custom-hero-v2 .hero-main-title span,
    .custom-hero-v2 .hero-main-title strong {
        font-size: clamp(1.4rem, 2.5vw, 2rem) !important;
        font-weight: 700 !important;
        color: #ffffff !important;
        -webkit-text-fill-color: #ffffff !important;
        line-height: 1.25 !important;
    }

    .hero-features-wrap {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.85rem;
        margin-top: 0.5rem;
        padding: 0;
        list-style: none;
    }

    .hero-feature-pill {
        background: rgba(255, 255, 255, 0.10);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.22);
        padding: 0.65rem 1.3rem;
        border-radius: 50px;
        color: #ffffff;
        font-weight: 500;
        font-size: 0.92rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: background 0.3s, transform 0.3s, border-color 0.3s;
    }

    .hero-feature-pill:hover {
        background: rgba(255, 255, 255, 0.20);
        transform: translateY(-3px);
        border-color: rgba(255, 255, 255, 0.42);
    }

    .hero-feature-pill svg {
        color: #d84a38;
        flex-shrink: 0;
    }

    .hero-buttons-wrap {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
        margin-top: 2rem;
    }

    .hero-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.8rem;
        border-radius: 50px;
        font-size: 0.92rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        transition: transform 0.25s, box-shadow 0.25s, background 0.25s;
        border: none;
        letter-spacing: 0.3px;
    }

    .hero-btn-primary {
        background: #d84a38;
        color: #ffffff;
        box-shadow: 0 6px 20px rgba(216, 74, 56, 0.40);
    }

    .hero-btn-primary:hover {
        background: #c0392b;
        transform: translateY(-3px);
        box-shadow: 0 10px 28px rgba(216, 74, 56, 0.55);
        color: #ffffff;
        text-decoration: none;
    }

    .hero-btn-ghost {
        background: rgba(255, 255, 255, 0.10);
        color: #ffffff;
        border: 1.5px solid rgba(255, 255, 255, 0.55);
        backdrop-filter: blur(8px);
    }

    .hero-btn-ghost:hover {
        background: rgba(255, 255, 255, 0.22);
        border-color: rgba(255, 255, 255, 0.85);
        transform: translateY(-3px);
        color: #ffffff;
        text-decoration: none;
    }

    .hero-btn-dark {
        background: rgba(15, 23, 42, 0.75);
        color: #ffffff;
        border: 1.5px solid rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(8px);
    }

    .hero-btn-dark:hover {
        background: rgba(15, 23, 42, 0.95);
        transform: translateY(-3px);
        border-color: rgba(255, 255, 255, 0.38);
        color: #ffffff;
        text-decoration: none;
    }

    .hero-neon-buttons-wrap {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 1rem;
        margin-top: 2rem;
    }

    .neon-btn {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-size: 0.92rem;
        font-weight: 600;
        color: #ffffff;
        background: rgba(255, 255, 255, 0.10);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        text-decoration: none;
        cursor: pointer;
        letter-spacing: 0.4px;
        overflow: hidden;
        transition: transform 0.25s, box-shadow 0.25s;
        z-index: 0;
    }

    .neon-btn::before {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: 50px;
        background-size: 200%;
        z-index: -1;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .neon-btn:hover::before {
        opacity: 1;
    }

    .neon-btn:hover {
        transform: translateY(-3px);
        color: #ffffff;
        text-decoration: none;
        background: rgba(255, 255, 255, 0.18);
    }

    .neon-btn-purple,
    .neon-btn-cyan,
    .neon-btn-green {
        border: 1.5px solid #d84a38;
        box-shadow:
            0 0 8px #d84a38,
            0 0 25px #d84a38,
            0 0 60px rgba(216, 74, 56, 0.6),
            0 0 100px rgba(216, 74, 56, 0.3);
    }

    .neon-btn-purple::before,
    .neon-btn-cyan::before,
    .neon-btn-green::before {
        background: linear-gradient(90deg, #d84a38, #ff6b52, #d84a38);
    }

    .neon-btn-purple:hover,
    .neon-btn-cyan:hover,
    .neon-btn-green:hover {
        box-shadow:
            0 0 12px #d84a38,
            0 0 40px #d84a38,
            0 0 90px rgba(216, 74, 56, 0.7),
            0 0 150px rgba(216, 74, 56, 0.4);
    }

    .neon-btn-purple .neon-dot,
    .neon-btn-cyan .neon-star,
    .neon-btn-green .neon-live {
        flex-shrink: 0;
    }

    .neon-btn-purple .neon-dot,
    .neon-btn-green .neon-live {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #d84a38;
        box-shadow: 0 0 8px #d84a38;
    }

    .neon-btn-cyan .neon-star {
        font-size: 14px;
        display: inline-block;
    }
</style>

<div class="container custom-hero-wrapper">
    <section {!! $shortcode->htmlAttributes(['style' => $variablesStyle]) !!}
        class="custom-hero-v2"
    >
        <div class="hero-bg" aria-hidden="true"></div>

        <div class="hero-gradient-overlay" aria-hidden="true"></div>

        <div class="container position-relative text-center" style="z-index:2;">

            <div>
                <span class="hero-badge">{{ __('Find Your Perfect Car') }}</span>
            </div>

            @if ($title = $shortcode->title)
                <h1 class="hero-main-title text-white">
                    {!! BaseHelper::clean($title) !!}
                </h1>
            @endif

            <!-- <ul class="hero-features-wrap">
                @foreach($tabs as $tab)
                    @continue(! $content = Arr::get($tab, 'content'))
                    <li class="hero-feature-pill">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" />
                        </svg>
                        {!! BaseHelper::clean($content) !!}
                    </li>
                @endforeach
            </ul> -->

            <!-- <div class="hero-neon-buttons-wrap">
                <a href="#" class="neon-btn neon-btn-purple">
                    <span class="neon-dot"></span>
                    {{ __('High quality at a low cost') }}
                </a>
                <a href="#" class="neon-btn neon-btn-cyan">
                    <span class="neon-star">✦</span>
                    {{ __('Premium services') }}
                </a>
                <a href="#" class="neon-btn neon-btn-green">
                    <span class="neon-live"></span>
                    {{ __('24/7 roadside support') }}
                </a>
            </div>
             -->

        </div>
    </section>
</div>
