@php
/** @var \Code16\Sharp\View\Components\Title $component */
@endphp

{{--
    Warning: title is also updated by the front
--}}
<title>
    @if(request()->is(sharp_base_url_segment() . "/login"))
        {{ trans('sharp::login.login_page_title') }}
    @else
        @if($currentEntityLabel = $component->currentEntityLabel())
            {{ $currentEntityLabel }} |
        @endif
        {{ config("sharp.name", "Sharp") }}
    @endif
    @if(config("sharp.display_sharp_version_in_title", true))
        (Sharp {{ sharp_version() }})
    @endif
</title>
