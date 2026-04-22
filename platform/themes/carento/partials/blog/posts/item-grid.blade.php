@php
    $categories = $post->categories;
    $author = $post->author;
    $firstCategory = $categories->first();
@endphp

<article class="blog-card-modern h-100 w-100 d-flex flex-column">
    <div class="blog-card-modern__media-wrap">
        <a href="{{ $post->url }}" class="blog-card-modern__image-link">
            <div class="blog-card-modern__media">
                {{ RvMedia::image($post->image, $post->name, 'large', attributes: ['class' => 'blog-card-modern__img']) }}
            </div>
        </a>

        @if ($firstCategory)
            <a href="{{ $firstCategory->url }}" class="blog-card-modern__category">
                {{ $firstCategory->name }}
            </a>
        @endif
    </div>

    <div class="blog-card-modern__body flex-grow-1 d-flex flex-column">
        <div class="blog-card-modern__date text-md-medium">
            <i class="ti ti-calendar-event blog-card-modern__date-icon" aria-hidden="true"></i>
            {{ Theme::formatDate($post->created_at) }}
        </div>

        <h3 class="blog-card-modern__title">
            <a href="{{ $post->url }}" class="blog-card-modern__title-link" title="{{ $post->name }}">
                {!! BaseHelper::clean($post->name) !!}
            </a>
        </h3>

        {{-- Use div, not <footer>: global theme `footer { background… !important }` targets all footer elements --}}
        <div class="blog-card-modern__footer mt-auto">
            @if ($author)
                <div class="blog-card-modern__author">
                    {{ RvMedia::image($author->avatar_url, $author->name, 'thumb', attributes: ['class' => 'blog-card-modern__avatar rounded-circle', 'width' => 36, 'height' => 36]) }}
                    <span class="blog-card-modern__author-name">{{ $author->name }}</span>
                </div>
            @endif

            <a href="{{ $post->url }}" class="blog-card-modern__read-more @unless($author) blog-card-modern__read-more--solo @endunless">
                {{ __('Keep Reading') }}
            </a>
        </div>
    </div>
</article>
