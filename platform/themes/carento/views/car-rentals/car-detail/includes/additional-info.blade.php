<div class="group-collapse-expand">
    <button class="btn btn-collapse" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAdditionalInfo" aria-expanded="true" aria-controls="collapseAdditionalInfo">
        <strong class="heading-6">{{ __('Additional Information') }}</strong>
        <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M1 1L6 6L11 1" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
        </svg>
    </button>

    <div class="collapse show" id="collapseAdditionalInfo">
        <div class="card card-body">
            <div class="row">
                @if($car->categories->isNotEmpty())
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="feature-image me-3">
                                {!! BaseHelper::renderIcon('ti ti-folder', attributes: ['class' => 'icon-category']) !!}
                            </div>
                            <div class="d-flex align-items-center flex-wrap">
                                <span class="text-sm-bold me-2">{{ __('Categories') }}:</span>
                                <div>
                                    @foreach($car->categories as $category)
                                        <span class="me-1 mb-1">{{ $category->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($car->colors->isNotEmpty())
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="feature-image me-3">
                                {!! BaseHelper::renderIcon('ti ti-palette', attributes: ['class' => 'icon-color']) !!}
                            </div>
                            <div class="d-flex align-items-center flex-wrap">
                                <span class="text-sm-bold me-2">{{ __('Colors') }}:</span>
                                <div>
                                    @foreach($car->colors as $color)
                                        <span class="me-1 mb-1">{{ $color->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($car->insurance_info)
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="feature-image me-3">
                                {!! BaseHelper::renderIcon('ti ti-shield-check', attributes: ['class' => 'icon-insurance']) !!}
                            </div>
                            <div class="d-flex align-items-center flex-wrap">
                                <span class="text-sm-bold me-2">{{ __('Insurance Info') }}:</span>
                                <span class="text-md-regular">{{ $car->insurance_info }}</span>
                            </div>
                        </div>
                    </div>
                @endif


                @if($car->tags->isNotEmpty())
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="feature-image me-3">
                                {!! BaseHelper::renderIcon('ti ti-tag', attributes: ['class' => 'icon-tag']) !!}
                            </div>
                            <div class="d-flex align-items-center flex-wrap">
                                <span class="text-sm-bold me-2">{{ __('Tags') }}:</span>
                                <div>
                                    @foreach($car->tags as $tag)
                                        <span class="me-1 mb-1">{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($car->year)
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="feature-image me-3">
                                {!! BaseHelper::renderIcon('ti ti-calendar', attributes: ['class' => 'icon-year']) !!}
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="text-sm-bold me-2">{{ __('Year') }}:</span>
                                <span class="text-md-regular">{{ $car->year }}</span>
                            </div>
                        </div>
                    </div>
                @endif

                @if($car->vin)
                    <div class="col-md-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="feature-image me-3">
                                {!! BaseHelper::renderIcon('ti ti-barcode', attributes: ['class' => 'icon-vin']) !!}
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="text-sm-bold me-2">{{ __('VIN') }}:</span>
                                <span class="text-md-regular">{{ $car->vin }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
