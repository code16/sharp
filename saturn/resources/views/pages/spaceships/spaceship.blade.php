

<x-layout>

    <h1>
        {{ $spaceship->name }}
    </h1>

    <x-sharp::content>
        <x-markdown>
            {!! $spaceship->description !!}
        </x-markdown>
    </x-sharp::content>

</x-layout>
