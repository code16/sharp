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

    @vite([
        'resources/sass/vendors.scss',
//        'resources/sass/app.scss',
        'resources/css/app.css',
    ], '/vendor/sharp')

    <x-sharp::root-styles />

    <x-sharp::extensions.injected-assets />

    @include('sharp::partials.head')

{{--    @vite('resources/assets/js/client-api.js', '/vendor/sharp')--}}

    <x-sharp::extensions.custom-fields-script />
    {{ $head ?? null }}

{{--    <script defer src="/vendor/sharp/lang.js?version={{ sharp_version() }}&locale={{ app()->getLocale() }}"></script>--}}
    @vite('resources/js/sharp.js', '/vendor/sharp')
</head>
<body {{ $attributes }}>
    <x-sharp::alert.assets-outdated />

    {{ $slot }}

    @stack('script')
</body>
</html>
