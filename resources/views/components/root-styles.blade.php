@php
/**
 * @var \Code16\Sharp\View\Components\RootStyles $self
 */
@endphp

<style>
    /** shadcn inline styles to prevent flickering */
    :root {
        --background: 0 0% 100%;
        --foreground: 240 10% 3.9%;
    }
    .dark {
        --background: 240 10% 3.9%;
        --foreground: 0 0% 98%;
    }
    body {
        background-color: hsl(var(--background));
    }
    :root {
        --sharp-config-primary: {{ $primaryColor }};
    }
    /** Legacy vars */
    :root {
        --primary-h: {{ $self->formatNumber($primaryColorHSL[0]) }}deg;
        --primary-s: {{ $self->formatNumber($primaryColorHSL[1]) }}%;
        --primary-l: {{ $self->formatNumber($primaryColorHSL[2]) }}%;

        --l-threshold: {{ $self->formatNumber(80 - $primaryColorLuminosity * 40) }}%;
    }
</style>
