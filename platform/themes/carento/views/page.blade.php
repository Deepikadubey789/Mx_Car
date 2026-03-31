@php
    Theme::set('pageTitle', $page->name);
    Theme::set('pageDescription', $page->description);
    Theme::set('isHomepage', BaseHelper::isHomepage($page->getKey()));
    Theme::set('breadcrumb_background_image', $page->getMetaData('breadcrumb_background_image', true));
    Theme::set('breadcrumb_simple', $page->getMetaData('breadcrumb_simple', true));
    Theme::set('breadcrumb_background_color', $page->getMetaData('breadcrumb_background_color', true));
    Theme::set('breadcrumb_text_color', $page->getMetaData('breadcrumb_text_color', true));
    Theme::set('breadcrumb_description', $page->getMetaData('breadcrumb_display_last_update', true) ? Theme::formatDate($page->updated_at) : $page->description);

    $pageSegments = request()->segments();
    $currentPageSlug = $pageSegments ? (string) end($pageSegments) : '';
    $isAboutUsPage = $currentPageSlug === 'about-us';
    $isContactPage = $currentPageSlug === 'contact';
    $isLegalPage = in_array($currentPageSlug, ['terms-of-use', 'privacy-policy', 'cookie-policy']);
    $contentClasses = 'ck-content page-content';

    if ($isAboutUsPage) {
        $contentClasses = 'ck-content page-content about-modern-content';
    } elseif ($isContactPage) {
        $contentClasses = 'ck-content page-content contact-modern-content';
    } elseif ($isLegalPage) {
        $contentClasses = 'ck-content page-content legal-modern-content';
    }
@endphp

<div @class([
    'page-modern',
    'page-modern--about' => $isAboutUsPage,
    'page-modern--contact' => $isContactPage,
    'page-modern--legal' => $isLegalPage,
])>

