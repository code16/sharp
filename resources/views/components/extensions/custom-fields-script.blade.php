

@if(config("sharp.extensions.activate_custom_fields", false))
    @if($script = trim(view('sharp::partials.plugin-script')->render()))
        {!! $script !!}
    @else
        <script defer src="{{ mix('/js/sharp-plugin.js') }}"></script>
    @endif
@endif
