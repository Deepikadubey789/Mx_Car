@php
    $linkNeedHelp = $shortcode->link_need_help;
    $top = $shortcode->top;
    $bottom = $shortcode->bottom;
    $left = $shortcode->left;
    $right = $shortcode->right;
    $url = $shortcode->url;
    $backgroundColor = $shortcode->background_color;

    $variablesStyle = [
        "--box-mt: {$top}px" => $top,
        "--box-mb: {$bottom}px" => $bottom,
        "--box-ml: {$left}px" => $left,
        "--box-mr: {$right}px" => $right,
        "background-color: transparent !important" => true,
        "--block-car-advance-search-background-color: transparent !important" => true,
    ];

    $selectedTabs = array_filter(explode(',', $shortcode->tabs ?? ''));

    $defaultTab = $shortcode->default_tab;

    $tabs = collect(['all' => __('All cars'), 'new_car' => __('New cars'), 'used_car' => __('Used cars')])
        ->reject(fn ($tab, $key) => ! in_array($key, $selectedTabs))
        ->sortBy(function ($tab, $key) use ($selectedTabs, $defaultTab) {
            if ($key === $defaultTab) {
                return -1;
            }

            return array_search($key, $selectedTabs);
        })
        ->all();

    if ($tabs && ! array_key_exists($type, $tabs)) {
        $type = $defaultTab && array_key_exists($defaultTab, $tabs)
            ? $defaultTab
            : array_key_first($tabs);
    }

    $isRentalEnabled = get_car_rentals_setting('enabled_car_rental', true);
    $carCategories = \Botble\CarRentals\Facades\CarListHelper::carCategoriesForFilter();
@endphp

