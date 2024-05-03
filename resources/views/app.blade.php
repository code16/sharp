<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="robots" content="noindex, nofollow" />

        <x-sharp::vite>
            @vite([
                'resources/css/vendors.css',
                'resources/css/app.css',
            ], '/vendor/sharp')
        </x-sharp::vite>

        <x-sharp::root-styles />

        <x-sharp::extensions.injected-assets />

        @include('sharp::partials.head')

        {{--    <x-sharp::vite>--}}
        {{--    @vite('resources/assets/js/client-api.js', '/vendor/sharp')--}}
        {{--    </x-sharp::vite>--}}

        <x-sharp::extensions.custom-fields-script />

        @php
            config()->set('ziggy', ['only' => 'code16.sharp.*'])
        @endphp
        @routes
        @inertiaHead

        <x-sharp::vite>
            @if(!Vite::isRunningHot())
                @vite(['vite/legacy-polyfills'], '/vendor/sharp')
            @endif
            @vite('resources/js/sharp.ts', '/vendor/sharp')
        </x-sharp::vite>
    </head>
    <body class="font-sans antialiased">
        <x-sharp::alert.assets-outdated />

        @inertia
        @stack('script')
    </body>
</html>
