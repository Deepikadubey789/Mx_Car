@php
    $col = BaseHelper::stringify(request()->query('col'));

    if (empty($col)) {
        $col = (int) ($layoutCol ?? 4);
    }

    $carUrl = $car->url;
    $query = [];

    // Only include rental date params if rental mode is enabled and car is not for sale
    if (CarRentalsHelper::isRentalBookingEnabled() && ! $car->is_for_sale) {
        if ($startDate = BaseHelper::stringify(request()->query('start_date'))) {
            $query['rental_start_date'] = $startDate;
        }

        if ($endDate = BaseHelper::stringify(request()->query('end_date'))) {
            $query['rental_end_date'] = $endDate;
        }
    }

    if ($query) {
        $carUrl = $car->url . '?' . http_build_query($query);
    }
@endphp

@once
<style>
    /* =========================================
       MODERN GRID CARD DESIGN
       ========================================= */
    .car-card-modern {
        background-color: #ffffff !important;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        border: 1px solid #e2e8f0 !important; 
    }
    .car-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1 !important; 
    }

    /* --- TALLER IMAGE & FIX FOR CUTOFF --- */
    .car-card-modern .img-wrap {
        position: relative;
        width: 100%;
        aspect-ratio: 4 / 3 !important;
        overflow: hidden;
        background: #111827;
    }
    .car-card-modern .img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center; 
        transition: transform 0.5s ease;
    }
    .car-card-modern:hover .img-wrap img {
        transform: scale(1.05);
    }

    /* Darker gradient for white text readability */
    .car-card-modern .img-wrap::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 75%;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.95) 0%, rgba(0, 0, 0, 0.4) 65%, transparent 100%);
        pointer-events: none;
    }

    .car-card-modern .favorite-btn {
        position: absolute;
        top: 15px !important;
        right: 15px !important;
        width: 32px;
        height: 32px;
        background: #ffffff; 
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #111827; 
        z-index: 10;
        transition: all 0.2s ease;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15); 
    }
    .car-card-modern .favorite-btn:hover {
        background: #ffffff;
        color: #ff4757;
        transform: scale(1.1); 
    }

    /* --- OVERLAY CONTENT (TITLE & SPECS) --- */
    .img-overlay-content {
        position: absolute;
        bottom: 15px;
        left: 15px;
        right: 15px;
        z-index: 2;
        display: flex;
        flex-direction: column;
    }
    .img-overlay-content .car-title {
        margin-bottom: 6px;
    }
    .img-overlay-content .car-title a {
        color: #ffffff !important;
        font-size: 1.25rem;
        font-weight: 800;
        text-shadow: 0 2px 4px rgba(0,0,0,0.8);
        text-decoration: none;
    }

    /* --- NEW: SCROLLING MARQUEE FOR TITLE --- */
    .title-scroll-wrapper {
        overflow: hidden;
        width: 100%;
        -webkit-mask-image: linear-gradient(to right, black 85%, transparent 100%);
        mask-image: linear-gradient(to right, black 85%, transparent 100%);
    }
    .title-scroll-text {
        display: inline-block;
        white-space: nowrap;
        padding-right: 20px;
    }
    @keyframes swipe-title-left {
        0%, 15% { transform: translateX(0); }
        85%, 100% { transform: translateX(calc(-100% + 220px)); } /* Wider offset since title spans full width */
    }
    .car-card-modern:hover .title-scroll-text {
        animation: swipe-title-left 4s linear infinite alternate;
    }

    .img-overlay-content .card-program > ul,
    .img-overlay-content .card-program > div {
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 6px !important;
        padding: 0 !important;
        margin: 0 !important;
        list-style: none !important;
    }
    
    .img-overlay-content .card-program li,
    .img-overlay-content .card-program div,
    .img-overlay-content .card-program span,
    .img-overlay-content .card-program p {
        color: #f8fafc !important; 
        font-size: 0.8rem !important;
        font-weight: 500 !important;
        display: flex !important;
        align-items: center !important;
        margin: 0 !important;
        text-shadow: 0 1px 3px rgba(0,0,0,0.8);
    }
    .img-overlay-content .card-program i,
    .img-overlay-content .card-program svg {
        display: none !important; 
    }
    .img-overlay-content .card-program li:not(:last-child)::after,
    .img-overlay-content .card-program > div > div:not(:last-child)::after {
        content: '•';
        margin-left: 6px;
        color: #94a3b8 !important;
    }

    /* --- BOTTOM DETAILS (PRICE, LOCATION, BUTTON) --- */
    .car-card-modern .card-details {
        padding: 20px 16px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        flex-grow: 1;
        background: transparent; 
    }
    
    .location-col {
        font-size: 0.85rem;
        color: #475569;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 6px;
        overflow: hidden;
        white-space: nowrap;
    }
    .location-col svg {
        flex-shrink: 0;
        width: 14px;
        height: 14px;
        fill: #111827; 
    }

    .location-scroll-wrapper {
        overflow: hidden;
        width: 100%;
        -webkit-mask-image: linear-gradient(to right, black 85%, transparent 100%);
        mask-image: linear-gradient(to right, black 85%, transparent 100%);
    }
    .location-scroll-text {
        display: inline-block;
        padding-right: 20px;
    }
    @keyframes swipe-left {
        0%, 15% { transform: translateX(0); }
        85%, 100% { transform: translateX(calc(-100% + 120px)); }
    }
    .car-card-modern:hover .location-scroll-text {
        animation: swipe-left 4s linear infinite alternate;
    }
    
    .price-col {
        text-align: right;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        flex-shrink: 0;
    }
    .price-amount {
        font-size: 1.15rem;
        font-weight: 800;
        color: #111827;
        margin-bottom: 2px;
        line-height: 1.2;
    }
    .price-amount * {
        margin: 0 !important;
        padding: 0 !important;
        font-size: inherit !important;
        font-weight: inherit !important;
        color: inherit !important;
        display: inline-block;
    }

    .price-excluding {
        font-size: 0.75rem;
        color: #94a3b8;
        text-decoration: underline; 
        text-underline-offset: 2px;
    }

    /* Expands the Book Now button across the card */
    .full-width-btn-wrap {
        width: 100%;
    }
    .full-width-btn-wrap a.btn {
        width: 100%;
        text-align: center;
        justify-content: center;
    }

    /* --- HOST FOOTER --- */
    .card-host-footer {
        padding: 12px 16px;
        background: #f8f9fa;
        border-top: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.75rem;
        color: var(--primary-color, #df4827) !important;
        font-weight: 600;
    }
    
    .host-avatar {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: var(--primary-color, #df4827) !important;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff !important;
    }
    .host-avatar svg {
        width: 12px;
        height: 12px;
        fill: currentColor;
    }
    
    .card-host-footer .host-name {
        color: var(--primary-color, #df4827) !important;
        font-weight: 800;
    }

    /* Dark Mode Support */
    html[data-bs-theme="dark"] .car-card-modern { background: #0f172a !important; border-color: #334155 !important; }
    html[data-bs-theme="dark"] .location-col, html[data-bs-theme="dark"] .price-col .price-amount { color: #f8fafc; }
    html[data-bs-theme="dark"] .location-col svg { fill: #f8fafc; }
    html[data-bs-theme="dark"] .card-host-footer { background: #1e293b; border-color: #334155; }
</style>
@endonce

<div class="col-lg-{{ $col }} col-md-6 mb-4">
    <article class="car-card-modern">
        <div class="img-wrap">
            <a href="{{ $carUrl }}">
                {{ RvMedia::image($car->image, $car->name, 'medium-rectangle') }}
            </a>
            
            <div class="favorite-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19.5 12.572l-7.5 7.428l-7.5 -7.428a5 5 0 1 1 7.5 -6.566a5 5 0 1 1 7.5 6.572" />
                </svg>
            </div>

            <div class="img-overlay-content">
                
                <div class="car-title">
                    <div class="title-scroll-wrapper">
                        <a href="{{ $carUrl }}" class="title-scroll-text" title="{!! BaseHelper::clean($car->name) !!}">
                            {!! BaseHelper::clean($car->name) !!}
                        </a>
                    </div>
                </div>
                
                <div class="card-program">
                    @include(Theme::getThemeNamespace('views.car-rentals.car-facilities'), ['car' => $car])
                </div>
            </div>
        </div>
        
        <div class="card-details">
            @php
                // Intelligently combine available location data
                $displayLocation = implode(', ', array_filter([
                    $car->address,
                    $car->city->name ?? null,
                    $car->state->name ?? null
                ]));
            @endphp
            
            <div class="d-flex justify-content-between align-items-center">
                <div class="location-col" style="max-width: 50%;" title="{{ $displayLocation }}">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/></svg>
                    <div class="location-scroll-wrapper">
                        <span class="location-scroll-text">
                            {{ $displayLocation ?: __('Location N/A') }}
                        </span>
                    </div>
                </div>
                    
                <div class="price-col">
                    <div class="price-amount">
                        @include(Theme::getThemeNamespace('views.car-rentals.price'), ['car' => $car])
                    </div>
                    <div class="price-excluding">{{ __('excluding fees') }}</div>
                </div>
            </div>

            <div class="full-width-btn-wrap mt-1">
                @include(Theme::getThemeNamespace('views.car-rentals.book-now-button'), ['car' => $car])
            </div>
        </div>

        <div class="card-host-footer">
            <div class="host-avatar">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </div>
            <span class="text-truncate">
                {{ __('By') }} <span class="host-name" style="text-transform: uppercase;">{{ $car->vendor->name ?? $car->author->name ?? __('Verified Host') }}</span> 
                @if(isset($car->vendor) && method_exists($car->vendor, 'cars') && $car->vendor->cars()->count() > 0)
                    | {{ __('Hosting') }} {{ $car->vendor->cars()->count() }} {{ __('cars') }}
                @endif
            </span>
        </div>
    </article>
</div>