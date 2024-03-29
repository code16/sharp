<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="base-url" content="{{ sharp_base_url_segment() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="robots" content="noindex, nofollow" />

    <x-sharp::title>
        {{ $title ?? null }}
    </x-sharp::title>

    <x-sharp::vite>
        @vite([
            'resources/assets/sass/vendors.scss',
            'resources/assets/sass/app.scss'
        ], '/vendor/sharp')
    </x-sharp::vite>

    <x-sharp::root-styles />

    <x-sharp::extensions.injected-assets />

    @include('sharp::partials.head')

    <x-sharp::vite>
        @vite('resources/assets/js/client-api.js', '/vendor/sharp')
    </x-sharp::vite>

    <x-sharp::extensions.custom-fields-script />

    <script defer src="/vendor/sharp/lang.js?version={{ sharp_version() }}&locale={{ app()->getLocale() }}"></script>

    <x-sharp::vite>
        @vite('resources/assets/js/sharp.js', '/vendor/sharp')
    </x-sharp::vite>
</head>
<body {{ $attributes }}>
    <x-sharp::alert.assets-outdated />

    {{ $slot }}

    @stack('script')
</body>
</html>
