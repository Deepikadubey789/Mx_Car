@php
    $title = $shortcode->title;
    $subtitle = $shortcode->subtitle;
    $buttonLabel = $shortcode->button_label;
    $buttonUrl = $shortcode->button_url;
    $normalizedCurrentUrl = rtrim(url()->current(), '/');
    $normalizedButtonUrl = $buttonUrl ? rtrim(url($buttonUrl), '/') : null;
    $isSelfReferencingButton = $normalizedButtonUrl && $normalizedButtonUrl === $normalizedCurrentUrl;
    $isShowingAllDealers = request()->boolean('show_all_dealers');
    $resolvedButtonUrl = $buttonUrl;

    if ($isSelfReferencingButton) {
        $resolvedButtonUrl = request()->fullUrlWithQuery(['show_all_dealers' => 1]);
    }
    $showCarCount = $shortcode->show_car_count !== 'no';

    $showPhone = ! get_car_rentals_setting('hide_owner_phone', false);
    $showEmail = ! get_car_rentals_setting('hide_owner_email', false);
@endphp

<style>
/* MXCar Premium Dealer Layout Options */
.section-car-dealers {
    background-color: #FDFBF8 !important; /* Soft premium cream background */
}
.mxcar-dealer-card {
    background: #ffffff;
    border: 1px solid #E9ECEF;
    border-radius: 24px;
    padding: 24px;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    height: 100%;
    transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    position: relative;
    overflow: hidden;
}
@media (min-width: 768px) {
    .mxcar-dealer-card {
        flex-direction: row;
        align-items: flex-start;
        gap: 20px;
    }
}
.mxcar-dealer-card:hover {
    border-color: #E9ECEF;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.06);
    transform: none;
}

/* Avatar Styling */
.mxcar-dealer-avatar {
    flex-shrink: 0;
    width: 124px;
    height: 124px;
    border-radius: 20px; /* Squircle style instead of circle */
    overflow: hidden;
    background-color: #F8F9FA;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #E9ECEF;
}
.mxcar-dealer-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}
.mxcar-dealer-avatar.avatar-fallback {
    background: #f8f9fa;
}
.mxcar-dealer-avatar.avatar-fallback::before {
    content: attr(data-initial);
    font-size: 36px;
    font-weight: 700;
    color: #b03a2e;
}

