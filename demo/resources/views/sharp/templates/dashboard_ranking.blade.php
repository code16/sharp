@if($author)
    <p>
        Congrats 🥳 to <strong>{{ $author->name }}</strong>,
        for the
        {!! $post->categories->map(fn ($category) => \Code16\Sharp\Utils\Links\LinkToShowPage::make('categories', $category->id)->renderAsText('#'.$category->name))->implode(' / ') !!}
        post:
    </p>
    @if($post)
        <p class="text-xl">
            <a href="{{ \Code16\Sharp\Utils\Links\LinkToShowPage::make('posts', $post->id)->renderAsUrl() }}">{{ $post->title }}</a>
        </p>
    @endif
@endif
