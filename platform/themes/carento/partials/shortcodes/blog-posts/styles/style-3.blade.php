@use(Theme\Carento\Support\ThemeHelper)
@php
    // Make sure we are not chunking the posts so Swiper can slide them individually
    $displayPosts = $posts instanceof \Illuminate\Support\Collection ? $posts->flatten() : $posts;
    $linkUrl = $shortcode->link_url;
    $linkLabel = $shortcode->link_label ?: 'DISCOVER MORE';
@endphp

{{-- FRONTEND CUSTOM CSS SCOPED ONLY TO THE BLOG SECTION --}}
<style>
    /* =========================================
       1. MAIN CONTAINER (BENTO STYLE)
       ========================================= */
    .shortcode-blog-posts {
        background-color: transparent !important;
        padding-top: 40px;
        padding-bottom: 40px;
    }

    .blog-bento-container {
        background-color: #f8f9fa; /* Off-white container matching screenshot */
        border-radius: 24px;
        padding: 50px 60px;
        max-width: 1200px;
        margin: 0 auto;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }

    /* =========================================
       2. TITLE STYLING (WITH BARS)
       ========================================= */
    .section-title-center {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 40px;
        text-align: center;
    }
    .section-title-center::before, 
    .section-title-center::after {
        content: '';
        height: 2px;
        width: 40px;
        background: #475569; 
        margin: 0 20px;
        border-radius: 2px;
    }

    /* =========================================
       3. BLOG CARD STYLING (SCREENSHOT REPLICA)
       ========================================= */
    .blog-screenshot-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        background: transparent;
        transition: transform 0.3s ease;
    }
    .blog-screenshot-card:hover {
        transform: translateY(-5px);
    }

    /* Image Wrapper with Overlap Fix */
    .blog-img-container {
        position: relative;
        margin-bottom: 25px; /* Space for the overlapping button and title */
    }
    
    .blog-img-wrapper {
        width: 100%;
        aspect-ratio: 16 / 10;
        border-radius: 16px;
        overflow: hidden;
        background: #e2e8f0;
    }
    
    .blog-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .blog-screenshot-card:hover .blog-img-wrapper img {
        transform: scale(1.05);
    }

    /* Floating Arrow Badge (Bottom Right) */
    .blog-arrow-badge {
        position: absolute;
        bottom: -20px;
        right: 20px;
        width: 50px;
        height: 50px;
        background-color: #e2e8f0; /* Light gray inner circle */
        border: 5px solid #f8f9fa; /* Matches container bg to create a 'cutout' effect */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary-color, #10b981); /* Uses Theme Primary Color */
        z-index: 10;
        transition: all 0.3s ease;
    }
    .blog-screenshot-card:hover .blog-arrow-badge {
        background-color: var(--primary-color, #10b981); /* Background becomes Theme Color on hover */
        color: #ffffff; /* Icon becomes white */
    }

    /* Text Content */
    .blog-card-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 10px;
        line-height: 1.4;
        text-decoration: none;
        display: block;
        transition: color 0.2s ease;
    }
    .blog-card-title:hover {
        color: var(--primary-color, #10b981); /* Title also uses Theme Color on hover */
    }

    .blog-card-desc {
        font-size: 0.9rem;
        color: #64748b;
        line-height: 1.6;
        margin: 0;
        display: -webkit-box;
        -webkit-line-clamp: 2; /* Truncates to exactly 2 lines like screenshot */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* =========================================
       4. SLIDER NAVIGATION & PAGINATION
       ========================================= */
    .slider-nav-wrapper {
        position: relative;
    }

    /* Minimalist Floating Arrows */
    .swiper-button-prev.custom-arrow::after,
    .swiper-button-next.custom-arrow::after { display: none !important; }

    .swiper-button-prev.custom-arrow,
    .swiper-button-next.custom-arrow {
        background: transparent !important;
        border: none !important;
        width: 40px !important;
        height: 40px !important;
        color: #111827 !important;
        top: 40% !important; /* Raised slightly due to text at bottom */
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: transform 0.2s ease !important;
        z-index: 20 !important;
    }
    .swiper-button-prev.custom-arrow { left: -45px !important; transform: translateY(-50%) !important; }
    .swiper-button-next.custom-arrow { right: -45px !important; transform: translateY(-50%) !important; }

    .swiper-button-prev.custom-arrow:hover,
    .swiper-button-next.custom-arrow:hover {
        transform: translateY(-50%) scale(1.2) !important;
    }

    /* Pagination Dots */
    .swiper-pagination-custom {
        position: relative !important;
        margin-top: 20px;
        margin-bottom: 25px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
    }
    .swiper-pagination-custom .swiper-pagination-bullet {
        background: #cbd5e1 !important;
        opacity: 1 !important;
        width: 8px !important;
        height: 8px !important;
        margin: 0 !important; 
        border-radius: 50% !important;
        transition: all 0.3s ease;
    }
    .swiper-pagination-custom .swiper-pagination-bullet-active {
        background: var(--primary-color, #10b981) !important; /* Uses Theme Color for active dot */
        width: 20px !important; 
        border-radius: 8px !important;
    }

    /* =========================================
       5. BOTTOM BUTTON
       ========================================= */
    .btn-theme-pill {
        border-radius: 50px !important; /* Perfect pill shape */
        padding: 14px 40px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        font-size: 0.85rem !important;
        transition: all 0.3s ease !important;
        display: inline-block;
    }
    .btn-theme-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15) !important;
    }

    /* =========================================
       6. DARK MODE OVERRIDES
       ========================================= */
    html[data-bs-theme="dark"] .blog-bento-container { background-color: #1e293b; box-shadow: none; border: 1px solid rgba(255,255,255,0.05); }
    html[data-bs-theme="dark"] .section-title-center { color: #fff; }
    html[data-bs-theme="dark"] .section-title-center::before,
    html[data-bs-theme="dark"] .section-title-center::after { background: #94a3b8; }
    html[data-bs-theme="dark"] .swiper-button-prev.custom-arrow,
    html[data-bs-theme="dark"] .swiper-button-next.custom-arrow { color: #f8fafc !important; }
    html[data-bs-theme="dark"] .blog-arrow-badge { background-color: #334155; border-color: #1e293b; }
    html[data-bs-theme="dark"] .blog-card-title { color: #f8fafc; }
    html[data-bs-theme="dark"] .blog-card-desc { color: #94a3b8; }

    /* =========================================
       7. MOBILE RESPONSIVENESS
       ========================================= */
    @media (max-width: 991px) {
        .blog-bento-container { padding: 40px 30px; }
        .swiper-button-prev.custom-arrow { left: -10px !important; }
        .swiper-button-next.custom-arrow { right: -10px !important; }
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-blog-posts section-box py-5">
    <div class="container">
        
        <div class="blog-bento-container wow fadeInUp" data-wow-delay="0.1s">
            
            <div class="row">
                <div class="col-12">
                    @if($shortcode->title)
                        <h2 class="section-title-center">{!! BaseHelper::clean($shortcode->title) !!}</h2>
                    @endif
                </div>
            </div>

            @if($displayPosts->isNotEmpty())
                <div class="slider-nav-wrapper">
                    
                    <div class="swiper-container blog-swiper-container pt-2 pb-2">
                        <div class="swiper-wrapper">
                            
                            @foreach($displayPosts as $post)
                                <div class="swiper-slide h-auto">
                                    <div class="blog-screenshot-card">
                                        
                                        <div class="blog-img-container">
                                            <div class="blog-img-wrapper">
                                                <a href="{{ $post->url }}">
                                                    {{ RvMedia::image($post->image, $post->name, 'medium-rectangle') }}
                                                </a>
                                            </div>
                                            
                                            <a href="{{ $post->url }}" class="blog-arrow-badge">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                                    <line x1="7" y1="17" x2="17" y2="7"></line>
                                                    <polyline points="7 7 17 7 17 17"></polyline>
                                                </svg>
                                            </a>
                                        </div>

                                        <div class="blog-text-container">
                                            <a href="{{ $post->url }}" class="blog-card-title text-truncate">
                                                {!! BaseHelper::clean($post->name) !!}
                                            </a>
                                            
                                            @if ($post->description)
                                                <p class="blog-card-desc">
                                                    {!! BaseHelper::clean($post->description) !!}
                                                </p>
                                            @endif
                                        </div>
                                        
                                    </div>
                                </div>
                            @endforeach
                            
                        </div>
                    </div>
                    
                    <div class="swiper-button-prev custom-arrow blog-arrow-prev">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
                    </div>
                    <div class="swiper-button-next custom-arrow blog-arrow-next">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
                    </div>
                    
                </div>
                
                <div class="swiper-pagination blog-pagination swiper-pagination-custom text-center"></div>
            @endif
            
            @if($linkUrl)
                <div class="text-center mt-2">
                    <a class="btn btn-primary btn-theme-pill" href="{{ $linkUrl }}">
                        {!! BaseHelper::clean($linkLabel) !!}
                    </a>
                </div>
            @endif

        </div>
        
    </div>
</section>

{{-- SLIDER INITIALIZATION SCRIPT --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swiper !== 'undefined') {
            new Swiper('.shortcode-blog-posts .blog-swiper-container', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: false,
                watchOverflow: true,
                navigation: {
                    nextEl: '.shortcode-blog-posts .blog-arrow-next',
                    prevEl: '.shortcode-blog-posts .blog-arrow-prev',
                },
                pagination: {
                    el: '.shortcode-blog-posts .blog-pagination',
                    clickable: true,
                },
                breakpoints: {
                    768: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 }
                }
            });
        }
    });
</script>