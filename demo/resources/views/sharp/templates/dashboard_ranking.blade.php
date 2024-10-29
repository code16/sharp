<p>
    Congrats 🥳 to <strong>{{ $author->name }}</strong>,
    for the
    {!! $post->categories->map(fn ($category) => \Code16\Sharp\Utils\Links\LinkToShowPage::make('categories', $category->id)->renderAsText('#'.$category->name))->implode(' / ') !!}
    post:
</p>
<p class="text-xl">
    <a href="{{ $postUrl }}">{{ $post->title }}</a>
</p>