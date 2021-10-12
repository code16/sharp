@props([
    'label' => trim($slot) ? $slot : null,
])

<div class="SharpLeftNav__separator {{ $label ? 'SharpLeftNav__separator--labelled' : '' }}">
    @if($label)
        <span>
            {{ $label }}
        </span>
    @endif
</div>
