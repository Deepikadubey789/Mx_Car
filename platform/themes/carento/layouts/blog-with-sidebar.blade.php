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
                    <div class="blog-modern__hero">
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

                        <div class="blog-modern__hero-panel">
                            <p class="blog-modern__hero-panel-eyebrow text-xs-medium">{{ __('Inside This Feed') }}</p>
                            <ul class="blog-modern__hero-list">
                                <li>{{ __('Maintenance and ownership checklists') }}</li>
                                <li>{{ __('Rental comparisons and practical costs') }}</li>
                                <li>{{ __('Market trends and new driving stories') }}</li>
                            </ul>
                        </div>
                    </div>

                    <div class="row blog-modern__layout g-4 g-xl-5">
                        <div class="col-lg-8 blog-modern__content-col">
                            {!! Theme::content() !!}
                        </div>
                        <div class="col-lg-4 blog-modern__sidebar">
                            {!! dynamic_sidebar('blog_sidebar') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {!! Theme::get('afterContent') !!}
@endsection