<style>
    /* 1. Base Search Bar Styling */
    section.shortcode-car-advance-search,
    section.no-bg-override,
    section.box-search-advance-home10,
    #js-box-search-advance {
        background: transparent !important;
        background-color: transparent !important;
        border: none !important;
        padding: 0 !important;
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        position: relative !important;
        z-index: 50 !important;
    }

    .custom-search-box {
        background: #ffffff !important;
        border-radius: 16px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
        padding: 20px 30px !important;
        max-width: 1000px !important;
        margin-left: auto !important;
        margin-right: auto !important;
    }
    .form {
        margin-top: -180px; 
        position: relative;
        z-index: 100;
    }

    .custom-search-box .box-top-search {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        padding: 0 0 20px 0 !important;
        border-bottom: 1px solid #e5e7eb !important;
        margin-bottom: 25px !important;
    }
    .custom-search-box .left-top-search { display: flex !important; gap: 12px !important; }
    .custom-search-box .category-link {
        font-size: 0.95rem !important;
        font-weight: 600 !important;
        color: #6b7280 !important;
        padding: 10px 20px !important;
        border-radius: 20px !important;
        background: transparent !important;
        text-decoration: none !important;
        transition: all 0.3s ease;
    }
    .custom-search-box .category-link.active {
        color: #ffffff !important;
        background: #dc2626 !important;
        font-weight: 700 !important;
    }
    .custom-search-box .right-top-search { display: flex !important; align-items: center !important; }
    .custom-search-box .need-some-help { font-size: 0.9rem !important; color: #9ca3af !important; text-decoration: none !important; }

    /* 2. Flex Layout for Inputs */
    .custom-search-box .box-bottom-search {
        display: flex !important;
        align-items: flex-end !important;
        flex-wrap: nowrap !important; 
        width: 100% !important;
        gap: 15px !important;
    }
    .custom-search-box .item-search {
        flex: 1; padding: 0 !important; border: none !important; margin: 0 !important; min-width: 0 !important;
    }
    .custom-search-box .item-search:last-child {
        flex: 0 0 auto !important; min-width: 160px !important;
    }

    .custom-search-box .item-search label {
        font-size: 0.85rem !important; font-weight: 700 !important; color: #1f2937 !important; margin-bottom: 8px !important; display: block !important;
    }
    .custom-search-box .search-input {
        border: none !important; background: transparent !important; padding: 0 !important; font-size: 0.95rem !important; color: #6b7280 !important; box-shadow: none !important; width: 100% !important;
    }
    .custom-search-box .search-input:focus { outline: none !important; color: #1f2937 !important; }

    .custom-search-box .btn-brand-2 {
        background-color: #dc2626 !important; border: none !important; color: #ffffff !important; padding: 12px 28px !important; border-radius: 50px !important; font-weight: bold !important; font-size: 1.0rem !important; display: flex !important; align-items: center !important; justify-content: center !important; gap: 8px !important; width: 100% !important; height: 100% !important;
    }

    /* =========================================================
       ZOOMCAR-STYLE MODAL CSS
    ========================================================= */
    #locationModal .modal-content {
        border-radius: 16px;
        overflow: hidden;
    }
    #locationModal .modal-header {
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        padding: 16px 24px;
    }
    #locationModal .modal-title {
        font-weight: 700;
        font-size: 1.1rem;
        color: #1f2937;
    }
    #locationModal .search-wrapper {
        position: relative;
        padding: 16px 24px;
        border-bottom: 1px solid #f3f4f6;
    }
    #locationModal .search-wrapper input {
        width: 100%;
        padding: 12px 20px 12px 45px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 1rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    #locationModal .search-wrapper input:focus {
        border-color: #dc2626;
        outline: none;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
    }
    #locationModal .search-wrapper .search-icon {
        position: absolute;
        left: 40px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
    }
    
    #modalLocationResults {
        max-height: 400px;
        overflow-y: auto;
    }
    .modal-loc-item {
        display: flex;
        align-items: center;
        padding: 16px 24px;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
        transition: background 0.2s ease;
    }
    .modal-loc-item:hover {
        background-color: #fef2f2;
    }
    .modal-loc-item .loc-icon {
        background: #f3f4f6;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        color: #6b7280;
    }
    .modal-loc-item:hover .loc-icon {
        background: #fee2e2;
        color: #dc2626;
    }
    .modal-loc-item .loc-details {
        display: flex;
        flex-direction: column;
    }
    .modal-loc-item .loc-name {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.95rem;
    }
    .modal-loc-item .loc-sub {
        font-size: 0.8rem;
        color: #6b7280;
        margin-top: 2px;
    }

    /* Mobile */
    @media (max-width: 991px) {
        .custom-search-box .box-bottom-search { flex-direction: column !important; align-items: stretch !important; }
        .custom-search-box .item-search { border-bottom: 1px solid #e2e8f0 !important; padding: 15px 5px !important; width: 100% !important; }
        .custom-search-box .item-search:last-child { border-bottom: none !important; padding-top: 15px !important; }
    }

    /* Dark Mode */
    [data-bs-theme="dark"] .custom-search-box,
    [data-bs-theme="dark"] #locationModal .modal-content { background: #1b2736 !important; border-color: #30455d !important; }
    [data-bs-theme="dark"] #locationModal .modal-header,
    [data-bs-theme="dark"] #locationModal .search-wrapper,
    [data-bs-theme="dark"] .modal-loc-item { border-color: #30455d !important; }
    [data-bs-theme="dark"] #locationModal .modal-header { background: #151e2b; }
    [data-bs-theme="dark"] #locationModal .modal-title,
    [data-bs-theme="dark"] .modal-loc-item .loc-name { color: #e6eef8 !important; }
    [data-bs-theme="dark"] #locationModal .search-wrapper input { background: #151e2b; border-color: #30455d; color: #fff; }
    [data-bs-theme="dark"] .modal-loc-item:hover { background-color: #23354b; }
    [data-bs-theme="dark"] .modal-loc-item .loc-icon { background: #30455d; color: #a7bad0; }
</style>

<section {!! $shortcode->htmlAttributes(['style' => $variablesStyle]) !!} class="shortcode-car-advance-search box-section box-search-advance-home10 no-bg-override" id="js-box-search-advance">
    <div class="container">
        <form action="{{ $url }}" method="GET" class="form">
            <div class="custom-search-box wow fadeIn">
                <input value="{{ $type }}" name="adv_type" id="adv_type_input" hidden/>
                
                @if (count($tabs) > 1 || $shortcode->title || $linkNeedHelp)
                    <div class="box-top-search d-flex justify-content-between align-items-center">
                        <div class="left-top-search">
                            @php
                                $categoryLinkStyle = ['category-link', 'text-sm-bold', 'btn-click', 'filter-tab-btn'];
                            @endphp
                            @if (count($tabs) > 1)
                                @foreach($tabs as $key => $tab)
                                    <a @class([...$categoryLinkStyle, 'active' => $type === $key]) href="#" data-tab="{{ $key }}">{{ $tab }}</a>
                                @endforeach
                            @else
                                <h6 class="mb-0">{{ $shortcode->title }}</h6>
                            @endif
                        </div>
                        @if(empty($linkNeedHelp) === false)
                            <div class="right-top-search d-none d-md-flex">
                                <a class="text-sm-medium need-some-help text-muted text-decoration-none" href="{{ $linkNeedHelp }}">
                                    <x-core::icon name="ti ti-user" class="mb-1" size="12" /> {{ __('Need help?') }}
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
                
                @php $hasTopSearch = count($tabs) > 1 || $shortcode->title || $linkNeedHelp; @endphp
                
                <div @class(['box-bottom-search', 'mt-0' => ! $hasTopSearch])>
                    @if($isRentalEnabled && is_plugin_active('location'))
                        @php
                            $selectedLocation = is_string(request()->input('location')) ? request()->input('location') : '';
                            $selectedCityId = is_string(request()->input('city_id')) ? request()->input('city_id') : '';
                        @endphp
                        
                        <div class="item-search">
                            <label>{{ __('Where') }}</label>
                            <div class="position-relative">
                                <span class="position-absolute top-50 start-0 translate-middle-y" style="z-index: 10;">
                                    <!-- <img src="{{ Theme::asset()->url('images/icons/location.svg') }}" alt="Location" width="16" height="16" /> -->
                                </span>
                                
                                {{-- THE MODAL TRIGGER INPUT (Readonly to prevent mobile keyboard popping up) --}}
                                <input
                                    type="text"
                                    class="search-input ps-4"
                                    placeholder="{{ __('Select Location...') }}"
                                    value="{{ $selectedLocation }}"
                                    id="visual-location-input"
                                    autocomplete="off"
                                    readonly
                                    data-bs-toggle="modal" 
                                    data-bs-target="#locationModal"
                                    style="cursor: pointer;"
                                />
                                
                                {{-- Hidden inputs actually sent to the server --}}
                                <input type="hidden" name="location" id="hidden-location-name" value="{{ $selectedLocation }}">
                                <input type="hidden" name="city_id" id="hidden-city-id" value="{{ $selectedCityId }}">
                            </div>
                        </div>

                        <div class="item-search">
                            <label for="keyword">{{ __('What') }}</label>
                            <div class="position-relative">
                                <input type="text" name="keyword" id="keyword" class="search-input w-100" placeholder="{{ __('Car name or brand') }}" value="{{ is_string(request()->input('keyword')) ? request()->input('keyword') : '' }}" autocomplete="off">
                            </div>
                        </div>
                        
                        <div class="item-search">
                            <label for="date-range-picker-1">{{ __('From') }}</label>
                            <div class="position-relative">
                                <input type="text" class="search-input date-range-input w-100" id="date-range-picker-1" placeholder="{{ __('Add dates') }}" readonly />
                                <input type="hidden" name="start_date" id="input-start-date" value="{{ $pickUpDateDefault ?? '' }}" />
                                <input type="hidden" name="end_date" id="input-end-date" value="{{ $returnDateDefault ?? '' }}" />
                            </div>
                        </div>
                        
                        <div class="item-search">
                            <label for="date-range-picker-2">{{ __('Until') }}</label>
                            <div class="position-relative">
                                <input type="text" class="search-input date-range-input w-100" id="date-range-picker-2" placeholder="{{ __('Add dates') }}" readonly />
                            </div>
                        </div>
                    @else
                        <div class="item-search">
                            <label for="keyword">{{ __('What / Where') }}</label>
                            <div class="position-relative">
                                <input type="text" name="keyword" id="keyword" class="search-input w-100" placeholder="{{ __('Car name, city, or address') }}" value="{{ is_string(request()->input('keyword')) ? request()->input('keyword') : '' }}" autocomplete="off">
                            </div>
                        </div>
                        <div class="item-search">
                            <label for="category-select-alt-1">{{ __('From') }}</label>
                            <select name="car_categories[]" id="category-select-alt-1" class="search-input w-100">
                                <option value="">{{ __('Add dates') }}</option>
                                @foreach($carCategories as $category)
                                    <option value="{{ $category->id }}" @selected(in_array($category->id, (array) request()->input('car_categories', [])))>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="item-search">
                            <label for="category-select-alt-2">{{ __('Until') }}</label>
                            <select name="car_categories[]" id="category-select-alt-2" class="search-input w-100">
                                <option value="">{{ __('Add dates') }}</option>
                                @foreach($carCategories as $category)
                                    <option value="{{ $category->id }}" @selected(in_array($category->id, (array) request()->input('car_categories', [])))>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    
                    <div class="item-search">
                        <button class="btn btn-brand-2 text-nowrap" type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path><path d="M21 21l-6 -6"></path></svg>
                            <span class="btn-text">{{ __('Search') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

{{-- ZOOMCAR STYLE MODAL HTML --}}
<div class="modal fade" id="locationModal" tabindex="-1" aria-labelledby="locationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg">
            <div class="modal-header d-flex justify-content-between align-items-center">
                <h5 class="modal-title mb-0" id="locationModalLabel">{{ __('Select Pick-up Location') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="search-wrapper">
                <x-core::icon name="ti ti-search" class="search-icon" />
                <input type="text" id="modalLocationInput" placeholder="{{ __('Search for your city, state, or country...') }}" autocomplete="off">
            </div>
            <div id="modalLocationResults">
                <div class="p-4 text-center text-muted" id="modalInitialState">
                    <x-core::icon name="ti ti-map-pin" style="width: 40px; height: 40px; opacity: 0.5; margin-bottom: 10px;" />
                    <p class="mb-0">{{ __('Type a location above to see available cars.') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. TABS LOGIC ---
        const tabs = document.querySelectorAll('.filter-tab-btn');
        const hiddenInput = document.getElementById('adv_type_input');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                if(hiddenInput) hiddenInput.value = this.getAttribute('data-tab');
            });
        });

        // --- 2. MODAL SEARCH LOGIC ---
        const modalInput = document.getElementById('modalLocationInput');
        const resultsContainer = document.getElementById('modalLocationResults');
        const visualInput = document.getElementById('visual-location-input');
        const hiddenLocationName = document.getElementById('hidden-location-name');
        const hiddenCityId = document.getElementById('hidden-city-id');
        
        let searchTimeout = null;
        const searchUrl = '{{ route("public.ajax.locations") }}';

        // Auto-focus the input when the modal opens
        const locationModalEl = document.getElementById('locationModal');
        if(locationModalEl) {
            locationModalEl.addEventListener('shown.bs.modal', function () {
                modalInput.focus();
            });
        }

        modalInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                resultsContainer.innerHTML = `
                    <div class="p-4 text-center text-muted">
                        <p class="mb-0">{{ __('Type a location above to see available cars.') }}</p>
                    </div>`;
                return;
            }

            // Show a quick loading state
            resultsContainer.innerHTML = `
                <div class="p-4 text-center text-muted">
                    <div class="spinner-border spinner-border-sm text-danger me-2" role="status"></div>
                    <span>{{ __('Searching locations...') }}</span>
                </div>`;

            searchTimeout = setTimeout(() => {
                fetch(`${searchUrl}?location=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(res => {
                    resultsContainer.innerHTML = '';
                    
                    // --- CRITICAL FIX: Handle PHP Mixed-Key Objects ---
                    let dataArray = [];
                    if (res.data) {
                        if (Array.isArray(res.data)) {
                            dataArray = res.data;
                        } else if (res.data[0] && Array.isArray(res.data[0])) {
                            // Extracts the array from {"0": [...], "total": 1}
                            dataArray = res.data[0];
                        } else if (res.data.data && Array.isArray(res.data.data)) {
                            // Extracts from standard Laravel pagination {"data": [...]}
                            dataArray = res.data.data;
                        }
                    } else if (Array.isArray(res)) {
                        dataArray = res;
                    }

                    if (dataArray.length > 0) {
                        dataArray.forEach(item => {
                            const displayName = item.city_name || item.name;
                            let extraInfo = [item.state_name, item.country_name].filter(Boolean).join(', ');

                            // --- SMART ICON LOGIC ---
                            let iconSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0"></path><path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z"></path></svg>`; // Default Map Pin
                            
                            const lowerName = (item.name || '').toLowerCase();
                            if (lowerName.includes('airport')) {
                                // Airplane Icon
                                iconSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 10h4a2 2 0 0 1 0 4h-4l-4 7h-3l2 -7h-4l-2 2h-3l2 -4l-2 -4h3l2 2h4l-2 -7h3z"></path></svg>`;
                            } else if (lowerName.includes('hotel') || lowerName.includes('resort')) {
                                // Building/Hotel Icon
                                iconSvg = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21l18 0"></path><path d="M9 8l1 0"></path><path d="M9 12l1 0"></path><path d="M9 16l1 0"></path><path d="M14 8l1 0"></path><path d="M14 12l1 0"></path><path d="M14 16l1 0"></path><path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16"></path></svg>`;
                            }

                            const div = document.createElement('div');
                            div.className = 'modal-loc-item';
                            div.innerHTML = `
                                <div class="loc-icon">
                                    ${iconSvg}
                                </div>
                                <div class="loc-details">
                                    <span class="loc-name">${displayName}</span>
                                    ${extraInfo && extraInfo !== displayName ? `<span class="loc-sub">${extraInfo}</span>` : ''}
                                </div>
                            `;

                            // When clicked, update inputs and close modal
                            div.addEventListener('click', function() {
                                visualInput.value = item.name;
                                hiddenLocationName.value = item.name;
                                hiddenCityId.value = item.id;
                                
                                // Close the modal using the close button (works on all Bootstrap versions securely)
                                document.querySelector('#locationModal .btn-close').click();
                                
                                // Clear the modal search for next time
                                modalInput.value = '';
                                resultsContainer.innerHTML = `<div class="p-4 text-center text-muted"><p class="mb-0">{{ __('Type a location above to see available cars.') }}</p></div>`;
                            });

                            resultsContainer.appendChild(div);
                        });
                    } else {
                        resultsContainer.innerHTML = `
                            <div class="p-4 text-center text-muted">
                                <p class="mb-0">{{ __('No locations found with available cars.') }}</p>
                            </div>`;
                    }
                })
                .catch(err => {
                    console.error('Location fetch error:', err);
                    resultsContainer.innerHTML = `<div class="p-4 text-center text-danger">{{ __('Error fetching locations. Please try again.') }}</div>`;
                });
            }, 300);
        });
    });
</script>