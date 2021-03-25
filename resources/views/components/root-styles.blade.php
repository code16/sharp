@php
    /** @var \Code16\Sharp\View\Components\RootStyles $component */
@endphp

<style>
    :root {
        --bs-primary: {{ $component->primaryColor }};
        --bs-primary-h: {{ round($component->primaryColorHSL[0], 5) }}deg;
        --bs-primary-s: {{ round($component->primaryColorHSL[1], 5) }}%;
        --bs-primary-l: {{ round($component->primaryColorHSL[2], 5) }}%;

        --l-threshold: {{ round(80 - $component->primaryColorLuminosity * 40) }}%;
    }
</style>
