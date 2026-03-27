<style>
    [v-cloak],
    [x-cloak] {
        display: none;
    }
</style>

{!! BaseHelper::googleFonts('https://fonts.googleapis.com/' . sprintf(
        'css2?family=%s:wght@300;400;500;600;700&display=swap',
        urlencode(theme_option('tp_primary_font', 'Urbanist')),
)) !!}

<style>
    :root {
        --primary-font: "{{ theme_option('tp_primary_font', 'Urbanist') }}";
        --primary-color: {{ $primaryColor = theme_option('primary_color', '#70f46d') }};
        --primary-color-rgb: {{ implode(', ', BaseHelper::hexToRgb($primaryColor)) }};
        --secondary-color: {{ $secondaryColor = '#6c7a91' }};
        --secondary-color-rgb: {{ implode(', ', BaseHelper::hexToRgb($secondaryColor)) }};
        --heading-color: inherit;
        --text-color: {{ $textColor = '#182433' }};
        --text-color-rgb: {{ implode(', ', BaseHelper::hexToRgb($textColor)) }};
        --link-color: {{ $linkColor = theme_option('primary_color', '#70f46d') }};
        --link-color-rgb: {{ implode(', ', BaseHelper::hexToRgb($linkColor)) }};
        --link-hover-color: {{ $linkHoverColor = theme_option('primary_color_hover', '#5edd5b') }};
        --link-hover-color-rgb: {{ implode(', ', BaseHelper::hexToRgb($linkHoverColor)) }};
    }
</style>

{!! Assets::renderHeader(['core']) !!}
