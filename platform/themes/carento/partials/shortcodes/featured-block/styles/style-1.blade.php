@php
    $buttonName = $shortcode->button_label;
    $title = $shortcode->title;
    $buttonUrl = $shortcode->button_url;
    $image1 = $shortcode->image_1;
    $image2 = $shortcode->image_2;
    $image3 = $shortcode->image_3;
    $image4 = $shortcode->image_4;
    $image5 = $shortcode->image_5;
@endphp

<style>
    /* =========================================
       1. LIGHT MODE (DEFAULT) STYLES
       ========================================= */
    /* Outer wrapper creating the 'Island' effect */
    .featured-zoom-style .zoom-outer-wrapper {
        background-color: transparent !important; /* Transparency reset, backround handled by page wrapper */
        border-radius: 20px !important;
        padding: 40px 0 !important;
        margin: 0 auto;
        max-width: 1240px;
        position: relative;
        overflow: hidden;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    /* Centered Header Styles */
    .featured-zoom-style .header-centered {
        text-align: center;
        margin-bottom: 50px;
    }

    .featured-zoom-style .subtitle-badge {
        display: inline-block;
        padding: 6px 16px;
        border-radius: 50px;
        font-weight: 700;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        margin-bottom: 20px;
        background-color: #B03A2E !important;
        color: #ffffff !important;
        transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Tick List Grid - Modern layout for bullet points */
    .featured-zoom-style .tick-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 20px;
        margin-bottom: 40px;
    }

    .featured-zoom-style .tick-grid li {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #ffffff;
        padding: 10px 20px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        font-weight: 600;
        list-style: none;
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        border: 1px solid transparent;
    }

    /* Image Gallery Grid */
    .featured-zoom-style .image-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 40px;
    }

    .featured-zoom-style .image-gallery img {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-radius: 16px;
        transition: transform 0.3s ease;
    }

    .featured-zoom-style .image-gallery img:hover {
        transform: translateY(-5px);
    }

    /* Decorative Overlay */
    .featured-zoom-style .bg-overlay-custom {
        position: absolute;
        bottom: 0;
        right: 0;
        height: 50%;
        width: 30%;
        background-color: var(--primary-color, #054232); 
        opacity: 0.1;
        z-index: 0;
        border-top-left-radius: 100%;
        transition: opacity 0.3s ease;
    }

    /* Smooth Text Transitions */
    .featured-zoom-style h2,
    .featured-zoom-style p.neutral-500 {
        transition: color 0.3s ease;
    }

    /* =========================================
       2. DARK MODE OVERRIDES
       ========================================= */
    /* Island Wrapper */
    [data-bs-theme="dark"] .featured-zoom-style .zoom-outer-wrapper,
    .dark .featured-zoom-style .zoom-outer-wrapper,
    .theme-dark .featured-zoom-style .zoom-outer-wrapper {
        background-color: #18191a !important; 
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    /* Text Colors */
    [data-bs-theme="dark"] .featured-zoom-style h2,
    .dark .featured-zoom-style h2,
    .theme-dark .featured-zoom-style h2 {
        color: #ffffff !important;
    }

    [data-bs-theme="dark"] .featured-zoom-style p.neutral-500,
    .dark .featured-zoom-style p.neutral-500,
    .theme-dark .featured-zoom-style p.neutral-500 {
        color: #adb5bd !important;
    }

    /* Subtitle Badge */
    [data-bs-theme="dark"] .featured-zoom-style .subtitle-badge,
    .dark .featured-zoom-style .subtitle-badge,
    .theme-dark .featured-zoom-style .subtitle-badge {
        background-color: #242526 !important; 
        color: #f8f9fa !important;
        border: 1px solid rgba(255,255,255,0.1);
    }

    /* Tick List Items */
    [data-bs-theme="dark"] .featured-zoom-style .tick-grid li,
    .dark .featured-zoom-style .tick-grid li,
    .theme-dark .featured-zoom-style .tick-grid li {
        background-color: #242526 !important; /* Dark mode card color */
        color: #f8f9fa !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3); /* Darker shadow */
    }

    /* Overlay adjustments for dark mode */
    [data-bs-theme="dark"] .featured-zoom-style .bg-overlay-custom,
    .dark .featured-zoom-style .bg-overlay-custom,
    .theme-dark .featured-zoom-style .bg-overlay-custom {
        opacity: 0.05; /* Reduce opacity of background blob in dark mode */
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-featured-block featured-zoom-style py-96 background-body">
    <div class="container">
        <div class="zoom-outer-wrapper shadow-sm">
            <div class="bg-overlay-custom"></div>

            <div class="position-relative z-1">
                <div class="header-centered">
                    @if ($subtitle = $shortcode->subtitle)
                        <span class="subtitle-badge bg-white text-dark wow fadeInDown">
                            {!! BaseHelper::clean($subtitle) !!}
                        </span>
                    @endif

                    @if($title = $shortcode->title)
                        <h2 class="mb-3 fw-bold wow fadeInUp">{!! BaseHelper::clean($title) !!}</h2>
                    @endif

                    @if ($description = $shortcode->description)
                        <p class="text-lg-medium neutral-500 mx-auto wow fadeInUp" style="max-width: 700px;">
                            {!! BaseHelper::clean($description) !!}
                        </p>
                    @endif
                </div>

                @if (count($tabs) > 0)
                    <ul class="tick-grid">
                        @foreach($tabs as $tab)
                            @continue(! $content = Arr::get($tab, 'content'))
                            <li class="wow fadeInUp" data-wow-delay="{{ $loop->index * 0.1 }}s">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor" class="text-success">
                                    <path d="M17 3.34a10 10 0 1 1 -14.995 8.984l-.005 -.324l.005 -.324a10 10 0 0 1 14.995 -8.336zm-1.293 5.953a1 1 0 0 0 -1.32 -.083l-.094 .083l-3.293 3.292l-1.293 -1.292l-.094 -.083a1 1 0 0 0 -1.403 1.403l.083 .094l2 2l.094 .083a1 1 0 0 0 1.226 0l.094 -.083l4 -4l.083 -.094a1 1 0 0 0 -.083 -1.32z" />
                                </svg>
                                {!! BaseHelper::clean($content) !!}
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if($buttonName && $buttonUrl)
                    <div class="text-center mb-5">
                        <a class="btn btn-primary px-5 py-3 wow fadeInUp" href="{{ $buttonUrl }}">
                            {!! BaseHelper::clean($buttonName) !!}
                            <svg class="ms-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 15L15 8L8 1M15 8L1 8" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </a>
                    </div>
                @endif
                
                <div class="image-gallery">
                    @if($image1 = $shortcode->image_1) {{ RvMedia::image($image1, $title) }} @endif
                    @if($image2 = $shortcode->image_2) {{ RvMedia::image($image2, $title) }} @endif
                    @if($image3 = $shortcode->image_3) {{ RvMedia::image($image3, $title) }} @endif
                    @if($image4 = $shortcode->image_4) {{ RvMedia::image($image4, $title) }} @endif
                    @if($image5 = $shortcode->image_5) {{ RvMedia::image($image5, $title) }} @endif
                </div>
            </div>
        </div>
    </div>
</section>