@php
    $category = $post->categories->first();
    $author = $post->author;
    $viewsCount = $post->views ?? 0;
@endphp

<article class="card-news background-card border rounded-16 p-4 d-flex flex-column h-100 w-100 mb-4">
    <a href="{{ $post->url }}" class="d-block rounded-12 overflow-hidden mb-4">
        {{ RvMedia::image($post->image, $post->name, 'medium-rectangle', attributes: ['class' => 'w-100']) }}
    </a>

    @if ($category)
        <div class="mb-3">
            <span class="badge rounded-pill px-3 py-2 text-success-emphasis bg-success-subtle fw-medium">
                {{ $category->name }}
            </span>
        </div>
    @endif

    <h3 class="mb-3 fw-bold lh-sm" style="font-size: 22px;">
        <a href="{{ $post->url }}" class="neutral-1000 text-decoration-none" title="{{ $post->name }}">
            {!! BaseHelper::clean($post->name) !!}
        </a>
    </h3>

    @if ($description = $post->description)
        <p class="card-desc neutral-500 mb-4" style="font-size: 14px; line-height: 1.35; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
            {!! BaseHelper::clean($description) !!}
        </p>
    @endif

    <div class="d-flex align-items-center gap-4 neutral-700 mt-auto flex-wrap" style="font-size: 14px;">
        @if ($author)
            <span class="card-author d-inline-flex align-items-center gap-2">
                {{ RvMedia::image($author->avatar_url, $author->name, 'thumb', attributes: ['class' => 'author-avatar rounded-circle', 'width' => 24, 'height' => 24]) }}
                {{ $author->name }}
            </span>
        @endif

        <span class="d-inline-flex align-items-center gap-2">
            <i class="ti ti-calendar-event text-success"></i>
            {{ Theme::formatDate($post->created_at) }}
        </span>

        @if ($post->time_to_read)
            <span class="d-inline-flex align-items-center gap-2">
                <i class="ti ti-clock text-success"></i>
                <span class="post-time">{{ $post->time_to_read }} {{ __('minutes read') }}</span>
            </span>
        @endif

        <span class="d-inline-flex align-items-center gap-2">
            <i class="ti ti-eye text-success"></i>
            <span class="post-views">{{ __(':count Views', ['count' => number_format($viewsCount)]) }}</span>
        </span>
    </div>
</article>
