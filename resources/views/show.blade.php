@extends("sharp::layout")

@section("content")

    <div id="sharp-app" class="show">
        @include("sharp::partials._menu")
        <sharp-action-view context="show">
            <router-view></router-view>
        </sharp-action-view>
    </div>

@endsection