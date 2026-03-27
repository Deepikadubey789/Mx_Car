<style>
    /* 1. The Wrapper: This creates the "Island" look like Zoomcar */
    .services-zoom-style .zoom-outer-wrapper {
        background-color: #f8f9fa !important; /* The gray card background */
        border-radius: 24px !important;      /* Large rounded corners for the whole block */
        padding: 60px 40px !important;       /* Internal spacing */
        margin: 0 auto;
        max-width: 1240px;                   /* Limits the width so it's not full-screen */
    }

    /* 2. The Title Badge - Dark Green block */
    .services-zoom-style .zoom-badge {
        background-color: #054232; 
        color: #fff !important;
        padding: 2px 10px;
        border-radius: 4px;
        font-family: monospace;
        font-weight: 700;
        font-size: 1.4rem;
        margin-left: 5px;
        display: inline-block;
        vertical-align: middle;
    }

    /* 3. The Individual Cards - Transparent to match Zoomcar's flat look */
    .services-zoom-style .card-news {
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        height: 100%;
    }

    /* 4. Image Styling - Precise rounding */
    .services-zoom-style .card-image img {
        height: 200px;
        width: 100%;
        object-fit: cover;
        border-radius: 12px !important;
        display: block;
        margin-bottom: 15px;
    }

    /* 5. Typography */
    .services-zoom-style .card-title-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .services-zoom-style .service-name {
        font-size: 16px;
        font-weight: 700;
        color: #1a1a1a;
        text-decoration: none;
        line-height: 1.2;
    }

    .services-zoom-style .service-description {
        font-size: 13px;
        color: #707070;
        line-height: 1.6;
        margin-bottom: 0;
    }

    .services-zoom-style .zoom-arrow {
        color: #1a1a1a;
        transition: transform 0.2s ease;
    }

    .services-zoom-style .card-news:hover .zoom-arrow {
        transform: translate(2px, -2px);
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-car-services services-zoom-style py-96 background-body">
    <div class="container">
        <div class="zoom-outer-wrapper">
            
            <div class="row justify-content-center text-center mb-50">
                <div class="col-lg-10">
                    <h2 class="fw-bold mb-3" style="font-size: 2rem;">
                        {!! BaseHelper::clean($shortcode->title) !!}
                        @if($subtitle = $shortcode->subtitle)
                             <span class="zoom-badge">{{ $subtitle }}</span>
                        @endif
                    </h2>
                    @if ($description = $shortcode->description)
                        <p class="neutral-500" style="font-size: 1.1rem;">{!! BaseHelper::clean($description) !!}</p>
                    @endif
                </div>
            </div>

            <div class="row">
                @foreach($services as $service)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card-news wow fadeInUp" data-wow-delay="{{ $loop->index * 0.1 }}s">
                            
                            <div class="card-image">
                                <a href="{{ $service->url }}">
                                    {!! RvMedia::image($service->image, $service->name, 'medium-rectangle') !!}
                                </a>
                            </div>

                            <div class="card-info">
                                <div class="card-title-flex">
                                    <a class="service-name" href="{{ $service->url }}">{{ $service->name }}</a>
                                    <a href="{{ $service->url }}" class="zoom-arrow">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="7" y1="17" x2="17" y2="7"></line><polyline points="7 7 17 7 17 17"></polyline></svg>
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