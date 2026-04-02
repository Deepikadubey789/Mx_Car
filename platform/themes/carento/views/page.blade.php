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
        $contentClasses = 'ck-content page-content';
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

@if ($isAboutUsPage)
    <style>
        .page-modern--about,
        .page-modern--about *,
        .page-modern--about section,
        .page-modern--about .section-1,
        .page-modern--about .background-body,
        .page-modern--about .shortcode-faqs,
        .page-modern--about .mxcar-faq-clean-section,
        .page-modern--about .about-modern-content,
        .page-modern--about .about-modern-content > section,
        .page-modern--about .shortcode-about-us-information {
            background: none !important;
            background-color: transparent !important;
            background-image: none !important;
            border: none !important;
            box-shadow: none !important;
        }

        .page-modern--about *::before,
        .page-modern--about *::after,
        .page-modern--about ::before,
        .page-modern--about ::after {
            background: none !important;
            background-image: none !important;
            display: none !important;
        }

        /* Keep actual images tag but remove background patterns */
        .page-modern--about img {
            display: block !important;
        }
    </style>
@endif

@if ($isLegalPage)
    <style>
        /* MXCar Ultra-Clean Legal / Terms of Use Document Styling */
        body, .page-modern--legal, .background-body {
            background-color: var(--bs-background-body) !important;
        }
        
        .legal-modern-content {
            background-color: var(--bs-color-white);
            border: 1px solid var(--bs-neutral-200);
            border-radius: 20px;
            box-shadow: 0 16px 48px rgba(var(--bs-color-1000), 0.08);
            max-width: 900px;
            margin: 60px auto;
            padding: 60px !important;
            font-family: inherit;
        }

        /* Typography & Headings */
        .legal-modern-content h1, 
        .legal-modern-content h2, 
        .legal-modern-content h3 {
            color: var(--bs-neutral-1000);
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
            background-color: var(--primary-color);
            border-radius: 4px;
        }

        .legal-modern-content p {
            color: var(--bs-neutral-500);
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
            color: var(--bs-neutral-500);
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
            background-color: var(--primary-color);
            border-radius: 50%;
        }

        /* Dividers */
        .legal-modern-content hr {
            border-color: var(--bs-neutral-200);
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
            background-color: var(--bs-neutral-900);
            border-color: var(--bs-neutral-700);
            box-shadow: 0 10px 40px rgba(var(--bs-color-1000), 0.2);
        }
        [data-bs-theme="dark"] .legal-modern-content h1, 
        [data-bs-theme="dark"] .legal-modern-content h2, 
        [data-bs-theme="dark"] .legal-modern-content h3 {
            color: var(--bs-neutral-100);
        }
        [data-bs-theme="dark"] .legal-modern-content p,
        [data-bs-theme="dark"] .legal-modern-content ul li {
            color: var(--bs-neutral-300);
        }
        [data-bs-theme="dark"] .legal-modern-content hr {
            border-color: var(--bs-neutral-700);
        }
    </style>
@endif



    {!! apply_filters(PAGE_FILTER_FRONT_PAGE_CONTENT, Html::tag('div', BaseHelper::clean($page->content), ['class' => $contentClasses])->toHtml(), $page) !!}
</div>
