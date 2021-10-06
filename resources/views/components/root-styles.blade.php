@php
/**
 * @var \Code16\Sharp\View\Components\RootStyles $self
 */
@endphp

<style>
    :root {
        --primary: {{ $primaryColor }};
        --primary-h: {{ $self->formatNumber($primaryColorHSL[0]) }}deg;
        --primary-s: {{ $self->formatNumber($primaryColorHSL[1]) }}%;
        --primary-l: {{ $self->formatNumber($primaryColorHSL[2]) }}%;

        --l-threshold: {{ $self->formatNumber(80 - $primaryColorLuminosity * 40) }}%;
    }
</style>
