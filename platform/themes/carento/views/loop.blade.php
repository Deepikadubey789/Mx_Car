@php
    $itemsPerRow = $itemsPerRow ?? null;
    $displayTitle = $displayTitle ?? false;
    $displayDescription = $displayDescription ?? false;
@endphp

<div>
    <div class="blog-loop-modern">
        @if (($displayTitle && ($title = theme_option('blog_post_list_page_title'))) || ($displayDescription && ($description = theme_option('blog_post_list_page_description'))))
            <div class="blog-loop-modern__intro">
                @if ($displayTitle && ($title ?? null))
                    <h2 class="neutral-1000">{{ $title }}</h2>
                @endif

                @if ($displayDescription && ($description ?? null))
                    <p class="text-xl-medium neutral-500">{!! $description !!}</p>
                @endif
            </div>
        @endif

        {!! apply_filters('ads_render', null, 'post_list_before', ['class' => 'mb-2']) !!}

        <div class="blog-loop-modern__posts">
            {!! Theme::partial('blog.posts', compact('posts', 'itemsPerRow')) !!}
        </div>

        @if ($posts instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator && $posts->total() > 0)
            <div class="car-list-pagination" style="
                margin-top: 20px;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                padding: 20px;
                background: #ffffff;
                display: flex;
                justify-content: center;
                gap: 30rem;
            ">
                {{ $posts->withQueryString()->links(Theme::getThemeNamespace('partials.pagination')) }}
            </div>
        @endif

        {!! apply_filters('ads_render', null, 'post_list_after', ['class' => 'mt-2']) !!}
    </div>
</div>