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

{{-- FRONTEND CUSTOM CSS SCOPED ONLY TO THE SEARCH BAR --}}
<style>
    /* 1. Positioning */
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


    /* 2. The Main Container */
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
    margin-top: -180px;  /* Covers roughly 30% of a 500px-600px hero */
    position: relative;
    z-index: 100;
}

    /* 3. The Top Tabs */
    .custom-search-box .box-top-search {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        padding: 0 0 20px 0 !important;
        border-bottom: 1px solid #e5e7eb !important;
        margin-bottom: 25px !important;
    }
    
    .custom-search-box .left-top-search {
        display: flex !important;
        gap: 12px !important;
    }
    
    .custom-search-box .category-link {
        font-size: 0.95rem !important;
        font-weight: 600 !important;
        color: #6b7280 !important;
        padding: 10px 20px !important;
        border-radius: 20px !important;
        background: transparent !important;
        text-decoration: none !important;
        transition: all 0.3s ease;
        border: none !important;
    }
    
    .custom-search-box .category-link.active {
        color: #ffffff !important;
        background: #dc2626 !important;
        font-weight: 700 !important;
    }
    
    .custom-search-box .category-link:hover {
        color: #374151 !important;
    }
    
    .custom-search-box .right-top-search {
        display: flex !important;
        align-items: center !important;
    }
    
    .custom-search-box .need-some-help {
        font-size: 0.9rem !important;
        color: #9ca3af !important;
        text-decoration: none !important;
    }
    
    .custom-search-box .need-some-help:hover {
        color: #6b7280 !important;
    }

    /* 4. Single-Row Flex Layout for Inputs */
    .custom-search-box .box-bottom-search {
        display: flex !important;
        align-items: flex-end !important;
        flex-wrap: nowrap !important; 
        width: 100% !important;
        gap: 15px !important;
    }

    .custom-search-box .item-search {
        flex: 1; 
        padding: 0 !important;
        border: none !important;
        margin: 0 !important;
        min-width: 0 !important;
    }

    /* 5. The Search Button Container */
    .custom-search-box .item-search:last-child {
        flex: 0 0 auto !important;
        min-width: 160px !important;
    }

    /* 6. Tidy up the labels and inputs */
    .custom-search-box .item-search label {
        font-size: 0.85rem !important;
        font-weight: 700 !important;
        text-transform: capitalize !important;
        color: #1f2937 !important; 
        margin-bottom: 8px !important;
        display: block !important;
    }

    .custom-search-box .search-input {
        border: none !important;
        background: transparent !important;
        padding: 0 !important;
        font-size: 0.95rem !important;
        color: #6b7280 !important;
        box-shadow: none !important;
        height: auto !important;
        width: 100% !important;
    }

    .custom-search-box .search-input:focus {
        outline: none !important;
        color: #1f2937 !important;
    }
    
    .custom-search-box .search-input::placeholder {
        color: #d1d5db !important;
    }

    .custom-search-box .position-relative span {
        display: none !important; 
    }
    
    .custom-search-box .ps-4 {
        padding-left: 0 !important; 
    }
    
    /* 7. Search Button Styling */
    .custom-search-box .btn-brand-2 {
        background-color: #dc2626 !important;
        border: none !important;
        color: #ffffff !important;
        padding: 12px 28px !important;
        border-radius: 16px !important;
        font-weight: 600 !important;
        font-size: 0.95rem !important;
        display: flex !important;
        align-items: center !important;
        gap: 8px !important;
        width: 100% !important;
        justify-content: center !important;
        transition: background-color 0.3s ease !important;
    }
    
    .custom-search-box .btn-brand-2:hover {
        background-color: #b91c1c !important;
    }
    
    .custom-search-box .btn-brand-2 .icon {
        width: 20px !important;
        height: 20px !important;
    }

    
    .custom-search-box .btn-brand-2 {
        border-radius: 50px !important; 
        border: none !important;
        padding: 10px 20px !important; 
        font-weight: bold !important;
        font-size: 1.0rem !important; 
        transition: background 0.2s ease;
        height: 100% !important;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white !important;
    }
    
    .custom-search-box .btn-brand-2 .btn-text {
        /* display: none;  */
    }
    .custom-search-box .btn-brand-2 .icon {
        margin-right: 0 !important;
    }

    /* 8. Mobile Responsiveness */
    @media (max-width: 991px) {
        section.shortcode-car-advance-search,
        #js-box-search-advance {
            margin-top: 1rem !important; 
        }
        .custom-search-box {
            border-radius: 1.5rem !important;
            padding: 15px !important;
        }
        .custom-search-box .box-top-search {
            flex-wrap: wrap;
            padding: 0 0 15px 0 !important;
        }
        .custom-search-box .category-link {
            margin-bottom: 10px !important;
        }
        .custom-search-box .box-bottom-search {
            flex-direction: column !important;
            align-items: stretch !important;
        }
        .custom-search-box .item-search {
            border-right: none !important;
            border-bottom: 1px solid #e2e8f0 !important;
            padding: 15px 5px !important;
            width: 100% !important;
        }
        .custom-search-box .item-search:nth-last-child(2) {
            border-bottom: none !important; 
        }
        .custom-search-box .item-search:last-child {
            border-bottom: none !important;
            padding-top: 15px !important;
        }
        .custom-search-box .btn-brand-2 {
            width: 100% !important;
            border-radius: 0.5rem !important;
        }
        .custom-search-box .btn-brand-2 .btn-text {
            display: inline-block; 
            margin-left: 8px;
        }
    }

    /* Dark Mode Overrides */
    [data-bs-theme="dark"] .custom-search-box {
        background: #1b2736 !important;
        border-bottom-color: #30455d !important;
        box-shadow: 0 12px 26px rgba(2, 7, 13, 0.5) !important;
    }

    [data-bs-theme="dark"] .custom-search-box .box-top-search {
        border-bottom-color: #30455d !important;
    }

    [data-bs-theme="dark"] .custom-search-box .category-link {
        color: #a7bad0 !important;
    }

    [data-bs-theme="dark"] .custom-search-box .category-link.active {
        background: #dc2626 !important;
        color: #ffffff !important;
    }

    [data-bs-theme="dark"] .custom-search-box .item-search label {
        color: #e6eef8 !important;
    }

    [data-bs-theme="dark"] .custom-search-box .search-input {
        color: #a7bad0 !important;
    }

    [data-bs-theme="dark"] .custom-search-box .search-input:focus {
        color: #e6eef8 !important;
    }

    [data-bs-theme="dark"] .custom-search-box .search-input::placeholder {
        color: #5a6f87 !important;
    }
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
                                $categoryLinkStyle = [
                                    'category-link',
                                    'text-sm-bold',
                                    'btn-click',
                                    'filter-tab-btn' // Custom class added for our JS hook
                                ];
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
                                    <x-core::icon name="ti ti-user" class="mb-1" size="12" />
                                    {{ __('Need help?') }}
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
                
                @php
                    $hasTopSearch = count($tabs) > 1 || $shortcode->title || $linkNeedHelp;
                @endphp
                
                <div @class(['box-bottom-search', 'mt-0' => ! $hasTopSearch])>
                    @if($isRentalEnabled && is_plugin_active('location'))
                        @php
                            $selectedLocation = is_string(request()->input('location')) ? request()->input('location') : '';
                            $selectedCityId = is_string(request()->input('city_id')) ? request()->input('city_id') : '';
                        @endphp
                        
                        <div class="item-search" data-bb-toggle="search-suggestion">
                            <label for="location-input">{{ __('Where') }}</label>
                            <div class="position-relative">
                                <span class="position-absolute top-50 start-0 translate-middle-y" style="z-index: 10;">
                                    <img src="{{ Theme::asset()->url('images/icons/location.svg') }}" alt="Location" width="16" height="16" />
                                </span>
                                <input
                                    type="text"
                                    class="search-input location-autocomplete ps-4"
                                    id="location-input"
                                    placeholder="{{ __('City, state, or country') }}"
                                    value="{{ $selectedLocation }}"
                                    name="location"
                                    data-url="{{ route('public.ajax.locations') }}" autocomplete="off"
                                />
                                <input type="hidden" name="city_id" id="city_id_hidden" value="{{ $selectedCityId }}">
                                <div class="location-suggestions" data-bb-toggle="data-suggestion"></div>
                            </div>
                        </div>

                        {{-- ADDED: Car Name / Keyword Search --}}
                        <div class="item-search">
                            <label for="keyword">{{ __('What') }}</label>
                            <div class="position-relative">
                                <input
                                    type="text"
                                    name="keyword"
                                    id="keyword"
                                    class="search-input w-100"
                                    placeholder="{{ __('Car name or brand') }}"
                                    value="{{ is_string(request()->input('keyword')) ? request()->input('keyword') : '' }}"
                                    autocomplete="off"
                                >
                            </div>
                        </div>
                        
                        <div class="item-search">
                            <label for="date-range-picker">{{ __('From') }}</label>
                            <div class="position-relative">
                                <input type="text" class="search-input date-range-input w-100" id="date-range-picker" placeholder="{{ __('Add dates') }}" readonly />
                                <input type="hidden" name="start_date" id="input-start-date" value="{{ $pickUpDateDefault ?? '' }}" />
                                <input type="hidden" name="end_date" id="input-end-date" value="{{ $returnDateDefault ?? '' }}" />
                            </div>
                        </div>
                        
                        <div class="item-search">
                            <label for="date-range-picker">{{ __('Until') }}</label>
                            <div class="position-relative">
                                <input type="text" class="search-input date-range-input w-100" id="date-range-picker" placeholder="{{ __('Add dates') }}" readonly />
                                <input type="hidden" name="start_date" id="input-start-date" value="{{ $pickUpDateDefault ?? '' }}" />
                                <input type="hidden" name="end_date" id="input-end-date" value="{{ $returnDateDefault ?? '' }}" />
                            </div>
                        </div>
                    @else
                        {{-- Fallback Search if Location/Rental plugin is off --}}
                        <div class="item-search">
                            <label for="keyword">{{ __('What / Where') }}</label>
                            <div class="position-relative">
                                <input
                                    type="text"
                                    name="keyword"
                                    id="keyword"
                                    class="search-input w-100"
                                    placeholder="{{ __('Car name, city, or address') }}"
                                    value="{{ is_string(request()->input('keyword')) ? request()->input('keyword') : '' }}"
                                    autocomplete="off"
                                >
                            </div>
                        </div>
                        <div class="item-search">
                            <label for="category-select-alt">{{ __('From') }}</label>
                            <select name="car_categories[]" id="category-select-alt" class="search-input w-100">
                                <option value="">{{ __('Add dates') }}</option>
                                @foreach($carCategories as $category)
                                    <option value="{{ $category->id }}"
                                        @selected(in_array($category->id, (array) request()->input('car_categories', [])))>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="item-search">
                            <label for="category-select-alt">{{ __('Until') }}</label>
                            <select name="car_categories[]" id="category-select-alt" class="search-input w-100">
                                <option value="">{{ __('Add dates') }}</option>
                                @foreach($carCategories as $category)
                                    <option value="{{ $category->id }}"
                                        @selected(in_array($category->id, (array) request()->input('car_categories', [])))>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    
                    <div class="item-search">
                        <button class="btn btn-brand-2 text-nowrap" type="submit">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                               <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                               <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path>
                               <path d="M21 21l-6 -6"></path>
                            </svg>
                            <span class="btn-text">{{ __('Search') }}</span>
                        </button>
                    </div>
                </div>
            </div>
            
        </form>
    </div>
</section>

{{-- SCRIPT TO ENSURE TABS UPDATE THE FILTER --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.filter-tab-btn');
        const hiddenInput = document.getElementById('adv_type_input');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Remove active class from all tabs
                tabs.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('active');
                
                // Update the hidden input value to be submitted with the form
                hiddenInput.value = this.getAttribute('data-tab');
            });
        });
    });
</script>