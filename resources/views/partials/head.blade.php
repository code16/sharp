
@if($faviconUrl = sharp()->config()->get('theme.favicon_url'))
    <link rel="icon" href="{{ $faviconUrl }}">
@endif
