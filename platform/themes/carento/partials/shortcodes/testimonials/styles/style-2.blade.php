<style>
    .testimonial-card-style-2 {
        background: #ffffff !important;
        border-radius: 20px !important;
        padding: 25px !important;
        height: 100% !important;
        min-height: 200px !important;
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05) !important;
        margin: 10px !important;
    }
    .testimonial-content-style-2 {
        font-size: 16px !important;
        line-height: 1.5 !important;
        color: #4b5563 !important;
        margin-bottom: 20px !important;
        font-style: italic !important;
    }
    .testimonial-footer-style-2 {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
    }
    .testimonial-user-style-2 {
        display: flex !important;
        align-items: center !important;
        gap: 12px !important;
    }
    .testimonial-avatar-style-2 {
        width: 50px !important;
        height: 50px !important;
        border-radius: 50% !important;
        object-fit: cover !important;
    }
    .testimonial-name-style-2 {
        font-size: 16px !important;
        font-weight: 700 !important;
        color: #111827 !important;
        margin: 0 !important;
    }
    .testimonial-stars-style-2 {
        display: flex !important;
        gap: 2px !important;
    }
    .star-red-box {
        background: #cb462b !important;
        color: #ffffff !important;
        width: 18px !important;
        height: 18px !important;
        padding: 4px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 2px !important;
    }
    .star-red-box svg {
        width: 12px !important;
        height: 12px !important;
        fill: currentColor !important;
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-testimonial shortcode-faqs section-box py-60 background-body">
    <div class="container">
        @if($shortcode->title || $shortcode->subtitle)
            <div class="row align-items-end mb-40">
                <div class="col-lg-8">
                    @if($shortcode->subtitle)
                        <span class="section-subtitle mxcar-page-desc mb-2">{!! BaseHelper::clean($shortcode->subtitle) !!}</span>
                    @endif
                    @if($shortcode->title)
                        <h2 class="mxcar-page-title">{!! BaseHelper::clean($shortcode->title) !!}</h2>
                    @endif
                </div>
            </div>
        @endif

        <div class="box-swiper mt-30">
            <div class="swiper-container swiper-group-3">
                <div class="swiper-wrapper">
                    @foreach($testimonials as $testimonial)
                        <div class="swiper-slide h-auto">
                            <div class="testimonial-card-style-2">
                                <div class="testimonial-content-style-2">
                                    "{!! BaseHelper::clean(Str::limit($testimonial->content, 150)) !!}"
                                </div>
                                <div class="testimonial-footer-style-2">
                                    <div class="testimonial-user-style-2">
                                        {{ RvMedia::image($testimonial->image, $testimonial->name, 'thumb', false, ['class' => 'testimonial-avatar-style-2']) }}
                                        <div class="testimonial-info-style-2">
                                            <h6 class="testimonial-name-style-2">{{ $testimonial->name }}</h6>
                                        </div>
                                    </div>
                                    <div class="testimonial-stars-style-2">
                                        @for($i = 0; $i < 5; $i++)
                                            <div class="star-red-box">
                                                <svg viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                            </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination swiper-pagination-style-2 mt-40"></div>
            </div>
        </div>
    </div>
</section>
