

<x-layout>
    <div class="row justify-content-center">
        <div class="col col-lg-6 col-md-8">
            <div class="text-center">
                <h1>{{ $spaceship->name }}</h1>
                @if($spaceship->picture)
                    <img class="mw-100" src="{{ $spaceship->picture->thumbnail(600) }}" alt="{{ $spaceship->name }}">
                @endif
            </div>

            <hr class="mt-5 mb-5">

            <x-sharp-content :image-thumbnail-width="500">
                <x-sharp-content::attributes
                    component="sharp-image"
                    class="d-block shadow m-auto"
                    :thumbnail-height="400"
                    :custom-prop="true"
                />
                <x-markdown>
                    {!! $spaceship->description !!}
                </x-markdown>
            </x-sharp-content>
        </div>
    </div>

</x-layout>
