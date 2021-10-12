

<x-layout>

    <h1>
        {{ $spaceship->name }}
    </h1>

    <x-sharp-content :image-height="500">
        <x-sharp-content::attributes
            component="sharp-media"
            :width="400"
        />
        <x-markdown>
            {!! $spaceship->description !!}
        </x-markdown>
    </x-sharp-content>

</x-layout>
