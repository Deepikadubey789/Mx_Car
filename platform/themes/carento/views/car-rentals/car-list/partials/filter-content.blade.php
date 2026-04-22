@php
    $formId = $formId ?? 'cars-filter-form';
@endphp

<style>
    .car-filters-modern,
    .car-filters-shell,
    .sidebar-filter-mobile__content,
    .filter-section--desktop {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    /* Separate light-gray blocks (reference UI) */
    .filter-widget {
        background-color: #f4f5f7 !important;
        border: 1px solid #e6e8ec !important;
        border-radius: 18px !important;
        padding: 18px 18px 16px !important;
        margin-bottom: 18px !important;
        box-shadow: none !important;
    }

    .filter-widget::before,
    .filter-widget::after {
        display: none !important;
    }

    .filter-widget-header {
        border-bottom: 1px solid #d7dce3;
        padding-bottom: 10px;
        margin-bottom: 14px !important;
    }

    .filter-title {
        font-weight: 800;
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #0f172a;
        margin: 0;
    }

    .modern-search-group {
        border: 1px solid #d5dbe3 !important;
        border-radius: 12px !important;
        background: #ffffff;
        min-height: 46px;
        padding: 0 8px 0 10px;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .modern-search-group:focus-within {
        border-color: var(--primary-color, #0d6efd) !important;
        box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.12);
    }

    .modern-search-group__icon {
        display: inline-flex;
        color: #9aa5b5;
        font-size: 18px;
        margin-inline-end: 6px;
    }

    .modern-search-group input {
        border: none !important;
        box-shadow: none !important;
        background: transparent !important;
        font-size: 14px;
        color: #0f172a;
        padding: 10px 6px !important;
    }

    .modern-search-group input::placeholder {
        color: #94a3b8;
    }

    .modern-search-btn {
        border: 0 !important;
        background: transparent !important;
        color: #94a3b8 !important;
        padding: 6px !important;
        margin: 0 !important;
        border-radius: 8px !important;
    }

    .modern-search-btn:hover {
        color: var(--primary-color, #0d6efd) !important;
        background: rgba(13, 110, 253, 0.08) !important;
    }

    .form-select.submit-form-filter {
        border-radius: 12px !important;
        border: 1px solid #d5dbe3 !important;
        background-color: #ffffff !important;
        color: #0f172a !important;
        min-height: 46px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        box-shadow: none !important;
    }

    .custom-filter-label {
        background: #ffffff !important;
        border: 1px solid #d5dbe3 !important;
        border-radius: 12px !important;
        margin-bottom: 10px;
        min-height: 44px;
    }

    .custom-filter-label:hover {
        border-color: #c5ccd6 !important;
        box-shadow: none !important;
    }

    .custom-filter-label .filter-option-text,
    .custom-filter-label .fw-medium {
        color: #334155 !important;
        font-size: 14px;
        font-weight: 600;
    }

    .custom-filter-check input:checked + label {
        border-color: var(--primary-color, #0d6efd) !important;
        background: #f4f7ff !important;
        box-shadow: none !important;
    }

    .custom-filter-check input:checked + label .filter-option-text {
        color: var(--primary-color, #0d6efd) !important;
    }

    .custom-filter-check input:checked + label .filter-option-count {
        background: #eef2ff !important;
        color: var(--primary-color, #0d6efd) !important;
        border-color: rgba(13, 110, 253, 0.35) !important;
    }

    .filter-option-count {
        background: #f8fafc !important;
        border: 1px solid #d5dbe3 !important;
        color: #64748b !important;
    }

    .price-slider-wrapper .d-flex.justify-content-between,
    .mileage-slider-wrapper .d-flex.justify-content-between {
        background: #ffffff !important;
        border: 1px solid #d5dbe3 !important;
        border-radius: 12px !important;
    }

    .btn.filter-toggle-btn {
        border-radius: 12px !important;
        border: 1px solid #d5dbe3 !important;
        background: #ffffff !important;
        color: #475569 !important;
    }

    .ui-slider-range,
    .noUi-connect {
        background: var(--primary-color, #0d6efd) !important;
    }

    .ui-slider-handle,
    .noUi-handle {
        border: 2px solid var(--primary-color, #0d6efd) !important;
        background: #fff !important;
        border-radius: 50% !important;
    }

    [data-bs-theme="dark"] .filter-widget {
        background-color: #141c27 !important;
        border-color: #273244 !important;
    }

    [data-bs-theme="dark"] .filter-widget-header {
        border-bottom-color: #2f3b4f;
    }

    [data-bs-theme="dark"] .filter-title {
        color: #f1f5f9;
    }

    [data-bs-theme="dark"] .modern-search-group,
    [data-bs-theme="dark"] .form-select.submit-form-filter,
    [data-bs-theme="dark"] .custom-filter-label,
    [data-bs-theme="dark"] .price-slider-wrapper .d-flex.justify-content-between,
    [data-bs-theme="dark"] .mileage-slider-wrapper .d-flex.justify-content-between,
    [data-bs-theme="dark"] .btn.filter-toggle-btn {
        background: #0f172a !important;
        border-color: #334155 !important;
    }

    [data-bs-theme="dark"] .modern-search-group input,
    [data-bs-theme="dark"] .form-select.submit-form-filter,
    [data-bs-theme="dark"] .custom-filter-label .filter-option-text,
    [data-bs-theme="dark"] .custom-filter-label .fw-medium {
        color: #e2e8f0 !important;
    }
</style>

{{-- Location Filter --}}
@if(CarRentalsHelper::isEnabledFilterCarsBy('locations') && is_plugin_active('location'))
    @php
        $selectedLocation = BaseHelper::stringify(request()->input('location', ''));
        $selectedCityId = BaseHelper::stringify(request()->input('city_id', ''));
    @endphp
    <div class="filter-widget" style="overflow: visible !important;">
        <div class="filter-widget-header mb-0">
            <h6 class="filter-title">{{ __('Location') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="input-group modern-search-group position-relative d-flex align-items-center">
                <span class="modern-search-group__icon" aria-hidden="true">
                    <x-core::icon name="ti ti-map-pin" />
                </span>
                <input
                    type="text"
                    class="form-control custom-sidebar-loc-input px-0"
                    placeholder="{{ __('Search for location...') }}"
                    value="{{ $selectedLocation }}"
                    name="location"
                    form="{{ $formId }}"
                    data-url="{{ route('public.ajax.locations') }}"
                    autocomplete="off"
                />
                <button class="btn modern-search-btn" type="submit" form="{{ $formId }}">
                    <x-core::icon name="ti ti-search" />
                </button>
                <input type="hidden" name="city_id" class="sidebar-city-hidden-input" form="{{ $formId }}" value="{{ $selectedCityId }}">
            </div>
        </div>
    </div>
@endif

{{-- Keyword / Car Name Search Filter --}}
<div class="filter-widget">
    <div class="filter-widget-header mb-0">
        <h6 class="filter-title">{{ __('What') }}</h6>
    </div>
    <div class="filter-widget-content">
        <div class="input-group modern-search-group d-flex align-items-center">
            <span class="modern-search-group__icon" aria-hidden="true">
                <x-core::icon name="ti ti-car" />
            </span>
            <input 
                type="text" 
                name="keyword" 
                class="form-control px-0 py-2" 
                placeholder="{{ __('Car name or brand') }}" 
                value="{{ BaseHelper::clean(request()->input('keyword')) }}"
                form="{{ $formId }}"
            >
            <button class="btn modern-search-btn" type="submit" form="{{ $formId }}">
                <x-core::icon name="ti ti-search" />
            </button>
        </div>
    </div>
</div>

{{-- Vehicle Condition Filter --}}
@if(CarRentalsHelper::isEnabledFilterCarsBy('vehicle_condition'))
    <div class="filter-widget">
        <div class="filter-widget-header mb-0">
            <h6 class="filter-title">{{ __('Vehicle Condition') }}</h6>
        </div>
        <div class="filter-widget-content">
            <select name="adv_type" form="{{ $formId }}" class="form-select submit-form-filter w-100">
                @php
                    $advType = request()->input('adv_type', 'all');
                    $advType = is_string($advType) ? $advType : 'all';
                @endphp
                @foreach($advancedTypes as $type => $label)
                    <option @selected($advType === $type) value="{{ $type }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
    </div>
@endif

{{-- Rental Type Filter --}}
@if(!empty($rentalTypes) && CarRentalsHelper::isEnabledFilterCarsBy('rental_types') && CarRentalsHelper::isRentalBookingEnabled())
    <div class="filter-widget">
        <div class="filter-widget-header mb-0">
            <h6 class="filter-title">{{ __('Rental Period') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="filter-options-list">
                @foreach($rentalTypes as $type => $label)
                    <div class="filter-option">
                        <div class="custom-filter-check">
                            <input type="checkbox" class="form-check-input submit-form-filter d-none" value="{{ $type }}" name="rental_types[]" id="check-rental-type-{{ $type }}" form="{{ $formId }}" @checked(in_array($type, (array) request()->input('rental_types', [])))>
                            <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center p-2 px-3 cursor-pointer" for="check-rental-type-{{ $type }}">
                                <span class="filter-option-text fw-medium text-secondary">{{ $label }}</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Price Filter --}}
@if($carMaxRentalRate && CarRentalsHelper::isEnabledFilterCarsBy('prices'))
    <div class="filter-widget">
        <div class="filter-widget-header mb-0">
            <h6 class="filter-title">{{ __('Price Range') }}</h6>
        </div>
        <div class="filter-widget-content px-2">
            <div class="price-slider-wrapper">
                <div id="slider-range" class="mb-4 mt-2"
                     data-current-range="{{ request()->query('rental_rate_to') > 0 ? BaseHelper::stringify(request()->query('rental_rate_to')) : 0 }}"
                     data-max-rental-rate-range="{{ $carMaxRentalRate }}"
                     data-currency="{{ get_application_currency()?->title }}"
                     data-currency-rate="{{ get_application_currency()?->exchange_rate }}"
                ></div>
                <div class="d-flex justify-content-between align-items-center bg-light rounded-pill px-3 py-2 border">
                    <div class="fw-bold text-dark rental-rate-from">{{ format_price(0) }}</div>
                    <div class="text-muted small px-2">{{ __('to') }}</div>
                    <div class="fw-bold text-dark rental-rate-to">{{ format_price($carMaxRentalRate) }}</div>
                </div>
                <input class="input-disabled form-control submit-form-filter value-money" name="rental_rate_from" type="hidden" form="{{ $formId }}" value="{{ request()->query('rental_rate_from') > 0 ? BaseHelper::stringify(request()->query('rental_rate_from')) : 0 }}">
                <input class="input-disabled form-control submit-form-filter value-money" name="rental_rate_to" type="hidden" form="{{ $formId }}" value="{{ BaseHelper::stringify(request()->query('rental_rate_to', $carMaxRentalRate)) }}" data-default-value="{{ $carMaxRentalRate }}">
            </div>
        </div>
    </div>
@endif

{{-- Mileage Range Filter --}}
@if(isset($carMaxMileage) && $carMaxMileage > 0 && CarRentalsHelper::isEnabledFilterCarsBy('mileage'))
    <div class="filter-widget">
        <div class="filter-widget-header mb-0">
            <h6 class="filter-title">{{ __('Mileage') }}</h6>
        </div>
        <div class="filter-widget-content px-2">
            <div class="mileage-slider-wrapper">
                <div id="mileage-slider-range" class="mb-4 mt-2"
                     data-current-range="{{ request()->query('mileage_to') > 0 ? BaseHelper::stringify(request()->query('mileage_to')) : 0 }}"
                     data-max-mileage-range="{{ $carMaxMileage }}"
                ></div>
                <div class="d-flex justify-content-between align-items-center bg-light rounded-pill px-3 py-2 border">
                    <div class="fw-bold text-dark mileage-from">0 {{ __('mi') }}</div>
                    <div class="text-muted small px-2">{{ __('to') }}</div>
                    <div class="fw-bold text-dark mileage-to">{{ number_format($carMaxMileage) }} {{ __('mi') }}</div>
                </div>
                <input class="input-disabled form-control submit-form-filter" name="mileage_from" type="hidden" form="{{ $formId }}" value="{{ request()->query('mileage_from') > 0 ? BaseHelper::stringify(request()->query('mileage_from')) : 0 }}">
                <input class="input-disabled form-control submit-form-filter" name="mileage_to" type="hidden" form="{{ $formId }}" value="{{ BaseHelper::stringify(request()->query('mileage_to', $carMaxMileage)) }}" data-default-value="{{ $carMaxMileage }}">
            </div>
        </div>
    </div>
@endif

{{-- Car Make/Brand Filter --}}
@if(isset($carMakes) && $carMakes->isNotEmpty() && CarRentalsHelper::isEnabledFilterCarsBy('makes'))
    <div class="filter-widget">
        <div class="filter-widget-header mb-0">
            <h6 class="filter-title">{{ __('Brand') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="filter-options-list">
                @foreach($carMakes->take(5) as $carMake)
                    <div class="filter-option">
                        <div class="custom-filter-check">
                            <input type="checkbox" class="form-check-input submit-form-filter d-none" value="{{ $carMake->id }}" name="car_makes[]" id="check-car-make-{{ $carMake->id }}" form="{{ $formId }}" @checked(in_array($carMake->id, (array) request()->input('car_makes', [])))>
                            <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center p-2 px-3 cursor-pointer" for="check-car-make-{{ $carMake->id }}">
                                <span class="filter-option-text fw-medium text-secondary">{{ $carMake->name }}</span>
                                <span class="filter-option-count badge bg-white border text-muted rounded-pill">{{ $carMake->cars_count ?: 0 }}</span>
                            </label>
                        </div>
                    </div>
                @endforeach

                @if($carMakes->count() > 5)
                    <div class="filter-options-extra" style="display: none;">
                        @foreach($carMakes->skip(5) as $carMake)
                            <div class="filter-option">
                                <div class="custom-filter-check">
                                    <input type="checkbox" class="form-check-input submit-form-filter d-none" value="{{ $carMake->id }}" name="car_makes[]" id="check-car-make-{{ $carMake->id }}" form="{{ $formId }}" @checked(in_array($carMake->id, (array) request()->input('car_makes', [])))>
                                    <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center p-2 px-3 cursor-pointer" for="check-car-make-{{ $carMake->id }}">
                                        <span class="filter-option-text fw-medium text-secondary">{{ $carMake->name }}</span>
                                        <span class="filter-option-count badge bg-white border text-muted rounded-pill">{{ $carMake->cars_count ?: 0 }}</span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-light w-100 rounded-pill btn-sm mt-2 filter-toggle-btn text-muted fw-bold border" data-target=".filter-options-extra">
                        <span class="show-more-text"><x-core::icon name="ti ti-chevron-down" class="me-1" /> {{ __('Show more') }}</span>
                        <span class="show-less-text d-none"><x-core::icon name="ti ti-chevron-up" class="me-1" /> {{ __('Show less') }}</span>
                    </button>
                @endif
            </div>
        </div>
    </div>
@endif

{{-- Review Score Filter --}}
@if($carReviewScores->isNotEmpty() && CarRentalsHelper::isEnabledFilterCarsBy('review_scores'))
    <div class="filter-widget">
        <div class="filter-widget-header mb-0">
            <h6 class="filter-title">{{ __('Rating') }}</h6>
        </div>
        <div class="filter-widget-content">
            <div class="filter-options-list">
                @foreach($carReviewScores as $carReviewScore)
                    <div class="filter-option">
                        <div class="custom-filter-check">
                            <input type="checkbox" class="form-check-input submit-form-filter d-none" value="{{ $carReviewScore->star }}" name="car_review_scores[]" id="check-car-review-score-{{ $carReviewScore->star }}" form="{{ $formId }}" @checked(in_array($carReviewScore->star, (array) request()->input('car_review_scores', [])))>
                            <label class="form-check-label custom-filter-label d-flex justify-content-between align-items-center p-2 px-3 cursor-pointer" for="check-car-review-score-{{ $carReviewScore->star }}">
                                <div class="d-flex align-items-center">
                                    @include(Theme::getThemeNamespace('views.car-rentals.car-list.partials.review-scores'), ['score' => $carReviewScore->star])
                                </div>
                                <span class="filter-option-count badge bg-white border text-muted rounded-pill">{{ $carReviewScore->cars_count ?: 0 }}</span>
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

{{-- Host Badge Filter --}}
<div class="filter-widget">
    <div class="filter-widget-header mb-0">
        <h6 class="filter-title">{{ __('Host Quality') }}</h6>
    </div>
    <div class="filter-widget-content">
        @php $selectedBadge = request()->input('host_badge', ''); @endphp
        <div class="d-flex flex-column gap-2">
            <label class="custom-filter-label d-flex align-items-center gap-3 p-2 px-3 cursor-pointer m-0">
                <input type="radio" name="host_badge" class="form-check-input mt-0" form="{{ $formId }}" value="" {{ $selectedBadge === '' ? 'checked' : '' }} onchange="this.form.dispatchEvent(new Event('submit',{bubbles:true}))">
                <span class="fw-medium text-secondary">{{ __('All Hosts') }}</span>
            </label>
            <label class="custom-filter-label d-flex align-items-center gap-3 p-2 px-3 cursor-pointer m-0">
                <input type="radio" name="host_badge" class="form-check-input mt-0" form="{{ $formId }}" value="rising_star" {{ $selectedBadge === 'rising_star' ? 'checked' : '' }} onchange="this.form.dispatchEvent(new Event('submit',{bubbles:true}))">
                <span class="fw-medium text-secondary">🌟 {{ __('Rising Star') }}</span>
            </label>
            <label class="custom-filter-label d-flex align-items-center gap-3 p-2 px-3 cursor-pointer m-0">
                <input type="radio" name="host_badge" class="form-check-input mt-0" form="{{ $formId }}" value="top_host" {{ $selectedBadge === 'top_host' ? 'checked' : '' }} onchange="this.form.dispatchEvent(new Event('submit',{bubbles:true}))">
                <span class="fw-medium text-secondary">🏆 {{ __('Top Host') }}</span>
            </label>
            <label class="custom-filter-label d-flex align-items-center gap-3 p-2 px-3 cursor-pointer m-0">
                <input type="radio" name="host_badge" class="form-check-input mt-0" form="{{ $formId }}" value="all_star" {{ $selectedBadge === 'all_star' ? 'checked' : '' }} onchange="this.form.dispatchEvent(new Event('submit',{bubbles:true}))">
                <span class="fw-medium text-secondary">⭐ {{ __('All-Star Host') }}</span>
            </label>
        </div>
    </div>
</div>

<script>
    // To ensure this script only runs once even if the file is loaded multiple times via AJAX
    if (!window.sidebarLocationScriptLoaded) {
        window.sidebarLocationScriptLoaded = true;
        
        let sidebarDropdown = document.getElementById('custom-sidebar-location-dropdown');
        if (!sidebarDropdown) {
            sidebarDropdown = document.createElement('div');
            sidebarDropdown.id = 'custom-sidebar-location-dropdown';
            sidebarDropdown.style.cssText = `
                position: absolute;
                background: #ffffff;
                border: 1px solid #e5e7eb;
                border-radius: 16px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.1);
                z-index: 2147483647;
                max-height: 300px;
                overflow-y: auto;
                display: none;
                padding: 0.5rem;
            `;
            if(document.documentElement.getAttribute('data-bs-theme') === 'dark') {
                sidebarDropdown.style.background = '#1b2736';
                sidebarDropdown.style.borderColor = '#30455d';
            }
            document.body.appendChild(sidebarDropdown);
        }

        let sidebarLocationTimeout = null;
        let activeSidebarInput = null;

        function updateSidebarDropdownPosition() {
            if (!activeSidebarInput || sidebarDropdown.style.display === 'none') return;
            const rect = activeSidebarInput.closest('.modern-search-group').getBoundingClientRect();
            sidebarDropdown.style.top = (rect.bottom + window.scrollY + 8) + 'px';
            sidebarDropdown.style.left = (rect.left + window.scrollX) + 'px';
            sidebarDropdown.style.width = rect.width + 'px';
        }

        window.addEventListener('resize', updateSidebarDropdownPosition);
        window.addEventListener('scroll', updateSidebarDropdownPosition);

        document.addEventListener('input', function(e) {
            if (e.target && e.target.classList.contains('custom-sidebar-loc-input')) {
                activeSidebarInput = e.target;
                clearTimeout(sidebarLocationTimeout);
                
                const query = activeSidebarInput.value;
                const url = activeSidebarInput.getAttribute('data-url');

                if (!url || query.length < 2) {
                    sidebarDropdown.style.display = 'none';
                    return;
                }

                sidebarLocationTimeout = setTimeout(() => {
                    fetch(`${url}?location=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(res => {
                        sidebarDropdown.innerHTML = '';
                        let dataArray = [];
                        if (res.data && Array.isArray(res.data)) dataArray = res.data;
                        else if (res.data && res.data.data && Array.isArray(res.data.data)) dataArray = res.data.data;
                        else if (Array.isArray(res)) dataArray = res;

                        if (dataArray.length > 0) {
                            dataArray.forEach(item => {
                                const div = document.createElement('div');
                                div.className = 'location-suggestion-item';
                                div.style.cssText = `
                                    padding: 10px 16px;
                                    cursor: pointer;
                                    border-radius: 12px;
                                    color: #374151;
                                    font-size: 0.9rem;
                                    transition: background 0.1s;
                                    margin-bottom: 2px;
                                `;
                                
                                if(document.documentElement.getAttribute('data-bs-theme') === 'dark') {
                                    div.style.color = '#e6eef8';
                                }

                                div.onmouseover = () => { div.style.background = 'rgba(13, 110, 253, 0.08)'; div.style.color = 'var(--primary-color, #0d6efd)'; }
                                div.onmouseout = () => { div.style.background = 'transparent'; div.style.color = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? '#e6eef8' : '#374151'; }
                                
                                const displayName = item.city_name || item.name;
                                let extraInfo = '';
                                if (item.state_name) extraInfo += item.state_name;
                                if (item.country_name) extraInfo += (extraInfo ? ', ' : '') + item.country_name;

                                if (extraInfo && extraInfo !== displayName) {
                                    div.innerHTML = `<strong class="d-block">${displayName}</strong> <small class="text-muted">${extraInfo}</small>`;
                                } else {
                                    div.innerHTML = `<strong>${displayName}</strong>`;
                                }

                                div.onmousedown = function (ev) {
                                    ev.preventDefault(); 
                                    activeSidebarInput.value = item.name; 
                                    const hiddenId = activeSidebarInput.parentElement.querySelector('.sidebar-city-hidden-input');
                                    if (hiddenId) hiddenId.value = item.id;
                                    sidebarDropdown.style.display = 'none';
                                    
                                    const formId = activeSidebarInput.getAttribute('form');
                                    if(formId) {
                                        const form = document.getElementById(formId);
                                        if(form) form.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
                                    }
                                };
                                sidebarDropdown.appendChild(div);
                            });
                            
                            updateSidebarDropdownPosition();
                            sidebarDropdown.style.display = 'block';
                        } else {
                            sidebarDropdown.style.display = 'none';
                        }
                    })
                    .catch(err => console.error('Location fetch error:', err));
                }, 300);
            }
        });

        document.addEventListener('mousedown', function (e) {
            if (activeSidebarInput && !activeSidebarInput.closest('.modern-search-group').contains(e.target) && !sidebarDropdown.contains(e.target)) {
                sidebarDropdown.style.display = 'none';
            }
        });
    }
</script>