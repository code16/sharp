<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>Post</title>
        @vite(['resources/css/app.css'])
    </head>
    <body>
        @php
        /**
         * @var \App\Models\Post $post
         */
        @endphp
        <div class="pt-12 pb-24 px-6 container mx-auto max-w-(--breakpoint-lg)">
            <div class="mb-8 prose prose-lg [&_iframe]:max-w-full max-w-full">
                @if($post->cover)
                    <img class="w-full" src="{{ $post->cover->thumbnail(1200) }}" alt="">
                @endif
                <h1>
                    {{ $post->title }}
                </h1>
                <x-sharp-content :image-thumbnail-width="1200">
                    {!! $post->content !!}
                </x-sharp-content>
            </div>
            <h2 class="mt-16 mb-8 text-3xl font-bold border-b pb-4">
                Blocks
            </h2>
            @foreach($post->blocks as $block)
                @if($block->type === 'text')
                    <div class="block mb-3">
                        {!! $block->content !!}
                    </div>
                @elseif($block->type === 'visuals')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($block->files as $visual)
                            <img class="rounded-2xl w-full" src="{{ $visual->thumbnail(600) }}" alt="">
                        @endforeach
                    </div>
                @elseif($block->type === 'video')
                    <div class="[&_iframe]:max-w-full my-4">
                        {!! $block->content !!}
                    </div>
                @endif
            @endforeach
        </div>
    </body>
</html>
