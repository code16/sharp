<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ sharp_page_title($sharpMenu, $entityKey ?? null) }}</title>
    <link rel="stylesheet" href="/vendor/sharp/sharp.css?version={{ sharp_version() }}">
    <link rel="stylesheet" href="/vendor/sharp/sharp-cms.css?version={{ sharp_version() }}">
</head>
<body>
    <div id="glasspane"></div>

    @yield('content')

    <script src="/vendor/sharp/lang.js?version={{ sharp_version() }}&locale={{ app()->getLocale() }}"></script>
    <script src="/vendor/sharp/sharp.js?version={{ sharp_version() }}"></script>
</body>
</html>