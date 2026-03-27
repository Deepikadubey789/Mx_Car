@php
    $searchQuery = e(BaseHelper::stringify(request()->input('q')));
    Theme::set('pageTitle', __('Search result for: ":query"', ['query' => $searchQuery]));
    $itemsPerRow = 3;
@endphp

@include(Theme::getThemeNamespace('views.loop'))
