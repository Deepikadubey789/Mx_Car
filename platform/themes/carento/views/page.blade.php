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
        <style>
            /* MXCar Ultra-Clean About Us Overrides */
            .page-modern--about {
                background-color: #ffffff !important;
            }
            .page-modern--about section,
            .page-modern--about .background-body,
            .page-modern--about .background-2,
            .page-modern--about .background-brand-2,
            .page-modern--about .background-100,
            .page-modern--about .bg-light {
                background-color: #ffffff !important; /* Pure white background to eliminate muddy beige */
            }
            .page-modern--about .card,
            .page-modern--about .box-image,
            .page-modern--about .box-tag,
            .page-modern--about .card-why,
            .page-modern--about .card-team,
            .page-modern--about .about-modern-ribbon__item,
            .page-modern--about .about-modern-hero__stat-card,
            .page-modern--about .card-testimonial {
                background-color: #ffffff !important;
                border: 1px solid #E9ECEF !important;
                border-radius: 20px !important;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02) !important;
                transition: all 0.3s ease;
            }
            .page-modern--about .card:hover,
            .page-modern--about .box-image:hover,
            .page-modern--about .box-tag:hover,
            .page-modern--about .card-why:hover,
            .page-modern--about .card-team:hover,
            .page-modern--about .about-modern-ribbon__item:hover {
                border-color: #B03A2E !important;
                box-shadow: 0 8px 30px rgba(176, 58, 46, 0.05) !important;
                transform: translateY(-2px);
            }
            .page-modern--about .heading-1,
            .page-modern--about .heading-2,
            .page-modern--about .heading-3,
            .page-modern--about h1, 
            .page-modern--about h2, 
            .page-modern--about h3,
            .page-modern--about .text-dark {
                color: #111111 !important;
                font-weight: 700 !important;
            }
            
            /* Buttons Fix */
            .page-modern--about .btn-primary {
                background-color: #B03A2E !important;
                border-color: #B03A2E !important;
                color: #ffffff !important;
            }
            .page-modern--about .btn-primary:hover {
                background-color: #8E2B21 !important;
                box-shadow: 0 6px 20px rgba(176, 58, 46, 0.25) !important;
            }

            /* Fix icon backgrounds inside cards */
            .page-modern--about .card-why .card-image,
            .page-modern--about .card-contact .card-icon {
                background-color: #f4f5f7 !important;
                border-radius: 50% !important;
                padding: 12px !important;
                border: none !important;
            }

            /* Dark Mode Support for About Page */
            [data-bs-theme="dark"] .page-modern--about {
                background-color: transparent !important;
            }
            [data-bs-theme="dark"] .page-modern--about section,
            [data-bs-theme="dark"] .page-modern--about .background-body,
            [data-bs-theme="dark"] .page-modern--about .background-2,
            [data-bs-theme="dark"] .page-modern--about .background-brand-2,
            [data-bs-theme="dark"] .page-modern--about .background-100 {
                background-color: transparent !important;
            }
            [data-bs-theme="dark"] .page-modern--about .card,
            [data-bs-theme="dark"] .page-modern--about .box-image,
            [data-bs-theme="dark"] .page-modern--about .box-tag,
            [data-bs-theme="dark"] .page-modern--about .card-why,
            [data-bs-theme="dark"] .page-modern--about .card-team,
            [data-bs-theme="dark"] .page-modern--about .about-modern-ribbon__item,
            [data-bs-theme="dark"] .page-modern--about .about-modern-hero__stat-card {
                background-color: #1a1a1a !important;
                border-color: #2e2e2e !important;
            }
            [data-bs-theme="dark"] .page-modern--about .card:hover,
            [data-bs-theme="dark"] .page-modern--about .box-image:hover,
            [data-bs-theme="dark"] .page-modern--about .box-tag:hover,
            [data-bs-theme="dark"] .page-modern--about .card-why:hover,
            [data-bs-theme="dark"] .page-modern--about .card-team:hover,
            [data-bs-theme="dark"] .page-modern--about .about-modern-ribbon__item:hover {
                border-color: #B03A2E !important;
            }
            [data-bs-theme="dark"] .page-modern--about .heading-1,
            [data-bs-theme="dark"] .page-modern--about .heading-2,
            [data-bs-theme="dark"] .page-modern--about .heading-3,
            [data-bs-theme="dark"] .page-modern--about h1, 
            [data-bs-theme="dark"] .page-modern--about h2, 
            [data-bs-theme="dark"] .page-modern--about h3,
            [data-bs-theme="dark"] .page-modern--about .text-dark,
            [data-bs-theme="dark"] .page-modern--about .neutral-1000,
            [data-bs-theme="dark"] .page-modern--about .text-xl-bold,
            [data-bs-theme="dark"] .page-modern--about .text-md-bold {
                color: #f1f1f1 !important;
            }
            [data-bs-theme="dark"] .page-modern--about p.neutral-500,
            [data-bs-theme="dark"] .page-modern--about .text-md-medium {
                color: #adb5bd !important;
            }
            [data-bs-theme="dark"] .page-modern--about .card-why .card-image,
            [data-bs-theme="dark"] .page-modern--about .card-contact .card-icon {
                background-color: #2e2e2e !important;
            }
        </style>
        <section class="about-modern-hero box-section background-body">
            <div class="container">
                <div class="about-modern-hero__shell">
                    <div class="about-modern-hero__content">
                        <p class="about-modern-hero__eyebrow text-xs-medium mb-0">{{ __('Built for Every Journey') }}</p>
                        <h1 class="about-modern-hero__title">{{ $page->name }}</h1>

                        @if ($page->description)
                            <p class="about-modern-hero__description">{!! BaseHelper::clean($page->description) !!}</p>
                        @endif

                        <ul class="about-modern-hero__points" aria-label="{{ __('Why choose us') }}">
                            <li>{{ __('Real-time availability across premium and everyday vehicle classes.') }}</li>
                            <li>{{ __('Fast pickup workflows designed for airport, city, and weekend trips.') }}</li>
                            <li>{{ __('Friendly support team available every day, including urgent requests.') }}</li>
                        </ul>

                        <div class="about-modern-hero__actions">
                            <a class="btn btn-primary" href="{{ url('/cars') }}">{{ __('Browse Cars') }}</a>
                            <a class="btn btn-outline-secondary" href="{{ url('/contact') }}">{{ __('Talk to Our Team') }}</a>
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
</div>
