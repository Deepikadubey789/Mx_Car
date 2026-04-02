@php
    $title = $shortcode->title;
    $subtitle = $shortcode->subtitle;
@endphp

<style>
    .shortcode-testimonial-style-1 {
        background-color: transparent !important;
        padding: 50px 0;
    }

    .testimonial-card-clean {
        background: #ffffff !important;
        border-radius: 24px !important;
        padding: 40px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.04) !important;
        border: none !important;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: all 0.3s ease;
    }

    .testimonial-card-clean:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.08) !important;
    }

    .testimonial-content-p {
        font-size: 18px !important;
        line-height: 1.6 !important;
        color: #111827 !important;
        margin-bottom: 40px !important;
        flex-grow: 1;
        font-weight: 500 !important;
    }

    .testimonial-footer-clean {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: auto;
    }

    .testimonial-user-box {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .testimonial-user-img {
        width: 65px !important;
        height: 65px !important;
        border-radius: 50% !important;
        object-fit: cover !important;
    }

    .testimonial-user-name {
        font-size: 20px !important;
        font-weight: 700 !important;
        color: #000 !important;
        margin-bottom: 2px !important;
    }

    .testimonial-user-desig {
        font-size: 14px !important;
        color: #6b7280 !important;
        margin-bottom: 0 !important;
    }

    .testimonial-stars-clean {
        display: flex;
        gap: 4px;
    }

    .star-bg-red {
        background: #cb462b !important;
        color: #fff !important;
        width: 20px !important;
        height: 20px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 2px !important;
        padding: 4px !important;
    }

    .star-bg-red svg {
        width: 12px !important;
        height: 12px !important;
        fill: currentColor !important;
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-testimonial-style-1">
    <div class="container">
        @if($title || $subtitle)
            <div class="row mb-50 justify-content-center text-center">
                <div class="col-lg-8">
                    @if($subtitle)
                        <span class="section-subtitle bg-brand-2 p-1 px-3 mb-2 d-inline-block rounded-pill text-white fw-bold" style="font-size: 13px; letter-spacing: 0.5px;">{!! BaseHelper::clean($subtitle) !!}</span>
                    @endif
                    @if($title)
                        <h2 class="heading-2 mb-20">{!! BaseHelper::clean($title) !!}</h2>
                    @endif
                </div>
            </div>
        @endif

        <div class="swiper-container testimonial-swiper-style-1">
            <div class="swiper-wrapper">
                @foreach($testimonials as $testimonial)
                    <div class="swiper-slide h-auto">
                        <div class="testimonial-card-clean">
                            <div class="testimonial-content-p text-start">
                                "{!! BaseHelper::clean($testimonial->content) !!}"
                            </div>
                            
                            <div class="testimonial-footer-clean">
                                <div class="testimonial-user-box">
                                    <div class="testimonial-user-image">
                                        {{ RvMedia::image($testimonial->image, $testimonial->name, 'thumb', false, ['class' => 'testimonial-user-img']) }}
                                    </div>
                                    <div class="testimonial-user-info text-start">
                                        <h5 class="testimonial-user-name text-dark">{{ $testimonial->name }}</h5>
                                        @if($company = $testimonial->company)
                                            <p class="testimonial-user-desig">{{ $company }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="testimonial-stars-clean">
                                    @for($i = 0; $i < 5; $i++)
                                        <div class="star-bg-red">
                                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swiper !== 'undefined') {
            new Swiper('.testimonial-swiper-style-1', {
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    }
                }
            });
        }
    });
</script>