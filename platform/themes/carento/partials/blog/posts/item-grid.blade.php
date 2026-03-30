@use(Theme\Carento\Support\ThemeHelper)

<div class="card-news card-news--journal card-news--journal-grid background-card hover-up mb-4">
    <a class="card-news__media-link" href="{{ $post->url }}">
        {{ RvMedia::image($post->image, $post->name, 'medium-rectangle') }}
        <span class="card-news__media-shade" aria-hidden="true"></span>
    </a>

    <div class="card-info">
        <div class="card-news__meta-row">
            {!! Theme::partial('blog.post-meta.category-badge', compact('post')) !!}

            {!! Theme::partial('blog.post-meta.index', compact('post')) !!}
        </div>

        <div class="card-title">
            <a class="text-xl-bold neutral-1000 truncate-2-custom" title="{{ $post->name }}" href="{{ $post->url }}">
                {{ $post->name }}
            </a>
        </div>

        @if ($description = $post->description)
            <div class="card-desc">
                <p class="text-md-medium neutral-500 truncate-2-custom">{!! BaseHelper::clean($description) !!}</p>
            </div>
        @endif

        <div class="card-program">
            <div class="endtime">
                @if (ThemeHelper::isShowPostMeta('list', 'author', true))
                    {!! Theme::partial('blog.post-meta.author', compact('post')) !!}
                @endif

                <div class="card-button"><a class="btn btn-gray" href="{{ $post->url }}">{{ __('Keep Reading') }}</a></div>
            </div>
        </div>
    </div>
</div>
