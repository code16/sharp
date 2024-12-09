@props([
    'post',
])

@if($post = is_string($post) ? \App\Models\Post::find($post) : $post)
    <div class="my-6 border rounded-md p-6 not-prose">
        <h4 class="mb-2 text-sm font-medium">Related post</h4>
        <h3 class="text-xl text-neutral-900">
            {{ $post->title }}
        </h3>
        <div class="mt-2 text-neutral-500 text-base">
            {{ Str::limit(strip_tags($post->content), 200) }}
        </div>
    </div>
@endif
