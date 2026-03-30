@use(Theme\Carento\Support\ThemeHelper)
@use(Botble\Theme\Supports\Youtube)
@php
    $posts = $posts->chunk(3); 
    $playIconRaw = ThemeHelper::getPlayVideoIconBase64();
@endphp

{{-- FRONTEND CUSTOM CSS SCOPED ONLY TO THE BLOG SECTION --}}
<style>
    /* Section Spacing & Typography */
    .shortcode-blog-posts {
        padding-top: 4rem !important;
        padding-bottom: 5rem !important;
        background: transparent !important;
    }
    .shortcode-blog-posts .heading-3 {
        font-weight: 800 !important;
        color: #111827 !important;
        letter-spacing: -0.5px;
    }
    .shortcode-blog-posts .text-lg-medium {
        color: #6b7280 !important;
        font-weight: 500 !important;
    }

    /* The Blog Card Container - NOW OFF-WHITE */
    .blog-modern-card {
        background: #f8fafc !important; /* Subtle off-white background */
        border-radius: 16px !important; 
        border: 1px solid #e2e8f0 !important; /* Slightly darker border to frame the off-white */
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
        margin-bottom: 30px;
    }

    /* Hover Lift & Shadow */
    .blog-modern-card:hover {
        box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06) !important;
        transform: translateY(-4px);
        border-color: #cbd5e1 !important;
    }

    /* Image Wrapper */
    .blog-modern-card .card-image {
        position: relative;
        width: 100%;
        height: 220px; 
        overflow: hidden;
        background: #ffffff; /* Keeps image area clean if image doesn't fill perfectly */
    }
    
    .blog-modern-card .card-image img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important; 
        transition: transform 0.5s ease;
    }
    .blog-modern-card:hover .card-image img {
        transform: scale(1.05); 
    }

    /* Video Play Button Styling */
    .blog-modern-card .btn-play-modern {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 48px;
        height: 48px;
        background: rgba(255,255,255,0.9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        color: #d84a38; 
        transition: all 0.2s ease;
    }
    .blog-modern-card .btn-play-modern:hover {
        background: #ffffff;
        transform: translate(-50%, -50%) scale(1.1);
    }
    .blog-modern-card .btn-play-modern svg {
        margin-left: 2px; 
    }

    /* Card Content Area */
    .blog-modern-card .card-info {
        padding: 24px !important;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    /* Meta Data (Date / Category) */
    .blog-modern-card .card-meta {
        font-size: 0.85rem !important;
        color: #6b7280 !important;
        font-weight: 600 !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px !important;
    }

    /* Blog Title */
    .blog-modern-card .card-title {
        font-size: 1.25rem !important;
        font-weight: 700 !important;
        color: #111827 !important;
        margin-bottom: 15px !important;
        line-height: 1.4 !important;
    }
    .blog-modern-card .card-title a {
        color: inherit !important;
        text-decoration: none !important;
        transition: color 0.2s ease;
    }
    .blog-modern-card .card-title a:hover {
        color: #d84a38 !important;
    }

    /* Read More Link inside the card */
    .blog-modern-card .read-more {
        margin-top: auto !important; 
        display: inline-flex;
        align-items: center;
        font-size: 0.95rem;
        font-weight: 600;
        color: #d84a38 !important; 
        text-decoration: none;
        transition: opacity 0.2s;
    }
    .blog-modern-card .read-more svg {
        transition: transform 0.2s;
        margin-left: 4px;
    }
    .blog-modern-card .read-more:hover {
        opacity: 0.8;
    }
    .blog-modern-card .read-more:hover svg {
        transform: translateX(4px); 
    }
</style>

<section {!! $shortcode->htmlAttributes() !!} class="shortcode-blog-posts section-box box-picked background-body py-5">
    <div class="container">
        
        <div class="row align-items-end mb-4">
            <div class="col-md-9 wow fadeInUp">
                @if($shortcode->title)
                    <h2 class="heading-3">{!! BaseHelper::clean($shortcode->title) !!}</h2>
                @endif
                @if($shortcode->subtitle)
                    <p class="text-lg-medium mt-2">{!! BaseHelper::clean($shortcode->subtitle) !!}</p>
                @endif
            </div>

            @if (($linkUrl = $shortcode->link_url) && ($linkLabel = $shortcode->link_label))
                <div class="col-md-3 mt-md-0 mt-4 wow fadeInUp">
                    <div class="d-flex justify-content-md-end justify-content-start">
                        <a class="btn btn-primary" href="{{ $linkUrl }}">
                            {!! BaseHelper::clean($linkLabel) !!}
                            <x-core::icon name="ti ti-arrow-right" class="ms-2" size="16" />
                        </a>
                    </div>
                </div>
            @endif
        </div>

        @if($posts->isNotEmpty())
            <div class="row pt-20">
                @foreach($posts as $postChunk)
                    @foreach($postChunk as $post)
                        @php
                            $youtubeUrl = $post->getMetaData('youtube_video_url', true);
                            $youtubeId = $youtubeUrl ? Youtube::getYoutubeVideoID($youtubeUrl) : null;
                        @endphp
                        
                        <div class="col-lg-4 col-md-6 wow fadeIn" data-wow-delay="0.{{ $loop->index + 1 }}s">
                            
                            <div class="blog-modern-card">
                                
                                <div class="card-image">
                                    <a href="{{ $post->url }}">
                                        {{ RvMedia::image($post->image, $post->name, 'medium-rectangle') }}
                                    </a>
                                    
                                    @if ($youtubeId)
                                        <a class="btn-play-modern popup-youtube" href="https://www.youtube.com/watch?v={{ $youtubeId }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M7 4v16l13 -8z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                </div>
                                
                                <div class="card-info">
                                    
                                    <div class="card-meta">
                                        @if(ThemeHelper::isShowPostMeta('detail', 'published_date', true) && ($timeStr = $post->created_at ? Theme::formatDate($post->created_at) : null))
                                            {!! BaseHelper::clean($timeStr) !!}
                                        @endif
                                    </div>
                                    
                                    <div class="card-title">
                                        <a class="text-ellipsis-2-lines" title="{{ $post->name }}" href="{{ $post->url }}">
                                            {{ $post->name }}
                                        </a>
                                    </div>
                                    
                                    <a href="{{ $post->url }}" class="read-more">
                                        Read Article
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M13 18l6 -6" /><path d="M13 6l6 6" />
                                        </svg>
                                    </a>
                                    
                                </div>
                                
                            </div>
                            </div>
                    @endforeach
                @endforeach
            </div>
        @endif
        
    </div>
</section>