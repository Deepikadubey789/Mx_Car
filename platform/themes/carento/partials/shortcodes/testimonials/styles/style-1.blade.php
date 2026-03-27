<section {!! $shortcode->htmlAttributes() !!} class="shortcode-testimonial section-box py-96 background-body">
    <div class="container">
        <div class="row align-items-end">
            <div class="col-md-9 col-sm-9">
                @if($shortcode->subtitle)
                    <div class="box-author-testimonials">
                        @foreach($testimonials as $testimonial)
                            {{ RvMedia::image($testimonial->image, $testimonial->name, 'thumb') }}
                        @endforeach

                        {!! BaseHelper::clean($shortcode->subtitle) !!}
                    </div>
                @endif

                @if($shortcode->title)
                    <h2 class="heading-3 mt-8 mb-15 shortcode-title">{!! BaseHelper::clean($shortcode->title) !!}</h2>
                @endif
            </div>
        </div>
    </div>
    <div class="block-testimonials">
        <div class="container-testimonials">
            <div class="container-slider ps-0">
                <div class="box-swiper mt-30">
                    <div class="swiper-container swiper-group-animate swiper-group-journey">
                        <div class="swiper-wrapper">
                            @foreach($testimonials as $testimonial)
                                <div class="swiper-slide">
                                    <div class="card-testimonial background-card">
                                        @if($content = $testimonial->content)
                                            <div class="card-info">
                                                <p class="text-md-regular neutral-500">{!! BaseHelper::clean($content) !!}</p>
                                            </div>
                                        @endif

                                        <div class="card-top pt-40 border-0 mb-0">
                                            <div class="card-author">
                                                <div class="card-image">
                                                    {{ RvMedia::image($testimonial->image, $testimonial->name, attributes: ['style' => 'object-fit: cover !important;']) }}
                                                </div>
                                                <div class="card-info">
                                                    <p class="text-lg-bold neutral-1000">{!! BaseHelper::clean($testimonial->name) !!}</p>

                                                    @if($company = $testimonial->company)
                                                        <p class="text-md-regular neutral-1000">{!! BaseHelper::clean($company) !!}</p>
                                                    @endif

                                                </div>
                                            </div>
                                            <div class="card-rate">
                                                @php
                                                    $start = (int) $testimonial->getMetaData('rating_star', true) ?: 5;
                                                @endphp

                                                @for($i = 0; $i < $start; $i++)
                                                    <svg class="background-brand-2 p-1" width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M13.3566 5.3503C13.2689 5.07913 13.0284 4.88654 12.7438 4.86089L8.87869 4.50994L7.35031 0.932611C7.23761 0.670439 6.98096 0.500732 6.6958 0.500732C6.41064 0.500732 6.15398 0.670439 6.04129 0.933224L4.51291 4.50994L0.647152 4.86089C0.363115 4.88715 0.123217 5.07913 0.0350431 5.3503C-0.0531308 5.62146 0.0282998 5.91888 0.243166 6.10636L3.16476 8.66862L2.30325 12.4636C2.24021 12.7426 2.34851 13.031 2.58003 13.1984C2.70447 13.2883 2.85007 13.3341 2.99689 13.3341C3.12348 13.3341 3.24905 13.2999 3.36174 13.2325L6.6958 11.2399L10.0286 13.2325C10.2725 13.3792 10.5799 13.3658 10.811 13.1984C11.0426 13.0305 11.1508 12.742 11.0877 12.4636L10.2262 8.66862L13.1478 6.10687C13.3627 5.91888 13.4447 5.62197 13.3566 5.3503Z" fill="currentColor"/></svg>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
