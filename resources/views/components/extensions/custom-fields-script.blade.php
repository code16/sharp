

@if(config("sharp.extensions.activate_custom_fields", false))
    @php
        try {
            $url = mix('/js/sharp-plugin.js');
        } catch(\Exception $exception) {}
    @endphp

    @if($url ?? null)
        <script src="{{ $url }}"></script>

    @elseif($exception ?? null)
        <script>
            console.error(@json($exception->getMessage()));
        </script>
    @endif
@endif
