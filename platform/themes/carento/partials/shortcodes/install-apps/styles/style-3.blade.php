@php
    $styles = [
        "background-color: $bgColor" => $bgColor,
    ];
@endphp

<style>
    /* Add top padding so the entire block sits centered on the page */
    .box-app-2 {
        padding-top: 80px; 
    }

    /* Ensures the map perfectly fills the right column and respects the rounded corners */
    .box-app-map {
        width: 100%;
        height: 100%;
        min-height: 450px; 
    }
    .box-app-map iframe {
        width: 100%;
        height: 100%;
        border: 0;
        /* Matches the border radius to the container */
        border-top-right-radius: 12px;
        border-bottom-right-radius: 12px;
    }
    
    /* Responsive adjustment for when the map stacks under the text on mobile */
    @media (max-width: 991px) {
        .box-app-map iframe {
            border-top-right-radius: 0;
            border-bottom-left-radius: 12px;
            border-bottom-right-radius: 12px;
            min-height: 350px;
        }
    }

    /* App Store Badges Styling */
    .box-app-2 .download-apps {
        display: flex;
        flex-wrap: nowrap !important; /* FORCES images to stay on one line */
        gap: 12px !important; /* Slightly tighter gap to save space */
        margin-top: 20px !important;
    }
    
    .box-app-2 .download-apps a {
        display: inline-block;
        flex: 0 1 auto; /* Allows them to shrink slightly if the screen is very small */
    }
    
    .box-app-2 .download-apps a img {
        height: 40px !important; /* REDUCED SIZE to fit on one line perfectly */
        width: auto !important;
        max-width: 100%;
        transition: transform 0.2s ease, opacity 0.2s ease !important;
        border-radius: 6px;
    }
    
    .box-app-2 .download-apps a:hover img {
        transform: translateY(-2px) !important;
        opacity: 0.9 !important;
    }
</style>

<section {!! $shortcode->htmlAttributes(['style' => $styles]) !!} class="box-app-2 position-relative pb-80">
    <div class="container bg-4 rounded-12 overflow-hidden">
        <div class="row align-items-stretch"> 
            
            <div class="col-lg-4 p-5 d-flex flex-column justify-content-center">
                @if(empty($buttonLabel) === false)
                    <div>
                        <span class="btn btn-primary background-brand-2">{!! BaseHelper::clean($buttonLabel) !!}</span>
                    </div>
                @endif
                
                @if(!empty($title))
                    <h4 class="mt-4 mb-3 shortcode-title">{!! BaseHelper::clean($title) !!}</h4>
                @endif
                
                @if(!empty($appsDescription))
                    <p class="text-md-medium pb-3 neutral-500">{!! BaseHelper::clean($appsDescription) !!}</p>
                @endif
                
                <div class="download-apps mt-0">
                    @if(!empty($androidAppImage))
                        <a class="wow fadeInUp" href="{{ $androidAppUrl }}">
                            {{ RvMedia::image($androidAppImage) }}
                        </a>
                    @endif
                    @if(!empty($iosAppImage))
                        <a class="wow fadeInUp" data-wow-delay="0.2s" href="{{ $iosAppUrl }}">
                            {{ RvMedia::image($iosAppImage) }}
                        </a>
                    @endif
                </div>
            </div>
            
            <div class="col-lg-8 px-0">
                <div class="box-app-map">
                   <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d25230.112843084957!2d-122.425599177959!3d37.77212904816295!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8085805f1234dce3%3A0xcf58a2e39e5265f5!2sSan%20Francisco%2C%20CA%2094111%2C%20USA!5e0!3m2!1sen!2sin!4v1775018174276!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
            </div>
            
        </div>
    </div>
</section>