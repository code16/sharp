
{{--
    Warning: title is also updated by the front
--}}
<title>
    @if(trim($slot))
        {{ $slot }} |
    @elseif($currentEntityLabel = currentSharpRequest()->getCurrentEntityMenuLabel())
        {{ $currentEntityLabel }} |
    @endif
    {{ config("sharp.name", "Sharp") }}
    @if(config("sharp.display_sharp_version_in_title", true))
        (Sharp {{ sharp_version() }})
    @endif
</title>
