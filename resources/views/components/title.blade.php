
{{--
    Warning: title is also updated by the front
--}}
<title>
    @if(request()->routeIs('code16.sharp.login'))
        {{ trans('sharp::login.login_page_title') }}
    @else
        @if($currentEntityLabel = currentSharpRequest()->getCurrentEntityMenuLabel())
            {{ $currentEntityLabel }} |
        @endif
        {{ config("sharp.name", "Sharp") }}
    @endif
    @if(config("sharp.display_sharp_version_in_title", true))
        (Sharp {{ sharp_version() }})
    @endif
</title>
