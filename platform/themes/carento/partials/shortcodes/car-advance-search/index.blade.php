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
        "background-color: $backgroundColor" => $backgroundColor,
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

<section {!! $shortcode->htmlAttributes(['style' => $variablesStyle]) !!} class="shortcode-car-advance-search box-section box-search-advance-home10" id="js-box-search-advance">
    <div class="container">
        <form action="{{ $url }}" method="GET">
            <div class="box-search-advance background-card wow fadeIn">
                <input value="{{ $type }}" name="adv_type" hidden/>
                @if (count($tabs) > 1 || $shortcode->title || $linkNeedHelp)
                    <div class="box-top-search">
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
                                <h6>{{ $shortcode->title }}</h6>
                            @endif
                        </div>
                        @if(empty($linkNeedHelp) === false)
                            <div class="right-top-search d-none d-md-flex">
                                <a class="text-sm-medium need-some-help" href="{{ $linkNeedHelp }}">
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
                <div @class(['box-bottom-search background-card', 'mt-0' => ! $hasTopSearch])>
                    @if($isRentalEnabled && is_plugin_active('location'))
                        @php
                            $selectedLocation = is_string(request()->input('location')) ? request()->input('location') : '';
                            $selectedCityId = is_string(request()->input('city_id')) ? request()->input('city_id') : '';
                        @endphp
                        <div class="item-search" data-bb-toggle="search-suggestion">
                            <label class="text-sm-bold neutral-500" for="location-input">{{ __('Location') }}</label>
                            <div class="position-relative">
                                <span class="position-absolute top-50 start-0 translate-middle-y" style="z-index: 10;">
                                    <img src="{{ Theme::asset()->url('images/icons/location.svg') }}" alt="Location" width="20" height="20" />
                                </span>
                                <input
                                    type="text"
                                    class="search-input location-autocomplete ps-4"
                                    id="location-input"
                                    placeholder="{{ __('Search for location...') }}"
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
                            <label class="text-sm-bold neutral-500" for="category-select">{{ __('Category') }}</label>
                            <select name="car_categories[]" id="category-select" class="search-input">
                                <option value="">{{ __('All Categories') }}</option>
                                @foreach($carCategories as $category)
                                    <option value="{{ $category->id }}"
                                        @selected(in_array($category->id, (array) request()->input('car_categories', [])))>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="item-search">
                            <label class="text-sm-bold neutral-500" for="date-range-picker">{{ __('Select Date') }}</label>
                            <div class="position-relative">
                                <input type="text" class="search-input date-range-input ps-4" id="date-range-picker" readonly />
                                <input type="hidden" name="start_date" id="input-start-date" value="{{ $pickUpDateDefault }}" />
                                <input type="hidden" name="end_date" id="input-end-date" value="{{ $returnDateDefault }}" />
                                <span class="position-absolute top-50 start-0 translate-middle-y" style="z-index: 10;">
                                    <img src="{{ Theme::asset()->url('images/icons/calendar-1.svg') }}" alt="Calendar" width="20" height="20" />
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="item-search" style="flex: 0 0 60%; max-width: 60%;">
                            <label class="text-sm-bold neutral-500" for="keyword">{{ __('Search') }}</label>
                            <div class="position-relative">
                                <span class="position-absolute top-50 start-0 translate-middle-y ms-3">
                                    <x-core::icon name="ti ti-search" />
                                </span>
                                <input
                                    type="text"
                                    name="keyword"
                                    id="keyword"
                                    class="search-input ps-5"
                                    placeholder="{{ __('Enter keywords to search cars...') }}"
                                    value="{{ is_string(request()->input('keyword')) ? request()->input('keyword') : '' }}"
                                >
                            </div>
                        </div>
                        <div class="item-search">
                            <label class="text-sm-bold neutral-500" for="category-select-alt">{{ __('Category') }}</label>
                            <select name="car_categories[]" id="category-select-alt" class="search-input">
                                <option value="">{{ __('All Categories') }}</option>
                                @foreach($carCategories as $category)
                                    <option value="{{ $category->id }}"
                                        @selected(in_array($category->id, (array) request()->input('car_categories', [])))>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                    <div class="item-search bd-none d-flex justify-content-end">
                        <button class="btn btn-brand-2 text-nowrap">
                            <x-core::icon name="ti ti-search" class="me-2" size="20" />
                            {{ __('Find a Vehicle') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

