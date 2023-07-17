<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="base-url" content="{{ sharp_base_url_segment() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <x-sharp::title>
        {{ $title ?? null }}
    </x-sharp::title>

    <link rel="stylesheet" href="{{ mix('vendors.css', '/vendor/sharp') }}">
    <link rel="stylesheet" href="{{ mix('sharp.css', '/vendor/sharp') }}">
    <meta name="robots" content="noindex, nofollow" />

    <x-sharp::root-styles />

    <x-sharp::extensions.injected-assets />

    @include('sharp::partials.head')

    <script defer src="{{ mix('manifest.js', '/vendor/sharp') }}"></script>
    <script defer src="{{ mix('vendor.js', '/vendor/sharp') }}"></script>
    <script defer src="{{ mix('client-api.js', '/vendor/sharp') }}"></script>

    <x-sharp::extensions.custom-fields-script />

    <script defer src="/vendor/sharp/lang.js?version={{ sharp_version() }}&locale={{ app()->getLocale() }}"></script>
    <script defer src="{{ mix('sharp.js', '/vendor/sharp') }}"></script>
</head>
<body {{ $attributes }}>
    <x-sharp::alert.assets-outdated />

    {{ $slot }}

    @stack('script')
</body>
</html>
