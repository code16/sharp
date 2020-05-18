<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="base-url" content="{{ sharp_base_url_segment() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ sharp_page_title($sharpMenu ?? null, $entityKey ?? $dashboardKey ?? null) }}</title>
    <link rel="stylesheet" href="{{ mix('sharp.css', '/vendor/sharp') }}">
    <link rel="stylesheet" href="{{ mix('sharp-cms.css', '/vendor/sharp') }}">
    {!! \Illuminate\Support\Arr::get($injectedAssets ?? [], 'head') !!}
</head>
<body>
    <div id="glasspane"></div>


    @yield('content')

    <script src="{{ mix('manifest.js', '/vendor/sharp') }}"></script>
    <script src="{{ mix('vendor.js', '/vendor/sharp') }}"></script>
    <script src="{{ mix('client-api.js', '/vendor/sharp') }}"></script>

    {!! sharp_custom_fields() !!}

    <script src="/vendor/sharp/lang.js?version={{ sharp_version() }}&locale={{ app()->getLocale() }}"></script>
    <script src="{{ mix('sharp.js', '/vendor/sharp') }}"></script>
</body>
</html>