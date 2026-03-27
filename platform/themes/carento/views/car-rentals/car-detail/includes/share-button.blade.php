@if ($socials = \Botble\Theme\Supports\ThemeSupport::getSocialSharingButtons($car->url, SeoHelper::getDescription()))
    <div class="dropdown car-detail-share">
        <button class="btn btn-share btn btn-outline-secondary dropdown-toggle" type="button" id="shareDropdown" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
            <x-core::icon name="ti ti-share" size="xs" />
            {{ __('Share') }}
        </button>
        <ul class="dropdown-menu" aria-labelledby="shareDropdown">
            @foreach($socials as $social)
                @php
                    $name = Arr::get($social, 'name');
                    $backgroundColor = Arr::get($social, 'background_color');
                    $color = Arr::get($social, 'color');
                @endphp

                <li>
                    <a
                        aria-label="{{ __('Share on :social', ['social' => $name]) }}"
                        @style(["background-color: {$backgroundColor}" => $backgroundColor, "color: {$color}" => $color])
                        href="{{ Arr::get($social, 'url') }}"
                        target="_blank"
                        class="dropdown-item"
                    >
                        {!! Arr::get($social, 'icon') !!}
                        <span>{{ $name }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
