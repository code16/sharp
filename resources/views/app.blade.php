<!DOCTYPE html>
<html class="scroll-pt-14" lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="robots" content="noindex, nofollow" />

        <script>
            const preference = localStorage.getItem('vueuse-color-scheme') || 'auto';
            if(preference === 'auto' && window.matchMedia('(prefers-color-scheme: dark)').matches || preference === 'dark') {
                document.documentElement.classList.add('dark');
            }
        </script>

        <x-sharp::root-styles />

        @foreach(sharp()->config()->get('assets') as $asset)
            {!! $asset !!}
        @endforeach

        @include('sharp::partials.head')

{{--        <x-sharp::extensions.custom-fields-script />--}}

        @php
            config()->set('ziggy', ['only' => 'code16.sharp.*', 'skip-route-function' => true])
        @endphp
        @routes
        @inertiaHead

        <x-sharp::vite-wrapper>
            @if(!\Illuminate\Support\Facades\Vite::isRunningHot())
                @vite(['vite/legacy-polyfills'], '/vendor/sharp')
            @endif
            @vite('resources/js/sharp.ts', '/vendor/sharp')
        </x-sharp::vite-wrapper>
    </head>
    <body class="font-sans antialiased">
        <x-sharp::alert.assets-outdated />

        @inertia
        @stack('script')
    </body>
</html>
