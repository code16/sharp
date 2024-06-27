
@if($faviconUrl = sharpConfig()->get('theme.favicon_url'))
    <link rel="icon" href="{{ $faviconUrl }}">
@endif
