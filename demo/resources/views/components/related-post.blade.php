@props([
    'post',
])

@if($post = is_string($post) ? \App\Models\Post::find($post) : $post)
    <div class="card mb-3">
        <div class="card-body">
            <h4 class="h6">Related post</h4>
            <h3>
                {{ $post->title }}
            </h3>
            <div class="text-muted">
                {{ Str::limit(strip_tags($post->content), 200) }}
            </div>
        </div>
    </div>
@endif
