<section {!! $shortcode->htmlAttributes() !!} class="section-1 pt-96 pb-0 shortcode-faqs mxcar-faq-clean-section">
    <div class="container mxcar-faq-clean-container">
        <div class="row pb-50 align-items-end">
            <div class="col-lg-12 text-start">
                <div class="header-content d-flex flex-row align-items-center justify-content-between gap-4 flex-wrap pb-60">
                    @if($title = $shortcode->title)
                        <h2 class="heading-1 mxcar-page-title mb-0" style="font-weight: 700; line-height: 1.1; max-width: 500px; font-size: 64px;">
                            {!! BaseHelper::clean($title) !!}
                        </h2>
                    @endif
                    @if($description = $shortcode->description)
                        <div class="mxcar-page-desc-wrapper" style="max-width: 600px;">
                            <p class="mxcar-page-desc mb-0" style="font-size: 18px; line-height: 1.6; color: #4b5563; font-weight: 400;">
                                {!! nl2br(e($description)) !!}
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @if(count($tabs) > 0)
            @php
                $tabsChunk = array_chunk($tabs, 3);
            @endphp

            @foreach($tabsChunk as $tabList)
                <div class="row g-4 mb-4">
                    @foreach($tabList as $tab)
                        @php
                            $dataTitle = Arr::get($tab, 'data_title');
                            $dataNumber = Arr::get($tab, 'data_number');
                            $image = Arr::get($tab, 'image');
                        @endphp

                        @continue($loop->index > 1 || (! $dataTitle && ! $image))

                        <div class="col-lg-4 col-md-6">
                            <div class="box-image rounded-12 position-relative overflow-hidden">
                                @if($image)
                                    {!! RvMedia::image($image, $dataTitle, attributes: ['class' => 'rounded-12']) !!}
                                @endif

                                @if($dataNumber || $dataTitle)
                                    <div class="box-tag p-3 d-flex position-absolute bottom-0 end-0 rounded-12 m-3" style="background-color: var(--bs-color-white); color: var(--bs-neutral-1000);">
                                        @if($dataNumber)
                                            <span class="fs-72 me-3" style="color: var(--bs-neutral-1000);">{!! BaseHelper::clean($dataNumber) !!}</span>
                                        @endif

                                        @if ($dataTitle)
                                            <h6 style="color: var(--bs-neutral-1000);">
                                                {!! BaseHelper::clean($dataTitle) !!}
                                            </h6>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if(count($tabList) == 3)
                        @php
                            $lastDataTitle = Arr::get($tabList[2], 'data_title');
                            $lastDataNumber = Arr::get($tabList[2], 'data_number');
                        @endphp
                        <div class="col-lg-4 col-12">
                            <div class="box-tag p-5 d-flex flex-row align-items-center justify-content-center rounded-20 h-100" style="background-color: #cb462b; min-height: 300px;">
                                @if($lastDataNumber)
                                    <span class="fs-1 px-4 fw-800" style="color: #111827; font-size: 120px !important; line-height: 1;">{!! BaseHelper::clean($lastDataNumber) !!}</span>
                                @endif

                                @if($lastDataTitle)
                                    <h2 class="mb-0 fw-700" style="color: #000; font-size: 38px; line-height: 1.2; max-width: 180px;">
                                        {!! BaseHelper::clean($lastDataTitle) !!}
                                    </h2>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
</section>
