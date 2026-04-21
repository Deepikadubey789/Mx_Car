<div class="box-search-style-2 blog-search-widget mt-4">
    <form action="{{ route('public.search') }}">
        <label class="visually-hidden" for="blog-search-query">{{ __('Search posts') }}</label>
        <input id="blog-search-query" type="text" name="q" placeholder="{{ __('Search') }}" />
        <button class="btn-search-submit" type="submit" aria-label="{{ __('Search') }}"></button>
    </form>
</div>
