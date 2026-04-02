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

    <div class="container my-5">
        <div class="row blog-modern__layout blog-modern__layout--full">
                        <div class="col-12 blog-modern__content-col">
                            {!! Theme::content() !!}
                        </div>
                    </div>
    </div>

    {!! Theme::get('afterContent') !!}
@endsection
