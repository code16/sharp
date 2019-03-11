@extends("sharp::layout")

@section("content")

    <div id="sharp-app">
        @include("sharp::partials._menu")
        <sharp-action-view context="list">
            <router-view></router-view>
        </sharp-action-view>
    </div>

@endsection