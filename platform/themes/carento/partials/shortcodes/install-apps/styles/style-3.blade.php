@php
    $androidAppUrl = $shortcode->android_app_url ?? '#';
    $androidAppImage = $shortcode->android_app_image;
    $iosAppUrl = $shortcode->ios_app_url ?? '#';
    $iosAppImage = $shortcode->ios_app_image;
    $decorImage = $shortcode->decor_image;
@endphp

<style>
    .install-app-modern-style {
        padding-top: 56px;
        padding-bottom: 56px;
        background-color: transparent !important;
        background-image: none !important;
    }

    .install-app-modern-style .main-card-container {
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(248, 250, 252, 0.96));
        border-radius: 24px;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.08);
        overflow: hidden;
    }

    .install-app-modern-style .content-col {
        padding: 3rem 2.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 1rem;
    }

    .install-app-modern-style .install-app-eyebrow {
        color: #c94c1e;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }

    .install-app-modern-style .shortcode-title {
        color: #101828 !important;
        font-size: clamp(1.65rem, 3vw, 2.2rem) !important;
        font-weight: 800 !important;
        line-height: 1.12 !important;
        letter-spacing: -0.03em;
        margin-bottom: 0 !important;
    }

    .install-app-modern-style .description-p {
        color: #475467 !important;
        font-size: 0.98rem !important;
        line-height: 1.7;
        margin-bottom: 0 !important;
        max-width: 36rem;
    }

    .install-app-modern-style .download-apps {
        display: flex;
        flex-wrap: wrap;
        gap: 0.85rem;
        align-items: flex-start;
    }

    .install-app-modern-style .download-apps a {
        display: inline-flex;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }

    .install-app-modern-style .download-apps a:hover {
        transform: translateY(-2px);
        opacity: 0.95;
    }

    .install-app-modern-style .download-apps img {
        height: 3.4rem;
        width: auto;
        max-width: 100%;
    }

    .install-app-modern-style .image-col {
        position: relative;
        min-height: 360px;
        background: linear-gradient(180deg, rgba(249, 250, 251, 0.15), rgba(249, 250, 251, 0.8));
    }

    .install-app-modern-style .box-app-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
    }

    .install-app-modern-style .box-app-img img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
        object-position: center top !important;
    }

    @media (max-width: 991px) {
        .install-app-modern-style {
            padding-top: 2.5rem;
            padding-bottom: 2.5rem;
        }

        .install-app-modern-style .content-col {
            padding: 2rem 1.5rem;
        }

        .install-app-modern-style .image-col {
            min-height: 280px;
        }

        .install-app-modern-style .box-app-img {
            position: relative;
            height: 280px;
        }
    }

    @media (max-width: 767px) {
        .install-app-modern-style .download-apps {
            gap: 0.65rem;
        }

        .install-app-modern-style .download-apps img {
            height: 2.9rem;
        }
    }

    @media (max-width: 575px) {
        .install-app-modern-style .content-col {
            padding: 1.5rem 1.1rem;
            gap: 0.8rem;
        }

        .install-app-modern-style .shortcode-title {
            font-size: 1.4rem !important;
        }

        .install-app-modern-style .description-p {
            font-size: 0.92rem !important;
        }

        .install-app-modern-style .download-apps img {
            height: 2.65rem;
        }

        .install-app-modern-style .image-col {
            min-height: 240px;
        }

        .install-app-modern-style .box-app-img {
            height: 240px;
        }
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="install-app-modern-style">
    <div class="container">
        <div class="main-card-container wow fadeInUp" data-wow-delay="0.1s">
            <div class="row g-0 align-items-stretch">
                <div class="col-lg-6 content-col">
                    <div class="install-app-eyebrow">{{ __('Install App') }}</div>

                    @if(!empty($title))
                        <h2 class="shortcode-title">{!! BaseHelper::clean($title) !!}</h2>
                    @endif

                    @if(!empty($buttonLabel))
                        <p class="description-p">{!! BaseHelper::clean($buttonLabel) !!}</p>
                    @elseif(!empty($appsDescription))
                        <p class="description-p">{!! BaseHelper::clean($appsDescription) !!}</p>
                    @endif

                    <div class="download-apps">
                        @if(!empty($androidAppImage))
                            <a href="{{ $androidAppUrl }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('Download on Android') }}">
                                {{ RvMedia::image($androidAppImage) }}
                            </a>
                        @endif

                        @if(!empty($iosAppImage))
                            <a href="{{ $iosAppUrl }}" target="_blank" rel="noopener noreferrer" aria-label="{{ __('Download on iOS') }}">
                                {{ RvMedia::image($iosAppImage) }}
                            </a>
                        @endif
                    </div>
                </div>

                <div class="col-lg-6 image-col">
                    @if(!empty($decorImage))
                        <div class="box-app-img">
                            {{ RvMedia::image($decorImage) }}
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</section>