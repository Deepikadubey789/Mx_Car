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
        "background-color: transparent" => true, // Ensure no CMS backgrounds bleed through
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
    /* 1. Remove the outer CMS background entirely */
    section.shortcode-car-advance-search,
    #js-box-search-advance {
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        margin-top: 2rem !important; 
        margin-bottom: 2rem !important; 
    }

    /* 2. The Main Floating Pill Container (Turo Style) */
    .custom-search-box {
        background: #ffffff !important;
        border-radius: 60px !important; /* Heavy pill-shaped rounding */
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08) !important;
        padding: 10px 10px 10px 30px !important; /* Tight padding, less on the right so the button fits snug */
        max-width: 1000px !important; /* Keep it contained */
        margin-left: auto !important;
        margin-right: auto !important;
        position: relative;
        z-index: 20;
    }

    /* 3. Hide Top Tabs (If you want it to look EXACTLY like the screenshot, tabs are usually hidden or moved. 
          I am leaving them here but removing bottom borders so they don't break the pill shape) */
    .custom-search-box .box-top-search {
        display: none !important; /* UNCOMMENT this line if you want to completely hide the "All Cars" tabs */
        padding: 0 15px 10px 15px !important;
        border-bottom: none !important;
    }

    /* 4. Single-Row Flex Layout for Inputs */
    .custom-search-box .box-bottom-search {
        display: flex !important;
        align-items: center !important;
        flex-wrap: nowrap !important; /* CRITICAL: Forces a single line */
        width: 100% !important;
    }

    .custom-search-box .item-search {
        flex: 1; /* Inputs share available space equally */
        padding: 5px 20px !important;
        border-right: 1px solid #e2e8f0 !important; /* Vertical dividers */
        margin: 0 !important;
        min-width: 0; 
    }

    /* 5. The Search Button Container (Snug on the right) */
    .custom-search-box .item-search:last-child {
        border-right: none !important;
        flex: 0 0 auto !important; /* Takes only the space it needs */
        padding: 0 !important; /* Remove container padding */
        margin: 0 !important;
        border-top: none !important; /* Remove horizontal dividers */
    }

    /* 6. Tidy up the labels and inputs */
    .custom-search-box .item-search label {
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        color: #1e293b !important; /* Darker labels for contrast */
        margin-bottom: 2px;
        display: block;
    }

    .custom-search-box .search-input {
        border: none !important;
        background: transparent !important;
        padding: 0 !important;
        font-size: 1rem !important;
        color: #64748b !important;
        box-shadow: none !important;
        height: auto !important;
        width: 100% !important;
    }

    .custom-search-box .search-input:focus {
        outline: none !important;
    }

    /* Adjust icon placement */
    .custom-search-box .position-relative span {
        display: none !important; /* Hide input icons for a cleaner look like Turo */
    }
    .custom-search-box .ps-4 {
        padding-left: 0 !important; /* Remove padding since icons are hidden */
    }

    /* 7. The Square/Pill Search Button */
    .custom-search-box .btn-brand-2 {
        border-radius: 50px !important; /* Round the button to match the outer pill */
        border: none !important;
        padding: 15px 30px !important; 
        font-weight: 600 !important;
        font-size: 1rem !important; 
        transition: background 0.2s ease;
        height: 100% !important;
        display: flex;
        justify-content: center;
        align-items: center;
        color: white !important;
    }

    
    /* Optional: Hide text and only show icon inside button if you want it exactly like the screenshot */
    .custom-search-box .btn-brand-2 .btn-text {
        display: none; 
    }
    .custom-search-box .btn-brand-2 .icon {
        margin-right: 0 !important;
    }

    /* 8. Mobile Responsiveness */
    @media (max-width: 991px) {
        .custom-search-box {
            border-radius: 1rem !important;
            padding: 15px !important;
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
            display: inline-block; /* Show text on mobile */
            margin-left: 8px;
        }
    }
</style>

<section {!! $shortcode->htmlAttributes(['style' => $variablesStyle]) !!} class="shortcode-car-advance-search box-section box-search-advance-home10" id="js-box-search-advance">
    <div class="container">
        <form action="{{ $url }}" method="GET">
            
            <div class="custom-search-box wow fadeIn">
                <input value="{{ $type }}" name="adv_type" hidden/>
                
                @if (count($tabs) > 1 || $shortcode->title || $linkNeedHelp)
                    <div class="box-top-search d-flex justify-content-between align-items-center">
                        <div class="left-top-search">
                            @php
                                $categoryLinkStyle = [
                                    'category-link',
                                    'text-sm-bold',
                                    'btn-click'
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
                                    placeholder="{{ __('City, airport, address or hotel') }}"
                                    value="{{ $selectedLocation }}"
                                    name="location"
                                    data-url="{{ route('public.ajax.cities') }}"
                                    autocomplete="off"
                                />
                                <input type="hidden" name="city_id" id="city_id_hidden" value="{{ $selectedCityId }}">
                                <div class="location-suggestions" data-bb-toggle="data-suggestion"></div>
                            </div>
                        </div>
                        
                        <div class="item-search">
                            <label for="category-select">{{ __('From') }}</label>
                            <div class="position-relative">
                                <input type="text" class="search-input date-range-input w-100" id="date-range-picker" placeholder="{{ __('Add dates') }}" readonly />
                                <input type="hidden" name="start_date" id="input-start-date" value="{{ $pickUpDateDefault }}" />
                                <input type="hidden" name="end_date" id="input-end-date" value="{{ $returnDateDefault }}" />
                            </div>
                        </div>
                        
                        <div class="item-search">
                            <label for="date-range-picker">{{ __('Until') }}</label>
                            <div class="position-relative">
                                <input type="text" class="search-input date-range-input w-100" id="date-range-picker" placeholder="{{ __('Add dates') }}" readonly />
                                <input type="hidden" name="start_date" id="input-start-date" value="{{ $pickUpDateDefault }}" />
                                <input type="hidden" name="end_date" id="input-end-date" value="{{ $returnDateDefault }}" />
                            </div>
                        </div>
                    @else
                        {{-- Fallback Search if Location/Rental plugin is off --}}
                        <div class="item-search">
                            <label for="keyword">{{ __('Where') }}</label>
                            <div class="position-relative">
                                <input
                                    type="text"
                                    name="keyword"
                                    id="keyword"
                                    class="search-input w-100"
                                    placeholder="{{ __('City, airport, address or hotel') }}"
                                    value="{{ is_string(request()->input('keyword')) ? request()->input('keyword') : '' }}"
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