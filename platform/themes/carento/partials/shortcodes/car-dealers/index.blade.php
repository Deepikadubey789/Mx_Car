@php
    $title = $shortcode->title;
    $subtitle = $shortcode->subtitle;
    $buttonLabel = $shortcode->button_label;
    $buttonUrl = $shortcode->button_url;
    $showCarCount = $shortcode->show_car_count !== 'no';

    $showPhone = ! get_car_rentals_setting('hide_owner_phone', false);
    $showEmail = ! get_car_rentals_setting('hide_owner_email', false);
@endphp

@if($dealers->isNotEmpty())
    <section {!! $shortcode->htmlAttributes() !!} class="section-car-dealers py-96 background-body border-top border-bottom shortcode-car-dealers">
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-xl-6 col-lg-7 col-md-9 col-sm-11">
                    <div class="text-center mb-5">
                        @if ($subtitle)
                            <span class="text-xl-medium shortcode-subtitle wow fadeInUp">{!! BaseHelper::clean($subtitle) !!}</span>
                        @endif

                        @if ($title)
                            <h2 class="heading-3 section-title shortcode-title wow fadeInUp">{!! BaseHelper::clean($title) !!}</h2>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row mt-50">
                @foreach($dealers as $dealer)
                    <div class="col-lg-3 col-md-6 col-12 mb-40 wow fadeIn" data-wow-delay="{{ $loop->index * 0.1 }}s">
                        <div class="card-news background-card hover-up shadow-2 h-100">
                            <div class="card-image text-center p-4">
                                @if($dealer->avatar_url)
                                    <div class="dealer-avatar-wrapper">
                                        <img src="{{ $dealer->avatar_url }}"
                                             alt="{{ $dealer->name }}"
                                             class="dealer-avatar rounded-circle"
                                             style="width: 120px; height: 120px; object-fit: cover;">
                                    </div>
                                @else
                                    <div class="dealer-avatar-placeholder rounded-circle mx-auto d-flex align-items-center justify-content-center background-100"
                                         style="width: 120px; height: 120px;">
                                        <svg width="50" height="50" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M20.5899 22C20.5899 18.13 16.7399 15 11.9999 15C7.25991 15 3.40991 18.13 3.40991 22" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                @endif

                            </div>

                            <div class="card-info p-4 pt-0">
                                <div class="card-title text-center mb-0">
                                    <h5 class="text-xl-bold neutral-1000 mb-2">
                                        {{ $dealer->name }}
                                        @if($dealer->is_verified && $dealer->badge)
                                            {!! $dealer->badge !!}
                                        @endif
                                    </h5>

                                    @if($showCarCount)
                                        <div class="car-count-badge">
                                            <span class="text-sm-medium neutral-500">
                                                <svg class="me-1" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M16 6L16 4C16 2.89543 15.1046 2 14 2L10 2C8.89543 2 8 2.89543 8 4L8 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                    <path d="M3 8.5C3 7.67157 3.67157 7 4.5 7H19.5C20.3284 7 21 7.67157 21 8.5V11C21 11.5523 20.5523 12 20 12H18.5C18.2239 12 18 12.2239 18 12.5V13C18 13.5523 17.5523 14 17 14H7C6.44772 14 6 13.5523 6 13V12.5C6 12.2239 5.77614 12 5.5 12H4C3.44772 12 3 11.5523 3 11V8.5Z" stroke="currentColor" stroke-width="1.5"/>
                                                    <path d="M7 14V18.5C7 19.3284 7.67157 20 8.5 20H9.5C10.3284 20 11 19.3284 11 18.5V18C11 17.4477 11.4477 17 12 17C12.5523 17 13 17.4477 13 18V18.5C13 19.3284 13.6716 20 14.5 20H15.5C16.3284 20 17 19.3284 17 18.5V14" stroke="currentColor" stroke-width="1.5"/>
                                                </svg>
                                                {{ $dealer->cars_count }} {{ $dealer->cars_count == 1 ? __('Car') : __('Cars') }}
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                @if(($dealer->email || $dealer->phone) && ($showPhone || $showEmail))
                                    <div class="dealer-contact-info text-center">
                                        @if($dealer->email && $showEmail)
                                            <p class="contact-item text-truncate mb-1" title="{{ $dealer->email }}">
                                                <a href="mailto:{{ $dealer->email }}" class="text-decoration-none">
                                                    {{ $dealer->email }}
                                                </a>
                                            </p>
                                        @endif

                                        @if($dealer->phone && $showPhone)
                                            <p class="contact-item mb-0">
                                                <a href="tel:{{ $dealer->phone }}" class="text-decoration-none">
                                                    {{ $dealer->phone }}
                                                </a>
                                            </p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($buttonLabel && $buttonUrl)
                <div class="row mt-5">
                    <div class="col-12 text-center">
                        <a href="{{ $buttonUrl }}" class="btn btn-primary wow fadeInUp">
                            {{ $buttonLabel }}
                            <svg class="ms-2" width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M8 15L15 8L8 1M15 8L1 8" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </section>
@else
    <section class="section-car-dealers py-96 background-body">
        <div class="container">
            @if($title || $subtitle)
                <div class="row align-items-center justify-content-center mb-5">
                    <div class="col-xl-6 col-lg-7 col-md-9 col-sm-11">
                        <div class="text-center">
                            @if ($subtitle)
                                <span class="text-xl-medium shortcode-subtitle">{!! BaseHelper::clean($subtitle) !!}</span>
                            @endif
                            @if ($title)
                                <h2 class="heading-3 section-title shortcode-title">{!! BaseHelper::clean($title) !!}</h2>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <svg class="mb-2" width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 8V12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M12 16H12.01" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <p class="mb-0 text-lg-medium">{{ __('No dealers found at the moment.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endif
