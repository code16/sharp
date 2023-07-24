<x-sharp::layout>
    <x-slot:head>
        @routes
        @inertiaHead
    </x-slot:head>

    <div id="menu">
        <x-sharp::menu />
    </div>
    @inertia
</x-sharp::layout>
