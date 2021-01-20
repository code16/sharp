@extends("sharp::layout")

@section("content")

    <div id="sharp-app" class="form">
        @include("sharp::partials._menu")
        <sharp-action-view context="form">
            <router-view
                @if(config('sharp.form_live_update'))
                    live-update
                @endif
            ></router-view>
        </sharp-action-view>
    </div>

@endsection
