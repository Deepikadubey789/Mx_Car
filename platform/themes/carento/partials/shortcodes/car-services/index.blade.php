<style>
    /* =========================================
       1. LIGHT MODE (DEFAULT) STYLES
       ========================================= */
    /* The Wrapper: This creates the "Island" look */
    .services-zoom-style .zoom-outer-wrapper {
        background-color: #f8f9fa !important; 
        border-radius: 24px !important;      
        padding: 40px 50px !important;       
        margin: 0 auto;
        max-width: 1240px;                   
        transition: background-color 0.3s ease;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }

    /* Centered Title with Decorative Lines */
    .services-zoom-style .section-title-center {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 12px;
    }
    
    .services-zoom-style .section-title-center::before, 
    .services-zoom-style .section-title-center::after {
        content: '';
        height: 2px;
        width: 40px; /* Length of the side bars */
        background: #475569; /* Color of the bars */
        margin: 0 20px;
        border-radius: 2px;
    }

    /* The Title Badge */
    .services-zoom-style .zoom-badge {
        background-color: #054232; 
        color: #fff !important;
        padding: 2px 10px;
        border-radius: 4px;
        font-family: monospace;
        font-weight: 700;
        font-size: 1rem;
        display: inline-block;
        transition: background-color 0.3s ease;
        margin-bottom: 15px;
    }

    /* The Individual Cards (Now using Flexbox for equal heights) */
    .services-zoom-style .card-news {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 16px !important;
        padding: 0 !important;
        height: 100%;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
    }

    .services-zoom-style .card-news:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1 !important;
    }

    /* Image Styling */
    .services-zoom-style .card-image {
        flex-shrink: 0; /* Prevents image from shrinking */
    }
    .services-zoom-style .card-image img {
        height: 200px;
        width: 100%;
        object-fit: cover;
        display: block;
        border-radius: 0 !important; /* Reset border radius so it sits flush in the card */
        border-bottom: 1px solid #f1f5f9;
    }

    /* Info Area (Stretches to fill space) */
    .services-zoom-style .card-info {
        padding: 24px !important;
        display: flex;
        flex-direction: column;
        flex-grow: 1; /* This is the key: forces info area to stretch */
    }

    /* Typography */
    .services-zoom-style .card-title-flex {
        display: flex;
        justify-content: space-between;
        align-items: flex-start; /* Changed to flex-start in case names are long */
        margin-bottom: 12px;
        gap: 15px;
    }

    .services-zoom-style .service-name {
        font-size: 1.15rem;
        font-weight: 700;
        color: #111827;
        text-decoration: none;
        line-height: 1.3;
        transition: color 0.3s ease;
    }

    .services-zoom-style .service-description {
        font-size: 0.9rem;
        color: #475569;
        line-height: 1.6;
        margin-bottom: 0;
        transition: color 0.3s ease;
    }

    .services-zoom-style .zoom-arrow {
        color: #111827;
        transition: transform 0.2s ease, color 0.3s ease;
        flex-shrink: 0;
        margin-top: 2px;
    }

    .services-zoom-style .card-news:hover .zoom-arrow {
        transform: translate(3px, -3px);
        color: #0ea5e9; /* Adds a nice brand color touch on hover */
    }

    /* =========================================
       2. DARK MODE OVERRIDES
       ========================================= */
    [data-bs-theme="dark"] .services-zoom-style .zoom-outer-wrapper,
    .dark .services-zoom-style .zoom-outer-wrapper,
    .theme-dark .services-zoom-style .zoom-outer-wrapper {
        background-color: #1e293b !important; 
        border: 1px solid rgba(255, 255, 255, 0.05); 
        box-shadow: none;
    }

    /* Dark Mode Title Bars */
    [data-bs-theme="dark"] .services-zoom-style .section-title-center::before,
    [data-bs-theme="dark"] .services-zoom-style .section-title-center::after,
    .dark .services-zoom-style .section-title-center::before,
    .dark .services-zoom-style .section-title-center::after,
    .theme-dark .services-zoom-style .section-title-center::before,
    .theme-dark .services-zoom-style .section-title-center::after {
        background: #94a3b8 !important; /* Lighter bars for dark mode */
    }

    [data-bs-theme="dark"] .services-zoom-style .card-news,
    .dark .services-zoom-style .card-news,
    .theme-dark .services-zoom-style .card-news {
        background: #0f172a !important;
        border-color: #334155 !important;
    }

    [data-bs-theme="dark"] .services-zoom-style .card-image img,
    .dark .services-zoom-style .card-image img,
    .theme-dark .services-zoom-style .card-image img {
        border-color: #1e293b;
    }

    [data-bs-theme="dark"] .services-zoom-style .section-title-center,
    .dark .services-zoom-style .section-title-center,
    .theme-dark .services-zoom-style .section-title-center,
    [data-bs-theme="dark"] .services-zoom-style .service-name,
    .dark .services-zoom-style .service-name,
    .theme-dark .services-zoom-style .service-name,
    [data-bs-theme="dark"] .services-zoom-style .zoom-arrow,
    .dark .services-zoom-style .zoom-arrow,
    .theme-dark .services-zoom-style .zoom-arrow {
        color: #f8fafc !important; 
    }

    [data-bs-theme="dark"] .services-zoom-style .service-description,
    .dark .services-zoom-style .service-description,
    .theme-dark .services-zoom-style .service-description {
        color: #94a3b8 !important; 
    }

    [data-bs-theme="dark"] .services-zoom-style .zoom-badge,
    .dark .services-zoom-style .zoom-badge,
    .theme-dark .services-zoom-style .zoom-badge {
        background-color: #0f6c51 !important; 
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-car-services services-zoom-style py-5 background-body">
    <div class="container">
        <div class="zoom-outer-wrapper wow fadeInUp" data-wow-delay="0.1s">
            
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-10">
                    {{-- NEW CENTERED TITLE WITH BARS --}}
                    <h2 class="section-title-center">
                        {!! BaseHelper::clean($shortcode->title) !!}
                    </h2>
                    
                    {{-- MOVED BADGE BELOW TITLE --}}
                    @if($subtitle = $shortcode->subtitle)
                         <div><span class="zoom-badge">{{ $subtitle }}</span></div>
                    @endif

                    @if ($description = $shortcode->description)
                        <p class="neutral-500 mt-2" style="font-size: 1.1rem;">{!! BaseHelper::clean($description) !!}</p>
                    @endif
                </div>
            </div>

            <div class="row align-items-stretch g-4">
                @foreach($services as $service)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card-news wow fadeIn" data-wow-delay="{{ $loop->index * 0.1 }}s">
                            
                            <div class="card-image">
                                <a href="{{ $service->url }}">
                                    {!! RvMedia::image($service->image, $service->name, 'medium-rectangle') !!}
                                </a>
                            </div>

                            <div class="card-info">
                                <div class="card-title-flex">
                                    <a class="service-name" href="{{ $service->url }}">{{ $service->name }}</a>
                                    <a href="{{ $service->url }}" class="zoom-arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
                                    </a>
                                </div>

                                @if ($desc = $service->description)
                                    <p class="service-description">
                                        {!! BaseHelper::clean($desc) !!}
                                    </p>
                                @endif
                            </div>
                            
                        </div>
                    </div>
                @endforeach
            </div>
            
        </div>
    </div>
</section>