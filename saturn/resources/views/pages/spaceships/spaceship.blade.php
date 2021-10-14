

<x-layout>

    <h1>
        {{ $spaceship->name }}
    </h1>

    <x-sharp-content :image-width="400">
        <x-sharp-content::attributes
            component="sharp-media"
            :height="300"
        />
        <x-markdown>
            {!! $spaceship->description !!}
        </x-markdown>
    </x-sharp-content>

</x-layout>
