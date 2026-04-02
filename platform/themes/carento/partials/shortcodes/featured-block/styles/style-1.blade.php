@php
    $buttonName = $shortcode->button_label;
    $title = $shortcode->title;
    $buttonUrl = $shortcode->button_url;
    
    // Map the inputs to a clean array for the 4 steps
    $steps = [
        [
            'image' => $shortcode->image_1,
            // Fallback text if tabs aren't filled out, and strips out any old HTML/SVGs from the previous design
            'text'  => isset($tabs[0]) ? strip_tags(BaseHelper::clean(Arr::get($tabs[0], 'content'))) : 'Download app/ Visit Website',
            // Thumbs Up Icon
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3zM7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"></path></svg>'
        ],
        [
            'image' => $shortcode->image_2,
            'text'  => isset($tabs[1]) ? strip_tags(BaseHelper::clean(Arr::get($tabs[1], 'content'))) : 'Search for desired Car and book',
            // Dollar Sign Icon
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>'
        ],
        [
            'image' => $shortcode->image_3,
            'text'  => isset($tabs[2]) ? strip_tags(BaseHelper::clean(Arr::get($tabs[2], 'content'))) : 'Verify your profile',
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>'
        ],
        [
            'image' => $shortcode->image_4,
            'text'  => isset($tabs[3]) ? strip_tags(BaseHelper::clean(Arr::get($tabs[3], 'content'))) : 'Get ready for your trip',
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="8" rx="2" ry="2"></rect><circle cx="7" cy="19" r="2"></circle><circle cx="17" cy="19" r="2"></circle><path d="M3 11l2-5h14l2 5"></path></svg>'
        ],
    ];
@endphp

<style>
    /* =========================================
       1. MAIN CONTAINER (BENTO STYLE)
       ========================================= */
    .shortcode-featured-block {
        background-color: transparent !important;
        padding-top: 40px;
        padding-bottom: 40px;
    }

    .how-it-works-container {
        background-color: #f8f9fa; /* Off-white container */
        border-radius: 24px;
        padding: 60px 50px;
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
        margin-bottom: 10px;
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

    .section-subtitle-center {
        text-align: center;
        color: #6b7280;
        font-weight: 500;
        margin-bottom: 50px;
    }

    /* =========================================
       3. 4-COLUMN STEPS GRID
       ========================================= */
    .steps-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 30px;
    }

    .step-card {
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Image Box */
    .step-img-box {
        position: relative;
        width: 100%;
        aspect-ratio: 1 / 1.15; /* Makes it slightly taller than a perfect square */
        border-radius: 20px;
        overflow: hidden;
        margin-bottom: 20px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.06);
        background: #e2e8f0; /* Fallback before image loads */
    }
    .step-img-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .step-card:hover .step-img-box img {
        transform: scale(1.05);
    }

    /* Floating Center Icon */
    .step-icon-badge {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #ffffff;
        width: 52px;
        height: 52px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        z-index: 2;
        color: #111827;
        transition: transform 0.3s ease;
    }
    .step-card:hover .step-icon-badge {
        transform: translate(-50%, -50%) scale(1.1);
    }

    /* Text */
    .step-title {
        font-weight: 700;
        font-size: 1.05rem;
        color: #111827;
        line-height: 1.4;
        max-width: 90%;
    }

    /* Pill Button - Made Smaller */
    .btn-theme-pill {
        border-radius: 50px !important;
        padding: 10px 28px !important; /* Reduced padding */
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        font-size: 0.85rem !important; /* Slightly smaller font */
        margin-top: 40px;
        transition: all 0.3s ease !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .btn-theme-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0,0,0,0.15) !important;
    }

    /* =========================================
       4. DARK MODE OVERRIDES
       ========================================= */
    html[data-bs-theme="dark"] .how-it-works-container { background-color: #1e293b; box-shadow: none; border: 1px solid rgba(255,255,255,0.05); }
    html[data-bs-theme="dark"] .section-title-center { color: #ffffff; }
    html[data-bs-theme="dark"] .section-title-center::before,
    html[data-bs-theme="dark"] .section-title-center::after { background: #94a3b8; }
    html[data-bs-theme="dark"] .section-subtitle-center { color: #94a3b8; }
    html[data-bs-theme="dark"] .step-img-box { background: #334155; }
    html[data-bs-theme="dark"] .step-icon-badge { background: #0f172a; color: #ffffff; border: 1px solid rgba(255,255,255,0.1); }
    html[data-bs-theme="dark"] .step-title { color: #f8fafc; }

    /* =========================================
       5. RESPONSIVE LAYOUT
       ========================================= */
    @media (max-width: 991px) {
        .how-it-works-container { padding: 40px 30px; }
        .steps-grid { grid-template-columns: repeat(2, 1fr); gap: 40px 20px; }
        .section-title-center { font-size: 1.6rem; }
    }
    @media (max-width: 575px) {
        .how-it-works-container { padding: 30px 20px; }
        .steps-grid { grid-template-columns: 1fr; gap: 40px; }
        .section-title-center::before, .section-title-center::after { width: 20px; }
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-featured-block py-5">
    <div class="container">
        <div class="how-it-works-container wow fadeInUp" data-wow-delay="0.1s">
            
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    @if($title)
                        <h2 class="section-title-center">{!! BaseHelper::clean($title) !!}</h2>
                    @endif

                    @if ($description = $shortcode->description)
                        <p class="section-subtitle-center text-lg-medium neutral-500 mx-auto" style="max-width: 700px;">
                            {!! BaseHelper::clean($description) !!}
                        </p>
                    @endif
                </div>
            </div>

            <div class="steps-grid">
                @foreach($steps as $index => $step)
                    @if($step['image'] || $step['text'])
                        <div class="step-card wow fadeIn" data-wow-delay="{{ $index * 0.1 }}s">
                            
                            <div class="step-img-box">
                                @if($step['image'])
                                    {{ RvMedia::image($step['image'], $step['text']) }}
                                @endif
                                
                                <div class="step-icon-badge">
                                    {!! $step['icon'] !!}
                                </div>
                            </div>

                            <div class="step-title">
                                {!! BaseHelper::clean($step['text']) !!}
                            </div>
                            
                        </div>
                    @endif
                @endforeach
            </div>

            @if($buttonName && $buttonUrl)
                <div class="d-flex justify-content-center">
                    <a class="btn btn-primary btn-theme-pill" href="{{ $buttonUrl }}">
                        {!! BaseHelper::clean($buttonName) !!}
                    </a>
                </div>
            @endif

        </div>
    </div>
</section>