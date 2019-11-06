@extends("sharp::layout")

@section("content")

    <div id="sharp-app" class="show">
        @include("sharp::partials._menu")
    </div>

    <sharp-action-view context="show">
        <router-view></router-view>
    </sharp-action-view>

@endsection