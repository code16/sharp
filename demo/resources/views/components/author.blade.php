@props([
    'author',
    'picture' => null,
    'biographyText' => null,
])

@if($author = is_string($author) ? \App\Models\User::find($author) : $author)
    <div class="card mb-3">
        <div class="card-body">
            <div class="row">
                @if($picture = $picture ? new \App\Models\Media(json_decode($picture, true)) : null)
                    <div class="col-auto">
                        <img src="{{ $picture->thumbnail(200, 200) }}" alt="{{ $author->name }}" width="100" height="100">
                    </div>
                @endif
                <div class="col">
                    @if($author)
                        <h3>{{ $author->name }}</h3>
                    @endif
                    {!! $biographyText !!}
                </div>
            </div>
        </div>
    </div>
@endif
