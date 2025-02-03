@props([
    'author',
    'picture' => null,
])

@if($author = is_string($author) ? \App\Models\User::find($author) : $author)
    <div class="my-6 flex items-start border rounded-md p-4 gap-4 not-prose">
        @if($picture = json_decode($picture ?? 'null', true))
            <img src="{{ (new \App\Models\Media($picture))->thumbnail(200, 200) }}" alt="{{ $author->name }}" width="100" height="100">
        @endif
        <div class="flex-1">
            <h3 class="text-xl text-neutral-900">{{ $author->name }}</h3>
            <div class="mt-2 text-base">
                {{ $slot }}
            </div>
        </div>
    </div>
@endif