@if ($isLegalPage)
    <style>
        /* MXCar Ultra-Clean Legal / Terms of Use Document Styling */
        body, .page-modern--legal, .background-body {
            background-color: #F4F5F7 !important; /* Cool grey background ensures the white document strongly pops */
        }
        
        .legal-modern-content {
            background-color: #ffffff;
            border: 1px solid #E9ECEF;
            border-radius: 20px;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.08); /* Increased shadow for strong floating effect */
            max-width: 900px;
            margin: 60px auto;
            padding: 60px !important;
            font-family: inherit;
        }

        /* Typography & Headings */
        .legal-modern-content h1, 
        .legal-modern-content h2, 
        .legal-modern-content h3 {
            color: #111111;
            font-weight: 700;
            margin-top: 2.5rem;
            margin-bottom: 1.5rem;
            position: relative;
        }

        /* Add MXCar Red Accents to Headings */
        .legal-modern-content h3 {
            padding-left: 16px;
            font-size: 1.5rem;
        }
        .legal-modern-content h3::before {
            content: '';
            position: absolute;
            left: 0;
            top: 5px;
            bottom: 5px;
            width: 4px;
            background-color: #B03A2E;
            border-radius: 4px;
        }

        .legal-modern-content p {
            color: #495057;
            line-height: 1.8;
            font-size: 1.05rem;
            margin-bottom: 1.5rem;
        }

        /* Lists */
        .legal-modern-content ul {
            list-style: none;
            padding-left: 0;
            margin-bottom: 2rem;
        }
        .legal-modern-content ul li {
            position: relative;
            padding-left: 28px;
            color: #495057;
            margin-bottom: 12px;
            line-height: 1.7;
            font-size: 1.05rem;
        }
        .legal-modern-content ul li::before {
            content: '';
            position: absolute;
            left: 0;
            top: 10px;
            width: 8px;
            height: 8px;
            background-color: #B03A2E;
            border-radius: 50%;
        }

        /* Dividers */
        .legal-modern-content hr {
            border-color: #E9ECEF;
            margin: 3rem 0;
            opacity: 1;
        }

        @media (max-width: 768px) {
            .legal-modern-content {
                margin: 30px 15px;
                padding: 30px !important;
            }
        }

        /* Dark Mode Support */
        [data-bs-theme="dark"] body, 
        [data-bs-theme="dark"] .page-modern--legal, 
        [data-bs-theme="dark"] .background-body {
            background-color: var(--bs-body-bg) !important;
        }
        [data-bs-theme="dark"] .legal-modern-content {
            background-color: #1a1a1a;
            border-color: #2e2e2e;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }
        [data-bs-theme="dark"] .legal-modern-content h1, 
        [data-bs-theme="dark"] .legal-modern-content h2, 
        [data-bs-theme="dark"] .legal-modern-content h3 {
            color: #f1f1f1;
        }
        [data-bs-theme="dark"] .legal-modern-content p,
        [data-bs-theme="dark"] .legal-modern-content ul li {
            color: #adb5bd;
        }
        [data-bs-theme="dark"] .legal-modern-content hr {
            border-color: #2e2e2e;
        }
    </style>
@endif


    @if ($isAboutUsPage)
        <section class="about-modern-hero box-section background-body">
            <div class="container">
                <div class="about-modern-hero__shell">
                    <div class="about-modern-hero__content">
                        <p class="about-modern-hero__eyebrow text-xs-medium mb-0 mxcar-page-desc">{{ __('Built for Every Journey') }}</p>
                        <h1 class="about-modern-hero__title mxcar-page-title">{{ $page->name }}</h1>

                        @if ($page->description)
                            <p class="about-modern-hero__description mxcar-page-desc">{!! BaseHelper::clean($page->description) !!}</p>
                        @endif

                        <ul class="about-modern-hero__points" aria-label="{{ __('Why choose us') }}">
                            <li>{{ __('Real-time availability across premium and everyday vehicle classes.') }}</li>
                            <li>{{ __('Fast pickup workflows designed for airport, city, and weekend trips.') }}</li>
                            <li>{{ __('Friendly support team available every day, including urgent requests.') }}</li>
                        </ul>

                        <div class="about-modern-hero__actions">
                            <a class="mxcar-btn-red" href="{{ url('/cars') }}">{{ __('Browse Cars') }}</a>
                            <a class="mxcar-btn-grey" href="{{ url('/contact') }}">{{ __('Talk to Our Team') }}</a>
                        </div>
                    </div>

                    <div class="about-modern-hero__stats" aria-label="{{ __('Company highlights') }}">
                        <div class="about-modern-hero__stat-card">
                            <p class="about-modern-hero__stat-value">25+</p>
                            <p class="about-modern-hero__stat-label">{{ __('Years in service') }}</p>
                        </div>

                        <div class="about-modern-hero__stat-card">
                            <p class="about-modern-hero__stat-value">120K+</p>
                            <p class="about-modern-hero__stat-label">{{ __('Happy trips completed') }}</p>
                        </div>

                        <div class="about-modern-hero__stat-card">
                            <p class="about-modern-hero__stat-value">4.9/5</p>
                            <p class="about-modern-hero__stat-label">{{ __('Customer satisfaction') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="about-modern-ribbon" aria-label="{{ __('About highlights') }}">
            <div class="container">
                <div class="about-modern-ribbon__shell">
                    <article class="about-modern-ribbon__item">
                        <p class="about-modern-ribbon__value">45+</p>
                        <p class="about-modern-ribbon__label">{{ __('Global branches') }}</p>
                    </article>
                    <article class="about-modern-ribbon__item">
                        <p class="about-modern-ribbon__value">24/7</p>
                        <p class="about-modern-ribbon__label">{{ __('Human support') }}</p>
                    </article>
                    <article class="about-modern-ribbon__item">
                        <p class="about-modern-ribbon__value">15m</p>
                        <p class="about-modern-ribbon__label">{{ __('Average response time') }}</p>
                    </article>
                    <article class="about-modern-ribbon__item">
                        <p class="about-modern-ribbon__value">4.9/5</p>
                        <p class="about-modern-ribbon__label">{{ __('Verified satisfaction') }}</p>
                    </article>
                </div>
            </div>
        </section>
    @elseif ($isContactPage)
        <section class="contact-modern-hero box-section background-body">
            <div class="container">
                <div class="contact-modern-hero__shell">
                    <div class="contact-modern-hero__content">
                        <p class="contact-modern-hero__eyebrow text-xs-medium mb-0">{{ __('Contact & Support') }}</p>
                        <h1 class="contact-modern-hero__title">{{ $page->name }}</h1>

                        @if ($page->description)
                            <p class="contact-modern-hero__description">{!! BaseHelper::clean($page->description) !!}</p>
                        @else
                            <p class="contact-modern-hero__description">{{ __('Tell us what you need and our team will route your request to the right specialist quickly.') }}</p>
                        @endif

                        <div class="contact-modern-hero__actions">
                            <a class="btn btn-primary" href="{{ url('/cars') }}">{{ __('Browse Available Cars') }}</a>
                            <a class="btn btn-outline-secondary" href="{{ url('/about-us') }}">{{ __('Learn About MXCar') }}</a>
                        </div>
                    </div>

                    <div class="contact-modern-hero__quick" aria-label="{{ __('Support highlights') }}">
                        <article class="contact-modern-hero__quick-card">
                            <p class="contact-modern-hero__quick-value">15m</p>
                            <p class="contact-modern-hero__quick-label">{{ __('Average response') }}</p>
                        </article>
                        <article class="contact-modern-hero__quick-card">
                            <p class="contact-modern-hero__quick-value">24/7</p>
                            <p class="contact-modern-hero__quick-label">{{ __('Customer support') }}</p>
                        </article>
                        <article class="contact-modern-hero__quick-card">
                            <p class="contact-modern-hero__quick-value">45+</p>
                            <p class="contact-modern-hero__quick-label">{{ __('Global branches') }}</p>
                        </article>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, Html::tag('div', BaseHelper::clean($page->content), ['class' => $contentClasses])->toHtml(), $page) !!}

    @if ($isAboutUsPage)
        <style>
            /* MXCar ULTIMATE OVERRIDES — Force Off-White & spacing across all About Us shortcodes */
            .page-modern--about {
                --bs-background-body: #F4F6F8 !important;
                --bs-background-body-2: #F4F6F8 !important;
                --bs-background-1: #F4F6F8 !important;
                --bs-background-2: #F4F6F8 !important;
                --bs-background-white: #ffffff !important;
                background-color: #F4F6F8 !important;
            }

            /* Clear all legacy backgrounds from major containers and nested boxes */
            .page-modern--about section,
            .page-modern--about .section-1,
            .page-modern--about .shortcode-why-us,
            .page-modern--about .section-team-1,
            .page-modern--about .shortcode-featured-block,
            .page-modern--about .shortcode-site-statistics,
            .page-modern--about .shortcode-intro-video,
            .page-modern--about .shortcode-testimonial,
            .page-modern--about .shortcode-blog-posts,
            .page-modern--about .box-cta-6,
            .page-modern--about .box-cta-1,
            .page-modern--about .box-why-book-22,
            .page-modern--about .section-cta-7,
            .page-modern--about .section-static-1,
            .page-modern--about .section-box,
            .page-modern--about .bg-overlay,
            .page-modern--about .background-body,
            .page-modern--about .background-100 {
                background-color: #F4F6F8 !important;
                background-image: none !important;
                border: none !important;
                box-shadow: none !important;
            }

            /* Section Spacing — Standardized 100px Gaps */
            .page-modern--about .about-modern-content > section,
            .page-modern--about section[data-block-id] {
                padding-top: 100px !important;
                padding-bottom: 100px !important;
                margin-top: 0 !important;
                margin-bottom: 100px !important; /* User requested final polish spacing */
            }

            /* Content Cards — Stand out in Pure White */
            .page-modern--about .card,
            .page-modern--about .box-image,
            .page-modern--about .card-why,
            .page-modern--about .card-team,
            .page-modern--about .card-news,
            .page-modern--about .card-testimonial,
            .page-modern--about .about-modern-hero__stat-card,
            .page-modern--about .about-modern-ribbon__item {
                background-color: #ffffff !important;
                border: 1px solid #E9ECEF !important;
                border-radius: 20px !important;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03) !important;
            }

            /* Brand Eyebrow Standardization (Pill Style) */
            .page-modern--about .section-subtitle,
            .page-modern--about .shortcode-subtitle,
            .page-modern--about .subtitle-badge,
            .page-modern--about .btn-signin,
            .page-modern--about .badge-green,
            .page-modern--about [data-block-id] span[class*="subtitle"],
            .page-modern--about [data-block-id] .btn-tag {
                background-color: #B03A2E !important;
                color: #ffffff !important;
                border: none !important;
                border-radius: 50px !important;
                padding: 6px 16px !important;
                font-size: 0.72rem !important;
                text-transform: uppercase !important;
                font-weight: 700 !important;
                letter-spacing: 1.2px !important;
                display: inline-block !important;
                margin-bottom: 16px !important;
            }

            /* Site Statistics Override */
            .shortcode-site-statistics {
                --background-color: transparent !important;
            }

            /* Fix Hero Background */
            .about-modern-hero {
                background-color: #ffffff !important; /* Hero usually looks better on pure white */
            }

            /* Dark Mode Consistency */
            [data-bs-theme="dark"] .page-modern--about,
            [data-bs-theme="dark"] .page-modern--about section {
                background-color: #121212 !important;
            }
            [data-bs-theme="dark"] .page-modern--about .card,
            [data-bs-theme="dark"] .page-modern--about .card-news {
                background-color: #1e1e1e !important;
                border-color: #2e2e2e !important;
            }
        </style>
    @endif
</div>
