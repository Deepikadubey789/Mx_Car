@php
    $title = $shortcode->title;
    $subTitle = $shortcode->sub_title;
@endphp
<section {!! $shortcode->htmlAttributes() !!} class="shortcode-why-us section-box background-body">
    <div class="container">
        <div style="
            background-color: #f8f9fa;
            border-radius: 24px;
            padding: 50px 60px;
            margin: 0 auto;
        ">
            <div class="text-center wow fadeInUp">
                @if($subTitle)
                    <span style="font-size: 13px; letter-spacing: 0.5px; background:#c0392b; color:#fff; padding: 4px 16px; border-radius: 50px; display:inline-block; margin-bottom: 12px; font-weight: 700;">{!! BaseHelper::clean($subTitle) !!}</span>
                @endif

                @if($title)
                    <h2 class="heading-3" style="display: flex; align-items: center; justify-content: center; gap: 14px; flex-wrap: wrap;">
                        <span style="display:inline-block; width:50px; height:2px; background:#8b1a1a; border-radius:2px; flex-shrink:0;"></span>
                        {!! BaseHelper::clean($title) !!}
                        <span style="display:inline-block; width:50px; height:2px; background:#8b1a1a; border-radius:2px; flex-shrink:0;"></span>
                    </h2>
                @endif
            </div>

            <div class="row mt-40 justify-content-center text-center">
                @foreach($tabs as $tab)
                    @continue(! $cardImage = Arr::get($tab, 'card_image'))
                    @continue(! $cardTitle = Arr::get($tab, 'card_title'))
                    @continue(! $cardContent = Arr::get($tab, 'card_content'))

                    <div class="col-lg-3 col-md-6 col-sm-12 mb-40 d-flex flex-column align-items-center text-center">
                        <div class="wow fadeIn w-100" data-wow-delay="0.1s"
                            onmouseover="this.style.transform='translateY(-8px)'; this.querySelector('.icon-circle').style.transform='scale(1.15) translateY(-5px)'"
                            onmouseout="this.style.transform='translateY(0)'; this.querySelector('.icon-circle').style.transform='scale(1) translateY(0)'"
                            style="
                                background: linear-gradient(135deg, #1a1035, #8b1a1a);
                                backdrop-filter: blur(10px);
                                border: 1px solid rgba(255,255,255,0.15);
                                border-radius: 12px;
                                padding: 32px 24px;
                                text-align: center;
                                border-top: 3px solid #8b1a1a;
                                transition: transform 0.25s ease;
                                display: flex;
                                flex-direction: column;
                                align-items: center;
                            ">
                            <div style="
                                width: 80px;
                                height: 80px;
                                background-color: #c0392b;
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                margin: 0 auto 16px;
                            ">
                                <img src="{{ RvMedia::getImageUrl($cardImage) }}" alt="{{ $cardTitle }}" style="
                                    width: 48px !important;
                                    height: 48px !important;
                                    object-fit: contain;
                                    display: block;
                                    filter: brightness(0) invert(1) drop-shadow(0 0 3px rgba(255,255,255,0.9));
                                ">
                            </div>
                            <div style="text-align: center; width: 100%;">
                                <h5 style="
                                    color: #ffffff !important;
                                    font-size: 18px;
                                    font-weight: 700;
                                    margin-bottom: 10px;
                                ">{!! BaseHelper::clean($cardTitle) !!}</h5>
                                <p style="
                                    color: rgba(255,255,255,0.7) !important;
                                    font-size: 14px;
                                    line-height: 1.6;
                                    margin: 0 auto;
                                    max-width: 260px;
                                ">{!! BaseHelper::clean($cardContent) !!}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>{{-- styled box end --}}
    </div>{{-- container end --}}
</section>