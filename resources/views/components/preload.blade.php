@props([
    'href'
])

@push('head')
    <link
        href="{{ $href }}"
        {{ $attributes }}
        rel="preload"
        as="fetch"
        type="application/json"
        crossorigin="anonymous"
    >
@endpush
