@php
    $backgroundImage = $shortcode->background_image;
    $backgroundImage = $backgroundImage ? RvMedia::getImageUrl($backgroundImage) : null;
@endphp

{{-- FRONTEND CUSTOM CSS SCOPED ONLY TO THE PROMO SECTION --}}
<style>
    /* =========================================
       1. MAIN WRAPPER
       ========================================= */
    .shortcode-promotion-block {
        background-color: transparent !important;
        background-image: none !important;
        padding-top: 40px;
        padding-bottom: 40px;
    }

    /* =========================================
       2. CINEMATIC BANNER CONTAINER
       ========================================= */
    .promo-cinematic-banner {
        position: relative;
        width: 100%;
        min-height: 480px;
        border-radius: 24px;
        overflow: hidden;
        background-size: cover;
        /* Focus on the right side of the image since text is on the left */
        background-position: center right; 
        background-repeat: no-repeat;
        display: flex;
        align-items: center;
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.08);
    }

    /* The magic gradient: Dark on the left, transparent on the right */
    .promo-side-gradient {
        position: absolute;
        inset: 0;
        background: linear-gradient(90deg, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.75) 45%, rgba(15, 23, 42, 0.1) 75%, transparent 100%);
        z-index: 1;
    }

    /* =========================================
       3. LEFT-ALIGNED CONTENT
       ========================================= */
    .promo-content-left {
        position: relative;
        z-index: 2;
        padding: 60px 80px;
        max-width: 750px; /* Prevents text from stretching across the image */
        text-align: left;
    }

    /* Typography */
    .promo-title {
        font-size: 3rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1.15;
        letter-spacing: -1px;
        margin-bottom: 20px;
    }

    .promo-subtitle {
        font-size: 1.15rem;
        font-weight: 400;
        color: rgba(255, 255, 255, 0.85);
        line-height: 1.6;
        margin-bottom: 35px;
        max-width: 90%;
    }

    /* =========================================
       4. THEME BUTTON (PILL SHAPE)
       ========================================= */
    .promo-btn-pill {
        border-radius: 50px !important;
        padding: 14px 40px !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        letter-spacing: 0.5px !important;
        font-size: 0.95rem !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 10px;
        transition: all 0.3s ease !important;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
    }
    .promo-btn-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.25) !important;
    }
    
    .promo-btn-pill svg {
        stroke: currentColor; 
        fill: none;
        transition: transform 0.2s ease;
    }
    .promo-btn-pill:hover svg {
        transform: translateX(4px); /* Little arrow nudge on hover */
    }

    /* =========================================
       5. MOBILE RESPONSIVENESS
       ========================================= */
    @media (max-width: 991px) {
        .promo-content-left { padding: 50px 40px; max-width: 100%; }
        .promo-title { font-size: 2.5rem; }
    }
    
    @media (max-width: 768px) {
        .promo-cinematic-banner { 
            min-height: 400px; 
            border-radius: 16px; 
            background-position: center;
        }
        /* On small screens, switch to a bottom-to-top gradient so the image shows at the top */
        .promo-side-gradient {
            background: linear-gradient(0deg, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.8) 50%, transparent 100%);
        }
        .promo-content-left { 
            padding: 40px 25px; 
            margin-top: auto; /* Pushes text to the bottom */
            text-align: center; /* Center text on mobile */
        }
        .promo-subtitle { max-width: 100%; }
    }
</style>

{{-- Removed $shortcode->htmlAttributes() to prevent the CMS from forcing the background onto the full-width section --}}
<section class="shortcode-promotion-block py-5">
    <div class="container">
        
        <div class="promo-cinematic-banner" style="background-image: url('{{ $backgroundImage }}');">
            <div class="promo-side-gradient"></div>
            
            <div class="promo-content-left wow fadeInLeft" data-wow-delay="0.1s">
                
                @if ($title = $shortcode->title)
                    <h2 class="promo-title">{!! BaseHelper::clean($title) !!}</h2>
                @endif

                @if ($subtitle = $shortcode->subtitle)
                    <p class="promo-subtitle">
                        {!! BaseHelper::clean($subtitle) !!}
                    </p>
                @endif

                @if (($buttonLabel = $shortcode->button_label) && ($buttonUrl = $shortcode->button_url))
                    <a class="btn btn-primary promo-btn-pill" href="{{ $buttonUrl }}">
                        {!! BaseHelper::clean($buttonLabel) !!}
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                @endif
                
            </div>
        </div>

    </div>
</section>