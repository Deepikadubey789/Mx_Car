@php
    // Scoped variable name for readability/safety
    $androidAppUrl = $shortcode->android_app_url ?? '#';
    $androidAppImage = $shortcode->android_app_image;
    $iosAppUrl = $shortcode->ios_app_url ?? '#';
    $iosAppImage = $shortcode->ios_app_image;
    $decorImage = $shortcode->decor_image;
@endphp

{{-- SCAMPED STYLES FOR REDESIGN --}}
<style>
    /* Scope the section to avoid bleeding into other areas */
    .install-app-modern-style {
        padding-top: 60px; /* Modern spacing */
        padding-bottom: 60px;
        background-color: transparent !important;
        background-image: none !important; /* Force remove any outer background */
    }

    /* Target inner container */
    .install-app-modern-style .main-card-container {
        /* Screenshot looks light off-white, using standard f8f9fa */
        background-color: #f8f9fa; 
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08); /* Soft, premium shadow */
        border-radius: 16px;
        overflow: hidden; /* Clips the image to the card's rounded corners */
    }

    /* Target left column content wrapper */
    .install-app-modern-style .content-col {
        padding: 80px 60px; 
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    /* Redesign shortcode title */
    .install-app-modern-style .shortcode-title {
        font-size: 2.5rem !important; 
        font-weight: 800 !important;
        color: #111827 !important; 
        line-height: 1.2 !important;
        letter-spacing: -0.5px;
        margin-bottom: 15px !important;
    }

    /* Redesign Description */
    .install-app-modern-style .description-p {
        color: #475569 !important; 
        font-size: 1.05rem !important;
        margin-bottom: 35px !important; /* Increased slightly to separate from buttons */
        font-weight: 500;
    }

    /* Target download buttons wrapper */
    .install-app-modern-style .download-apps {
        display: flex;
        flex-direction: column; /* CHANGED: Stacks buttons vertically */
        gap: 20px; /* Increased gap for vertical layout */
        align-items: flex-start; /* Aligns them to the left */
    }

    /* Target standard button badges for modern shadow effect */
    .install-app-modern-style .download-apps img {
        height: 85px; /* CHANGED: Increased from 50px to make them much bigger */
        width: auto;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 10px; 
    }

    /* Standard modern button shadow/lift on hover */
    .install-app-modern-style .download-apps img:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    /* Target right column image container */
    .install-app-modern-style .image-col {
        position: relative;
        min-height: 350px; /* Ensures image area has height on mobile */
    }

    .install-app-modern-style .box-app-img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .install-app-modern-style .box-app-img img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important; /* Stretches image perfectly to fill the space */
        object-position: top !important;
    }

    /* Mobile Responsiveness */
    @media (max-width: 991px) {
        .install-app-modern-style .content-col { padding: 50px 40px; }
        .install-app-modern-style .shortcode-title { font-size: 2rem !important; }
        .install-app-modern-style .image-col { min-height: 300px; }
        .install-app-modern-style .box-app-img { position: relative; height: 300px; }
    }
    @media (max-width: 575px) {
        .install-app-modern-style .content-col { padding: 40px 25px; }
        .install-app-modern-style .download-apps img { height: 55px; } /* Slightly smaller on very small screens, but still bigger than before */
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="install-app-modern-style">
    <div class="container">
        <div class="main-card-container wow fadeInUp" data-wow-delay="0.1s">
            
            {{-- g-0 removes the gap between columns so the image touches the middle seamlessly --}}
            <div class="row g-0 align-items-stretch">
                
                {{-- Text Column (Left) --}}
                <div class="col-lg-6 content-col">
                    
                    @if(!empty($title))
                        <h2 class="shortcode-title">{!! BaseHelper::clean($title) !!}</h2>
                    @endif
                    
                    @if(!empty($buttonLabel))
                        {{-- Used buttonLabel as the small gray subtitle matching the screenshot ("Install App") --}}
                        <p class="description-p">{!! BaseHelper::clean($buttonLabel) !!}</p>
                    @elseif(!empty($appsDescription))
                        <p class="description-p">{!! BaseHelper::clean($appsDescription) !!}</p>
                    @endif
                    
                    {{-- Stacked App Buttons --}}
                    <div class="download-apps">
                        @if(!empty($androidAppImage))
                            <a href="{{ $androidAppUrl }}" target="_blank">
                                {{ RvMedia::image($androidAppImage) }}
                            </a>
                        @endif
                        
                        @if(!empty($iosAppImage))
                            <a href="{{ $iosAppUrl }}" target="_blank">
                                {{ RvMedia::image($iosAppImage) }}
                            </a>
                        @endif
                    </div>
                </div>
                
                {{-- Image Column (Right) --}}
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