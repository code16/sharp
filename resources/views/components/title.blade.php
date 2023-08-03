
{{--
    Warning: title is also updated by the front
--}}
<title>
    @auth
        @if($currentEntityLabel = auth()->check() ? currentSharpRequest()->getCurrentEntityMenuLabel() : null)
            {{ $currentEntityLabel }} |
        @endif
    @endauth

    {{ config("sharp.name", "Sharp") }}
    @if(config("sharp.display_sharp_version_in_title", true))
        (Sharp {{ sharp_version() }})
    @endif
</title>
