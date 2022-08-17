<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Post</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    </head>
    <body>
        @php
        /**
         * @var \App\Models\Post $post
         */
        @endphp
        <div class="mx-auto pb-5" style="width: 800px;">
            @if($post->cover)
                <div class="my-4">
                    <img class="img-fluid" src="{{ $post->cover->thumbnail(1200) }}" alt="">
                </div>
            @endif

            <h1 class="mb-3" style="font-weight: 600">
                {{ $post->title }}
            </h1>

            <x-sharp-content :image-thumbnail-width="1200">
                <x-sharp-content::attributes
                    component="sharp-image"
                    class="img-fluid"
                />
                {!! $post->content !!}
            </x-sharp-content>

            @foreach($post->blocks as $block)
                @if($block->type === 'text')
                    <div class="block mb-3">
                        {!! $block->content !!}
                    </div>
                @elseif($block->type === 'visuals')
                    <div class="block my-4" style="max-width: 600px">
                        <div class="row row-cols-md-2 g-3">
                            @foreach($block->files as $visual)
                                <div>
                                    <img class="img-fluid" src="{{ $visual->thumbnail(600) }}" alt="">
                                </div>
                            @endforeach
                        </div>
                    </div>
                @elseif($block->type === 'video')
                    <div class="block my-4">
                        {!! $block->content !!}
                    </div>
                @endif
            @endforeach

        </div>
    </body>
</html>
