<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="base-url" content="{{ sharp_base_url_segment() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="robots" content="noindex, nofollow" />

        <x-sharp::vite>
            @vite([
                'resources/sass/vendors.scss',
        //        'resources/sass/app.scss',
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

        @routes
        @inertiaHead

        <x-sharp::vite>
            @vite('resources/js/sharp.js', '/vendor/sharp')
        </x-sharp::vite>
    </head>
    <body>
        <x-sharp::alert.assets-outdated />

        @inertia

        @if($login ?? false)
            <template id="login-append">
                @includeIf(config("sharp.login_page_message_blade_path"))
            </template>
        @endif

        @stack('script')
    </body>
</html>
