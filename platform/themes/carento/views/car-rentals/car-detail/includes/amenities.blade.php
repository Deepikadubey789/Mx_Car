@if($car->amenities->isNotEmpty())
    <div class="group-collapse-expand">
        <button class="btn btn-collapse" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAmenities" aria-expanded="true" aria-controls="collapseAmenities">
            <strong class="heading-6">{{ __('Accessories') }}</strong>
            <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1 1L6 6L11 1" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
        </button>

        <div class="collapse show" id="collapseAmenities">
            <div class="card card-body">
                <div class="row">
                    @php
                        $amenitiesByCategory = $car->amenities->groupBy(function($amenity) {
                            return $amenity->category ? $amenity->category->name : __('Others');
                        });
                        $hasMultipleCategories = $amenitiesByCategory->count() > 1;
                    @endphp

                    @if($hasMultipleCategories)
                        @foreach($amenitiesByCategory as $categoryName => $categoryAmenities)
                            @php
                                $category = $categoryAmenities->first()->category;
                                $categoryIcon = $category && $category->icon ? $category->icon : 'ti ti-tag';
                            @endphp
                            <div class="mb-4">
                                <div class="mb-2">
                                    <h4 class="h5">
                                        {!! BaseHelper::renderIcon($categoryIcon, attributes: ['class' => 'icon-category']) !!}
                                        <span>{{ $categoryName }}</span>
                                    </h4>
                                </div>
                                <div class="box-feature">
                                    <ul>
                                        @foreach($categoryAmenities as $amenity)
                                            <li>
                                                @if($amenity->icon)
                                                    {!! BaseHelper::renderIcon($amenity->icon) !!}
                                                @else
                                                    {!! BaseHelper::renderIcon('ti ti-square-check') !!}
                                                @endif
                                                {{ $amenity->name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="mb-3">
                            <div class="box-feature">
                                <ul class="p-0">
                                    @foreach($car->amenities as $amenity)
                                        <li>
                                            @if($amenity->icon)
                                                {!! BaseHelper::renderIcon($amenity->icon) !!}
                                            @else
                                                {!! BaseHelper::renderIcon('ti ti-square-check') !!}
                                            @endif
                                            {{ $amenity->name }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
