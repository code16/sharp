

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
        
            <x-sharp-content :image-width="400">
                <x-sharp-content::attributes
                        component="sharp-media"
                        :height="300"
                        style="margin:auto; display:block; box-shadow: #444 0 0 10px"
                />
                <x-markdown>
                    {!! $spaceship->description !!}
                </x-markdown>
            </x-sharp-content>
        </div>
    </div>

</x-layout>
