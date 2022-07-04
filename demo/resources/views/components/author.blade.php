@props([
    'author',
    'picture' => null,
])

@if($author = is_string($author) ? \App\Models\User::find($author) : $author)
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                @if($picture = json_decode($picture ?? 'null', true))
                    <div class="col-auto">
                        <img src="{{ (new \App\Models\Media($picture + ['file_name' => $picture['path']]))->thumbnail(200, 200) }}" alt="{{ $author->name }}" width="100" height="100">
                    </div>
                @endif
                <div class="col">
                    <h3>{{ $author->name }}</h3>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
@endif
