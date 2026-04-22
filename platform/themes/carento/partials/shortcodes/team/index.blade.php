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
                        <div class="card-team background-card shadow-sm hover-up border-0 text-center p-4 h-100 d-flex flex-column" style="border-radius: 24px; background: #ffffff; transition: all 0.3s ease;">
                            
                            <div class="team-avatar mx-auto mb-4" style="width: 150px; height: 150px; border-radius: 50%; overflow: hidden; border: 5px solid #f8f9fa; box-shadow: 0 10px 25px rgba(0,0,0,0.06);">
                                <a href="{{ $team->url }}" class="d-block h-100 w-100">
                                    {{ RvMedia::image($team->photo, $team->name, 'medium-square', false, ['style' => 'width: 100%; height: 100%; object-fit: cover;']) }}
                                </a>
                            </div>

                            <div class="team-info flex-grow-1">
                                <a href="{{ $team->url }}" class="text-decoration-none">
                                    <h5 class="fw-bold mb-1 neutral-1000" style="font-size: 20px; letter-spacing: -0.5px;">{{ $team->name }}</h5>
                                </a>
                                
                                @if($teamTitle = $team->title)
                                    <p class="neutral-500 mb-0" style="font-size: 14px; color: #6b7280;">{!! BaseHelper::clean($teamTitle) !!}</p>
                                @endif
                            </div>
                            
                            <div class="mt-4">
                                <hr class="w-50 mx-auto mt-0 mb-4" style="opacity: 0.08;">
                                <div class="team-socials d-flex align-items-center justify-content-center gap-2">
                                    @foreach($team->socials as $key => $social)
                                        <a href="{{ $social }}" class="rounded-circle d-flex align-items-center justify-content-center hover-up" style="width: 40px; height: 40px; background: #f3f4f6; color: #4b5563; transition: all 0.2s ease; text-decoration: none;">
                                            <x-core::icon name="ti ti-brand-{{ $key }}" class="m-0" style="width: 18px; height: 18px;" />
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                            
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif