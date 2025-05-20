
<sharp-card-content class="text-sm {{ $attributes->get('class') }}" {{ $attributes->except('class') }}>
    {{ $slot }}
</sharp-card-content>
