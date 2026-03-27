@if ($items = Theme::getSocialLinks())
    @if ($sidebar === 'off_canvas_sidebar')
        <div class="d-flex align-items-center mb-30">
            @if ($title = Arr::get($config, 'title'))
                <p class="text-lg-bold neutral-1000 d-inline-block mr-10">{{ $title }}</p>
            @endif

            <div class="box-socials-footer d-inline-block">
                @foreach($items as $item)
                    <a class="icon-socials neutral-1000" title="{{ $item->getName() }}" href="{{ $item->getUrl() }}" target="_blank">
                        {!! $item->getIconHtml() !!}
                    </a>
                @endforeach
            </div>
        </div>
    @else
        <div class="col-md-6 text-md-end text-center mb-20">
            <div class="d-flex align-items-center justify-content-center justify-content-md-end">
                @if ($title = Arr::get($config, 'title'))
                    <p class="text-lg-bold neutral-0 d-inline-block mr-10">{{ $title }}</p>
                @endif

                <div class="box-socials-footer d-inline-block">
                    @foreach($items as $item)
                        <a class="icon-socials" title="{{ $item->getName() }}" href="{{ $item->getUrl() }}" target="_blank">
                            {!! $item->getIconHtml() !!}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
@endif
