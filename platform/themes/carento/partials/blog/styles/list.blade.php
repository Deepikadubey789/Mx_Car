<div class="box-grid-hotels box-grid-news blog-posts-modern blog-posts-modern--list mt-60 mb-50 wow fadeIn">
    @foreach($posts as $post)
        {!! Theme::partial('blog.posts.item-list', compact('post')) !!}
    @endforeach
</div>
