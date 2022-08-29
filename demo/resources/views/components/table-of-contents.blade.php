@aware([
    'content'
])


@if(count($titles = Str::matchAll('/<h2>(.+)<\/h2>/U', $content)))
    <ul>
        @foreach($titles as $title)
            <li>{{ $title }}</li>
        @endforeach
    </ul>
    <hr>
@endif
