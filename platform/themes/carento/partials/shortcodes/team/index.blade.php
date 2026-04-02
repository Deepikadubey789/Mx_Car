@if($teams->isNotEmpty())
    <section {!! $shortcode->htmlAttributes() !!} class="section-team-1 py-96 background-body border-top border-bottom shortcode-team">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-xl-6 col-lg-7 col-md-9 col-sm-11">
                    <div class="text-center mb-5">
                        @if ($subtitle = $shortcode->subtitle)
                             <span class="section-subtitle bg-brand-2 p-1 px-3 mb-2 d-inline-block rounded-pill text-white fw-bold" style="font-size: 13px; letter-spacing: 0.5px;">{!! BaseHelper::clean($subtitle) !!}</span>
                        @endif

                        @if ($title = $shortcode->title)
                            <h2 class="heading-3">{!! BaseHelper::clean($title) !!}</h2>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row mt-50">
                @foreach($teams as $team)
                    <div class="col-lg-3 col-md-6 col-12 mb-40">
                        <div class="card-news background-card hover-up shadow-2 mb-4 mb-lg-0 border-0" style="border-radius: 20px; overflow: hidden; background: #fff;">
                            <div class="card-image" style="border-radius: 20px 20px 0 0; overflow: hidden;">
                                <a href="{{ $team->url }}">
                                    {{ RvMedia::image($team->photo, $team->name, 'medium-square', false, ['style' => 'width: 100%; height: auto; display: block;']) }}
                                </a>
                            </div>
                            <div class="card-info p-4 text-start">
                                <div class="card-title mb-3">
                                    <a class="text-xl-bold neutral-1000 text-decoration-none" href="{{ $team->url }}">
                                        <h5 class="fw-bold mb-1" style="color: #000; font-size: 20px; letter-spacing: -0.5px;">{{ $team->name }}</h5>
                                    </a>

                                    @if($teamTitle = $team->title)
                                        <p class="text-sm-medium neutral-500 mb-0" style="font-size: 14px; color: #6b7280;">{!! BaseHelper::clean($teamTitle) !!}</p>
                                    @endif
                                </div>
                                <div class="card-program mt-4">
                                    <div class="endtime d-flex align-items-center justify-content-between">
                                        <div class="card-author d-flex align-items-center gap-3">
                                            @foreach($team->socials as $key => $social)
                                                <a href="{{ $social }}" class="text-dark hover-up" style="font-size: 18px; color: #000;">
                                                    <x-core::icon name="ti ti-brand-{{ $key }}" class="m-0" />
                                                </a>
                                            @endforeach
                                        </div>
                                        <a href="{{ $team->url }}" class="rounded-circle background-100 icon-shape icon icon-sm hover-up border-0" style="background: #f3f4f6; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;">
                                            <img class="m-0" src="{{ Theme::asset()->url('images/icons/arrow-up-right.svg') }}" alt="icon" style="width: 18px; height: 18px;" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
