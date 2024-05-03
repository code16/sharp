@props([
    'href' => null,
])

<sharp-badge
    class="line-clamp-1"
    {{ $attributes->merge(['as' => $href ? 'a' : 'span', 'href' => $href]) }}
    variant="secondary"
>
    {{ $slot }}
</sharp-badge>
