@use(Theme\Carento\Support\ThemeHelper)

<div class="card-flight card-news card-news--journal card-news--journal-list background-card d-flex flex-column h-auto">
    <div class="card-image card-news__list-media">
        <a href="{{ $post->url }}">
            {{ RvMedia::image($post->image, $post->name, 'medium-square') }}
        </a>

        <span class="card-news__media-shade" aria-hidden="true"></span>
    </div>

    <div class="card-info p-2 d-flex flex-column">
        <div class="card-news__meta-row mb-1 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2" style="font-size: 13px; color: var(--bs-neutral-600);">
                {!! Theme::partial('blog.post-meta.index', compact('post')) !!}
            </div>
            {!! Theme::partial('blog.post-meta.category-badge', compact('post')) !!}
        </div>

        <div class="card-title mb-1">
            <a class="text-lg-bold neutral-1000 lh-sm" style="font-size: 16px; transition: color 0.3s; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $post->name }}" href="{{ $post->url }}">
                {{ $post->name }}
            </a>
        </div>

        @if ($description = $post->description)
            <div class="card-desc mb-1">
                <p class="neutral-500" style="font-size: 13px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin-bottom: 0;">{!! BaseHelper::clean($description) !!}</p>
            </div>
        @endif

        <div class="card-program mt-auto pt-2 border-top border-light">
            <div class="endtime d-flex align-items-center justify-content-between">
                @if (ThemeHelper::isShowPostMeta('list', 'author', true))
                    <div class="author-small">
                        {!! Theme::partial('blog.post-meta.author', compact('post')) !!}
                    </div>
                @endif

                <div class="card-button">
                    <a class="text-primary fw-bold text-decoration-none" href="{{ $post->url }}" style="font-size: 14px;">
                        {{ __('Read More') }} 
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ms-1"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
