@extends(Theme::getThemeNamespace('layouts.base'))

@section('content')
    @php
        $blogTitle = theme_option('blog_post_list_page_title', __('Road Journal'));
        $blogDescription = theme_option('blog_post_list_page_description', __('Stories, practical guides, and updates from the road.'));
    @endphp

    @if(Theme::get('breadcrumbs', true))
        {!! Theme::partial('breadcrumbs') !!}
    @endif

    {!! Theme::get('beforeContent') !!}

    {!! dynamic_sidebar('above_blog_list_sidebar') !!}

    <section class="box-section background-body blog-modern blog-modern--journal">
        <div class="container">
            <div class="section-box background-body blog-modern__section py-96">
                <div class="container">
                    <div class="blog-modern__hero blog-modern__hero--full">
                        <div class="blog-modern__hero-main">
                            <p class="blog-modern__eyebrow text-xs-medium">{{ __('MxCar Journal') }}</p>
                            <h1 class="blog-modern__title">{{ $blogTitle }}</h1>

                            @if ($blogDescription)
                                <p class="blog-modern__subtitle">{!! BaseHelper::clean($blogDescription) !!}</p>
                            @endif

                            <div class="blog-modern__hero-pills">
                                <span class="blog-modern__hero-pill">{{ __('Car Insights') }}</span>
                                <span class="blog-modern__hero-pill">{{ __('Ownership Tips') }}</span>
                                <span class="blog-modern__hero-pill">{{ __('Rental Guides') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="row blog-modern__layout blog-modern__layout--full">
                        <div class="col-12 blog-modern__content-col">
                            {!! Theme::content() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {!! Theme::get('afterContent') !!}
@endsection