/* Details Section */
.mxcar-dealer-details {
    flex-grow: 1;
    min-width: 0;
}
.mxcar-dealer-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: #111111;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
    line-height: 1.25;
}
.mxcar-dealer-contact {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: 10px;
}
@media (min-width: 992px) {
    .mxcar-dealer-contact {
        flex-direction: row;
        gap: 20px;
        flex-wrap: wrap;
    }
}
.mxcar-dealer-contact a {
    color: #6C757D;
    font-size: 0.95rem;
    display: flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: color 0.2s ease;
    max-width: 100%;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.mxcar-dealer-contact a:hover {
    color: #B03A2E;
}
.mxcar-dealer-contact svg {
    width: 16px;
    height: 16px;
    stroke: #adb5bd;
}

/* Add bottom spacing to the dealers grid row to separate from CTA */
.section-car-dealers .row.g-4.mt-20 {
    margin-bottom: 3rem;
}

.mxcar-car-count {
    background: rgba(176, 58, 46, 0.08);
    color: #B03A2E;
    font-weight: 700;
    font-size: 0.9rem;
    padding: 6px 16px;
    border-radius: 30px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

/* Dark Mode Overrides */
[data-bs-theme="dark"] .section-car-dealers {
    background-color: transparent !important;
}
[data-bs-theme="dark"] .mxcar-dealer-card {
    background-color: #1a1a1a;
    border-color: #2e2e2e;
}
[data-bs-theme="dark"] .mxcar-dealer-name {
    color: #f1f1f1;
}
[data-bs-theme="dark"] .mxcar-dealer-avatar {
    border-color: #2e2e2e;
    background-color: #242424;
}
</style>

@if($dealers->isNotEmpty())
    <section {!! $shortcode->htmlAttributes() !!} class="section-car-dealers py-96 border-top border-bottom shortcode-car-dealers">
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

            <!-- Redesigned Grid: 2 Columns instead of 4 -->
            <div class="row g-4 mt-20">
                @foreach($dealers as $dealer)
                    <div class="col-lg-6 col-md-12 wow fadeIn" data-wow-delay="{{ $loop->index * 0.1 }}s">
                        <div class="mxcar-dealer-card">
                            <!-- Left: Avatar -->
                            <div class="mxcar-dealer-avatar" data-initial="{{ strtoupper(mb_substr($dealer->name, 0, 1)) }}">
                                @if($dealer->avatar_url)
                                    <img src="{{ $dealer->avatar_url }}" alt="{{ $dealer->name }}" loading="lazy" onerror="this.style.display='none'; this.parentElement.classList.add('avatar-fallback');">
                                @else
                                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="#B03A2E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M20.5899 22C20.5899 18.13 16.7399 15 11.9999 15C7.25991 15 3.40991 18.13 3.40991 22" stroke="#B03A2E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                @endif
                            </div>

                            <!-- Middle: Details -->
                            <div class="mxcar-dealer-details">
                                <h5 class="mxcar-dealer-name">
                                    {{ $dealer->name }}
                                    @if($dealer->is_verified && $dealer->badge)
                                        {!! $dealer->badge !!}
                                    @endif
                                </h5>

                                @if($showCarCount)
                                    <div class="mxcar-car-count mt-2">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M16 6L16 4C16 2.89543 15.1046 2 14 2L10 2C8.89543 2 8 2.89543 8 4L8 6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                            <path d="M3 8.5C3 7.67157 3.67157 7 4.5 7H19.5C20.3284 7 21 7.67157 21 8.5V11C21 11.5523 20.5523 12 20 12H18.5C18.2239 12 18 12.2239 18 12.5V13C18 13.5523 17.5523 14 17 14H7C6.44772 14 6 13.5523 6 13V12.5C6 12.2239 5.77614 12 5.5 12H4C3.44772 12 3 11.5523 3 11V8.5Z" stroke="currentColor" stroke-width="1.5"/>
                                            <path d="M7 14V18.5C7 19.3284 7.67157 20 8.5 20H9.5C10.3284 20 11 19.3284 11 18.5V18C11 17.4477 11.4477 17 12 17C12.5523 17 13 17.4477 13 18V18.5C13 19.3284 13.6716 20 14.5 20H15.5C16.3284 20 17 19.3284 17 18.5V14" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                        {{ $dealer->cars_count }} {{ $dealer->cars_count == 1 ? __('Car') : __('Cars') }}
                                    </div>
                                @endif

                                @if(($dealer->email || $dealer->phone) && ($showPhone || $showEmail))
                                    <div class="mxcar-dealer-contact">
                                        @if($dealer->email && $showEmail)
                                            <a href="mailto:{{ $dealer->email }}" title="{{ $dealer->email }}">
                                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4 7.00005L10.2 11.65C11.2667 12.45 12.7333 12.45 13.8 11.65L20 7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                    <rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                </svg>
                                                {{ $dealer->email }}
                                            </a>
                                        @endif

                                        @if($dealer->phone && $showPhone)
                                            <a href="tel:{{ $dealer->phone }}">
                                                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5.10547 5C5.10547 4.44772 5.55318 4 6.10547 4H9.27856C9.72145 4 10.1136 4.28825 10.2312 4.7194L10.9855 7.48512C11.1278 8.00684 10.8845 8.55743 10.4045 8.79743L8.6045 9.69743C9.83733 12.4414 12.016 14.6548 14.7176 15.9082L15.5492 14.2449C15.7745 13.7944 16.3073 13.5654 16.808 13.6934L19.5042 14.3824C19.9575 14.4983 20.2811 14.9048 20.2811 15.3734V18.8945C20.2811 19.4468 19.8333 19.8945 19.2811 19.8945H18.1055C10.9258 19.8945 5.10547 14.0742 5.10547 6.89453V5Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                </svg>
                                                {{ $dealer->phone }}
                                            </a>
                                        @endif
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>
                @endforeach
            </div>

            @if($buttonLabel && $resolvedButtonUrl && ! $isShowingAllDealers)
                <div class="row mt-5" style="margin-top: 5rem;">
                    <div class="col-12 text-center">
                        <a href="{{ $resolvedButtonUrl }}" class="btn btn-primary" style="background-color: #B03A2E; border-color: #B03A2E; color: #fff !important;">
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
    <section class="section-car-dealers py-96">
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
