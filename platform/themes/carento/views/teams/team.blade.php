@php
    Theme::set('breadcrumbs', true);
    Theme::set('breadcrumb_simple', true);
@endphp

<div class="team-detail-page">
    <section class="section-box background-body">
        <div class="container">
            <div class="row">
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <div class="team-details-info-wrap">
                        @if ($photo = $team->photo)
                            <div class="team-details-thumb mb-4">
                                <div class="card-image rounded-12 overflow-hidden">
                                    {{ RvMedia::image($photo, $team->name ?: $team->title, 'large-rectangle', attributes: ['class' => 'w-100 h-100 object-fit-cover']) }}
                                </div>
                            </div>
                        @endif

                        <div class="team-details-info">
                            <div class="card background-card p-4 rounded-12 shadow-2">
                                <h5 class="text-xl-bold neutral-1000 mb-3">{{ __('Contact Information') }}</h5>
                                <div class="contact-info-list">
                                    @if ($phone = $team->phone)
                                        <div class="contact-item d-flex align-items-center mb-3">
                                            <div class="icon-shape icon-sm rounded-circle background-100 me-3">
                                                <x-core::icon name="ti ti-phone" class="text-primary" />
                                            </div>
                                            <div>
                                                <span class="text-sm-medium neutral-500 d-block">{{ __('Phone') }}</span>
                                                <a href="tel:{{ $phone }}" class="text-md-bold neutral-1000" dir="ltr">
                                                    {{ $phone }}
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($email = $team->email)
                                        <div class="contact-item d-flex align-items-center mb-3">
                                            <div class="icon-shape icon-sm rounded-circle background-100 me-3">
                                                <x-core::icon name="ti ti-mail" class="text-primary" />
                                            </div>
                                            <div>
                                                <span class="text-sm-medium neutral-500 d-block">{{ __('Email') }}</span>
                                                {!! Html::mailto($email, attributes: ['class' => 'text-md-bold neutral-1000', 'dir' => 'ltr']) !!}
                                            </div>
                                        </div>
                                    @endif

                                    @if ($address = $team->address)
                                        <div class="contact-item d-flex align-items-center mb-3">
                                            <div class="icon-shape icon-sm rounded-circle background-100 me-3">
                                                <x-core::icon name="ti ti-map-pin" class="text-primary" />
                                            </div>
                                            <div>
                                                <span class="text-sm-medium neutral-500 d-block">{{ __('Address') }}</span>
                                                <span class="text-md-bold neutral-1000">{{ $address }}</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                @if($team->socials && count($team->socials) > 0)
                                    <div class="social-links mt-4 pt-3 border-top">
                                        <h6 class="text-lg-bold neutral-1000 mb-3">{{ __('Follow Me') }}</h6>
                                        <div class="d-flex gap-2">
                                            @foreach($team->socials as $key => $social)
                                                <a href="{{ $social }}"
                                                   class="rounded-circle background-100 icon-shape icon-sm hover-up"
                                                   target="_blank"
                                                   rel="noopener noreferrer"
                                                   title="{{ ucfirst($key) }}">
                                                    <x-core::icon name="ti ti-brand-{{ $key }}" class="m-0" />
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="team-details-content">
                        <div class="mb-4">
                            <h1 class="heading-2 neutral-1000 mb-2">{{ $team->name }}</h1>
                            @if ($title = $team->title)
                                <span class="text-xl-medium text-primary d-block mb-3">{{ $title }}</span>
                            @endif

                            @if ($description = $team->description)
                                <p class="text-lg-medium neutral-700 mb-4">{{ $description }}</p>
                            @endif
                        </div>

                        @if($team->content)
                            <div class="team-content">
                                <div class="content-detail ck-content">
                                    {!! BaseHelper::clean($team->content) !!}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if(dynamic_sidebar('service_detail_bottom_sidebar'))
        <section class="section-box background-body border-top py-96">
            <div class="container">
                {!! dynamic_sidebar('service_detail_bottom_sidebar') !!}
            </div>
        </section>
    @endif
</div>
