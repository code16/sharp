<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="base-url" content="{{ sharp_base_url_segment() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <x-sharp::title />

    <link rel="stylesheet" href="{{ mix('vendors.css', '/vendor/sharp') }}">
    <link rel="stylesheet" href="{{ mix('sharp.css', '/vendor/sharp') }}">

    <x-sharp::root-styles />

    @include('sharp::public.head')

    {!! \Illuminate\Support\Arr::get($injectedAssets ?? [], 'head') !!}
</head>
<body class="{{ $bodyClass ?? '' }}">
    @yield('content')

    @if(sharp_assets_out_of_date())
        <script>
            window.prompt(
                'Sharp assets are out of date. Please run the following command:',
                'php artisan vendor:publish --provider=Code16\\\\Sharp\\\\SharpServiceProvider --tag=assets --force'
            );
        </script>
    @endif

    <script src="{{ mix('manifest.js', '/vendor/sharp') }}"></script>
    <script src="{{ mix('vendor.js', '/vendor/sharp') }}"></script>
    <script src="{{ mix('client-api.js', '/vendor/sharp') }}"></script>

    {!! sharp_custom_fields() !!}

    <script src="/vendor/sharp/lang.js?version={{ sharp_version() }}&locale={{ app()->getLocale() }}"></script>
    <script src="{{ mix('sharp.js', '/vendor/sharp') }}"></script>
</body>
</html>
