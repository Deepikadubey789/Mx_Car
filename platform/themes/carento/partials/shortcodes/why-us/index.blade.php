@php
    $title = $shortcode->title;
    $subTitle = $shortcode->sub_title;
@endphp
<section {!! $shortcode->htmlAttributes() !!} class="shortcode-why-us section-box box-why-book-22 background-body">
    <div class="container">
        <div class="text-center wow fadeInUp">
            @if($subTitle)
                <span class="section-subtitle bg-brand-2 p-1 px-3 mb-2 d-inline-block rounded-pill text-white fw-bold" style="font-size: 13px; letter-spacing: 0.5px;">{!! BaseHelper::clean($subTitle) !!}</span>
            @endif

            @if($title)
                <h2 class="heading-3">{!! BaseHelper::clean($title) !!}</h2>
            @endif
        </div>
        <div class="row mt-40 justify-content-center text-center">
            @foreach($tabs as $tab)
                @continue(! $cardImage = Arr::get($tab, 'card_image'))
                @continue(! $cardTitle = Arr::get($tab, 'card_title'))
                @continue(! $cardContent = Arr::get($tab, 'card_content'))

                <div class="col-lg-3 col-md-6 col-sm-12 mb-40 d-flex flex-column align-items-center text-center">
                    <div class="card-why-clean wow fadeIn w-100" data-wow-delay="0.1s" style="padding: 10px 0; display: flex; flex-direction: column; align-items: center;">
                        <div class="card-image mb-3 d-flex justify-content-center align-items-center" style="width: 100px; height: 100px; margin: 0 auto !important;">
                            <img src="{{ RvMedia::getImageUrl($cardImage) }}" alt="{{ $cardTitle }}" style="width: 100px !important; height: 100px !important; object-fit: contain; display: block !important; margin: 0 auto !important;">
                        </div>
                        <div class="card-info text-center w-100">
                            <h5 class="fw-bold mb-2" style="color: #000; font-size: 22px; font-weight: 700; width: 100%; text-align: center;">{!! BaseHelper::clean($cardTitle) !!}</h5>
                            <p class="text-md-medium neutral-500 mx-auto" style="max-width: 280px; line-height: 1.6; color: #4b5563; font-size: 16px; width: 100%; text-align: center;">{!! BaseHelper::clean($cardContent) !!}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
