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
    $contentClasses = 'ck-content page-content';

    if ($isAboutUsPage) {
        $contentClasses = 'ck-content page-content about-modern-content';
    } elseif ($isContactPage) {
        $contentClasses = 'ck-content page-content contact-modern-content';
    }
@endphp

<div @class([
    'page-modern',
    'page-modern--about' => $isAboutUsPage,
    'page-modern--contact' => $isContactPage,
])>
    @if ($isAboutUsPage)
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
