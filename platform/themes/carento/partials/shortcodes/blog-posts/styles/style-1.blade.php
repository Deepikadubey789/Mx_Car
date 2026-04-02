@use(Theme\Carento\Support\ThemeHelper)
<style>
    .blog-list-style-1 {
        padding-top: 80px !important;
        padding-bottom: 80px !important;
        display: block !important;
    }
    .blog-list-style-1 .container {
        margin-top: 100px !important;
        margin-bottom: 100px !important;
    }
    .blog-list-style-1 .card-news {
        background: #ffffff !important;
        border-radius: 24px !important;
        overflow: hidden !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05) !important;
        height: 100% !important;
        display: flex !important;
        flex-direction: column !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease !important;
        border: none !important;
    }
    .blog-list-style-1 .card-news:hover {
        transform: translateY(-5px) !important;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    .blog-list-style-1 .card-image {
        position: relative !important;
        width: 100% !important;
        aspect-ratio: 1200 / 800 !important;
        overflow: hidden !important;
        z-index: 0 !important;
    }
    .blog-list-style-1 .card-image img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }
    .blog-list-style-1 .card-info {
        padding: 24px !important;
        flex-grow: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        position: relative !important;
        background: #ffffff !important;
        margin-top: -1px !important;
    }
    .blog-list-style-1 .category-badge-floating {
        position: absolute !important;
        top: -18px !important;
        right: 20px !important;
        background: #DCFCE7 !important;
        color: #111827 !important;
        padding: 6px 16px !important;
        border-radius: 12px !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        z-index: 20 !important;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05) !important;
    }
    .blog-list-style-1 .card-meta {
        display: flex !important;
        gap: 15px !important;
        font-size: 14px !important;
        color: #6B7280 !important;
        margin-bottom: 12px !important;
        position: relative !important;
        z-index: 10 !important;
        background: #ffffff !important;
    }
    .blog-list-style-1 .card-title {
        margin-bottom: 25px !important;
        flex-grow: 1 !important;
    }
    .blog-list-style-1 .card-title a {
        font-size: 22px !important;
        font-weight: 700 !important;
        color: #111827 !important;
        line-height: 1.3 !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
        overflow: hidden !important;
        text-decoration: none !important;
    }
    .blog-list-style-1 .card-footer-custom {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding-top: 15px !important;
        margin-top: auto !important;
    }
    .blog-list-style-1 .author-info {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
    }
    .blog-list-style-1 .author-avatar {
        width: 32px !important;
        height: 32px !important;
        border-radius: 50% !important;
        object-fit: cover !important;
        background: #E5E7EB !important;
    }
    .blog-list-style-1 .author-name {
        font-size: 15px !important;
        font-weight: 700 !important;
        color: #4B5563 !important;
        margin: 0 !important;
    }
    .blog-list-style-1 .keep-reading {
        font-size: 15px !important;
        font-weight: 700 !important;
        color: #111827 !important;
        text-decoration: none !important;
        transition: color 0.2s ease !important;
    }
    .blog-list-style-1 .keep-reading:hover {
        color: #CB462B !important;
    }
    
    @media (max-width: 768px) {
        .blog-list-style-1 .card-title a {
            font-size: 18px !important;
        }
        .blog-list-style-1 .card-info {
            padding: 20px !important;
        }
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-blog-posts blog-list-style-1 section-box box-news background-body">
    <div class="container">
        <div class="row align-items-end">
            <div class="col-md-9 mb-30 wow fadeInUp">
                @if(!empty($subtitle))
                    <span class="section-subtitle bg-brand-2 p-1 px-3 mb-2 d-inline-block rounded-pill text-white fw-bold" style="font-size: 13px; letter-spacing: 0.5px;">{!! BaseHelper::clean($subtitle) !!}</span>
                @endif
                @if(!empty($title))
                    <h2 class="heading-3 mb-15">{!! BaseHelper::clean($title) !!}</h2>
                @endif
            </div>
            <div class="col-md-3 position-relative mb-30 wow fadeInUp">
                <div class="box-button-slider box-button-slider-team justify-content-end">
                    <div class="swiper-button-prev swiper-button-prev-style-1 swiper-button-prev-2" tabindex="0" role="button" aria-label="Previous slide">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M7.99992 3.33325L3.33325 7.99992M3.33325 7.99992L7.99992 12.6666M3.33325 7.99992H12.6666" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </div>
                    <div class="swiper-button-next swiper-button-next-style-1 swiper-button-next-2" tabindex="0" role="button" aria-label="Next slide">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M7.99992 12.6666L12.6666 7.99992L7.99992 3.33325M12.6666 7.99992L3.33325 7.99992" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-list-news wow fadeInUp mt-5">
            <div class="box-swiper">
                <div class="swiper-container swiper-group-3">
                    <div class="swiper-wrapper">
                        @foreach($posts as $post)
                            @php
                                $author = $post->author;
                                $timeStr = $post->created_at ? Theme::formatDate($post->created_at) : null;
                                $category = $post->categories->first();
                            @endphp
                            <div class="swiper-slide h-auto pt-2 pb-2">
                                <div class="card-news">
                                    @if(!empty($post->image))
                                        <div class="card-image">
                                            <a href="{{ $post->url }}">
                                                {{ RvMedia::image($post->image, $post->name, 'medium-square') }}
                                            </a>
                                        </div>
                                    @endif
                                    <div class="card-info">
                                        @if($category)
                                            <span class="category-badge-floating">{{ $category->name }}</span>
                                        @endif
                                        
                                        <div class="card-meta">
                                            @if($timeStr)
                                                <span class="post-date">{{ $timeStr }}</span>
                                            @endif
                                            @if($post->time_to_read)
                                                <span class="post-time">{{ $post->time_to_read }} {{ __('mins') }}</span>
                                            @endif
                                        </div>

                                        <div class="card-title">
                                            <a href="{{ $post->url }}">{!! BaseHelper::clean($post->name) !!}</a>
                                        </div>

                                        <div class="card-footer-custom">
                                            <div class="author-info">
                                                @if($author)
                                                    {{ RvMedia::image($author->avatar_url, $author->name, attributes: ['class' => 'author-avatar']) }}
                                                    <p class="author-name">{{ $author->name }}</p>
                                                @endif
                                            </div>
                                            <a href="{{ $post->url }}" class="keep-reading">{{ __('Keep Reading') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
