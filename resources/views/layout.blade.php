<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="base-url" content="{{ sharp_base_url_segment() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ sharp_page_title($sharpMenu ?? null, $entityKey ?? $dashboardKey ?? null) }}</title>
    <link rel="stylesheet" href="/vendor/sharp/sharp.css?version={{ sharp_version() }}">
    <link rel="stylesheet" href="/vendor/sharp/sharp-cms.css?version={{ sharp_version() }}">
    {!! \Illuminate\Support\Arr::get($injectedAssets ?? [], 'head') !!}
</head>
<body>
    <div id="glasspane"></div>


    @yield('content')

    <script src="/vendor/sharp/manifest.js?version={{ sharp_version() }}"></script>
    <script src="/vendor/sharp/vendor.js?version={{ sharp_version() }}"></script>
    <script src="/vendor/sharp/client-api.js?version={{ sharp_version() }}"></script>

    {!! sharp_custom_form_fields() !!}

    <script src="/vendor/sharp/lang.js?version={{ sharp_version() }}&locale={{ app()->getLocale() }}"></script>
    <script src="/vendor/sharp/sharp.js?version={{ sharp_version() }}"></script>
</body>
</html>