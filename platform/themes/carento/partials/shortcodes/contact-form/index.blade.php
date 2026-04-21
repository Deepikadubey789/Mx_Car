@php
    $isShowMap = $shortcode->show_map && $shortcode->map_address;
@endphp

<style>
    /* Bypasses the transparency rule by styling an inner container */
    .contact-bento-container {
        background-color: #f8f9fa !important; 
        border-radius: 24px;
        padding: 60px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    }
    
    /* Dark mode support */
    html[data-bs-theme="dark"] .contact-bento-container {
        background-color: #1e293b !important;
        box-shadow: none;
        border: 1px solid rgba(255,255,255,0.05);
    }

    @media (max-width: 991px) {
        .contact-bento-container {
            padding: 40px 20px;
        }
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-contact-form py-5">
    <div class="container">
        <div class="contact-bento-container">
            <div class="row">
                <div @class(['mb-30', 'col-lg-6' => $isShowMap, 'col-12' => ! $isShowMap])>
                    @if ($title = $shortcode->title)
                        <h2 class="shortcode-title mb-25">{!! BaseHelper::clean($title) !!}</h2>
                    @endif
                    <div class="form-contact">
                        {!! $form
                            ->setFormInputClass('form-control')
                            ->setFormInputWrapperClass('form-group')
                            ->setFormLabelClass('text-sm-medium neutral-1000')
                            ->modify(
                                'submit', 'submit', ['attr' => ['class' => 'btn btn-book'], 'label' => $shortcode->button_label ?: __('Send Message')], true)
                            ->renderForm()
                        !!}
                    </div>
                </div>

                @if ($isShowMap)
                    <div class="col-lg-6 mb-30">
                        <div class="ps-lg-5">
                            @if($mapTitle = $shortcode->map_title)
                                <h4 class="neutral-1000">{!! BaseHelper::clean($mapTitle) !!}</h4>
                            @endif
                            <p class="neutral-500 mb-30">{!! BaseHelper::clean($shortcode->map_address) !!}</p>
                            <iframe class="h-520 rounded-3" src="https://maps.google.com/maps?q={{ urlencode($shortcode->map_address) }}&t=&z=13&ie=UTF8&iwloc=&output=embed" width="100%" height="650" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                            </iframe>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>