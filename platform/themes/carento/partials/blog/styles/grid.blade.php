@php
    $itemsPerRow = $itemsPerRow ?: theme_option('blog_post_gird_items_per_row', 2);
@endphp

<div class="row blog-posts-modern blog-posts-modern--grid mt-60 mb-50 g-4">
    @foreach($posts as $post)
        <div @class([
            'blog-posts-modern__item',
            'd-flex',
            'col-lg-4 col-md-6 col-12' => $itemsPerRow == 3,
            'col-lg-6 col-12' => $itemsPerRow == 2,
            'col-12' => $itemsPerRow == 1,
        ])>
            {!! Theme::partial('blog.posts.item-grid', compact('post')) !!}
        </div>
    @endforeach
</div>
