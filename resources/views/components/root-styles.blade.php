@php
/**
 * @var \Code16\Sharp\View\Components\RootStyles $self
 */
@endphp

<style>
    /** shadcn inline styles to prevent flickering */
    :root {
        --background: oklch(1 0 0);
        --foreground: oklch(0.145 0 0);
    }
    .dark {
        --background: oklch(0.145 0 0);
        --foreground: oklch(0.985 0 0);
    }
    body {
        background-color: var(--background);
    }
    :root {
        --sharp-config-primary: {{ $primaryColor }};
    }
</style>
