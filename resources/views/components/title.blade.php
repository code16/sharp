@php
/** @var \Code16\Sharp\View\Components\Title $component */
@endphp

<title>
    @if(request()->is(sharp_base_url_segment() . "/login"))
        {{ trans('sharp::login.login_page_title') }}
    @else
        @if($component->label)
            {{ config("sharp.name", "Sharp") }}, {{ $component->label }}
        @else
            {{ config("sharp.name", "Sharp") }}
        @endif
    @endif
    @if(config("sharp.display_sharp_version_in_title", true))
        | Sharp {{ sharp_version() }}
    @endif
</title>
