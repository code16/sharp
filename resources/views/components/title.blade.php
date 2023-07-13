
{{--
    Warning: title is also updated by the front
--}}
<title>
    @if(trim($slot))
        {{ $slot }}
    @else
        @if($currentEntityLabel = currentSharpRequest()->getCurrentEntityMenuLabel())
            {{ $currentEntityLabel }}
        @endif
    @endif
    | {{ config("sharp.name", "Sharp") }}
    @if(config("sharp.display_sharp_version_in_title", true))
        (Sharp {{ sharp_version() }})
    @endif
</title>
