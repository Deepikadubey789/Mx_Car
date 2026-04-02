<section {!! $shortcode->htmlAttributes() !!} class="shortcode-branch-locations box-section background-body pt-50 pb-50">
    <div class="container">
        @if ($title = $shortcode->title)
            <div class="text-start mb-4">
                <h4 class="fw-bold" style="color: var(--bs-neutral-1000); letter-spacing: -0.02em;">{!! BaseHelper::clean($title) !!}</h4>
            </div>
        @endif

        <div class="row g-4">
            @foreach($tabs as $item)
                @continue(! ($title = Arr::get($item, 'name')))
                <div class="col-lg-3 col-sm-6">
                    <div class="card border rounded-4 p-4 h-100 transition-up" style="background: #ffffff; border-color: var(--bs-neutral-200) !important; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
                        @if ($image = Arr::get($item, 'icon_image'))
                            <div class="d-inline-flex align-items-center justify-content-center border-0 rounded-3 mb-4" style="width: 48px; height: 48px; background: var(--bs-neutral-100);">
                                {{ RvMedia::image($image, 'icon', attributes: ['width' => 28, 'height' => 28]) }}
                            </div>
                        @endif

                        <div class="card-info">
                            <h5 class="fw-bold mb-3" style="color: var(--bs-neutral-1000);">{!! BaseHelper::clean($title) !!}</h5>
                            
                            <div class="d-flex flex-column gap-2">
                                @if ($address = Arr::get($item, 'address'))
                                    <div class="d-flex align-items-start gap-2">
                                        <i class="fa-solid fa-location-dot mt-1" style="color: var(--bs-brand-2); font-size: 14px;"></i>
                                        <a class="text-decoration-none" style="color: var(--bs-neutral-600); font-size: 14px; line-height: 1.5;" href="https://maps.google.com/?q={{ $address }}">{!! BaseHelper::clean($address) !!}</a>
                                    </div>
                                @endif

                                @if ($phone = Arr::get($item, 'phone'))
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-phone" style="color: var(--bs-brand-2); font-size: 13px;"></i>
                                        <a class="text-decoration-none" style="color: var(--bs-neutral-600); font-size: 14px;" href="tel:{{ $phone }}">{!! BaseHelper::clean($phone) !!}</a>
                                    </div>
                                @endif

                                @if($email = Arr::get($item, 'email'))
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-envelope" style="color: var(--bs-brand-2); font-size: 13px;"></i>
                                        {!! Html::mailto($email, attributes: ['class' => 'text-decoration-none', 'style' => 'color: var(--bs-neutral-600); font-size: 14px;']) !!}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<style>
    .transition-up {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .transition-up:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08) !important;
        border-color: var(--bs-brand-2) !important;
    }
</style>
