@php
    /** @var \Code16\Sharp\View\Components\RootStyles $component */
@endphp

<style>
    :root {
        --bs-primary: {{ $component->primaryColor }};
        --bs-primary-h: {{ $component->formatNumber($component->primaryColorHSL[0]) }}deg;
        --bs-primary-s: {{ $component->formatNumber($component->primaryColorHSL[1]) }}%;
        --bs-primary-l: {{ $component->formatNumber($component->primaryColorHSL[2]) }}%;

        --l-threshold: {{ $component->formatNumber(80 - $component->primaryColorLuminosity * 40) }}%;
    }
</style>
