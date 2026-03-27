@if (is_plugin_active('language'))
    @php
        $supportedLocales = Language::getSupportedLocales();

        if (empty($options)) {
            $options = [
                'before' => '',
                'lang_flag' => true,
                'lang_name' => true,
                'class' => '',
                'after' => '',
            ];
        }
    @endphp

    @if ($supportedLocales && count($supportedLocales) > 1)
        @php
            $languageDisplay = setting('language_display', 'all');
            $showRelated = setting('language_show_default_item_if_current_version_not_existed', true);
            $currentLocale = Language::getCurrentLocale();
        @endphp

        <div>{{ __('Language') }}</div>
        <div class="language-list-mobile">
            @foreach ($supportedLocales as $localeCode => $properties)
                <a class="language-item text-sm-medium {{ $localeCode === $currentLocale ? 'active' : '' }}" 
                   href="{{ $showRelated ? Language::getLocalizedURL($localeCode) : url($localeCode) }}">
                    @if (Arr::get($options, 'lang_flag', true) && ($languageDisplay == 'all' || $languageDisplay == 'flag'))
                        <span class="language-flag">{!! language_flag($properties['lang_flag'], $properties['lang_name']) !!}</span>
                    @endif
                    @if (Arr::get($options, 'lang_name', true) && ($languageDisplay == 'all' || $languageDisplay == 'name'))
                        <span class="language-name">{{ $properties['lang_name'] }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    @endif
@endif
