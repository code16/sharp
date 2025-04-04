
<div style="display: flex; align-items: start; gap: 1rem;">
    @if($picture)
        <img src="{{ $picture['thumbnail'] }}" width="50" height="50">
    @endif
    <div style="flex: 1">
        <div><strong>{{ $name }}</strong></div>
        <div style="font-size: .875rem">
            {{ $slot }}
        </div>
    </div>
</div>
