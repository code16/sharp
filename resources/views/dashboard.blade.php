@extends("sharp::layout")

@section("content")

    <div id="sharp-app">
        @include("sharp::partials._menu", ["dashboard" => true])
        <sharp-action-view context="dashboard">
            Dashboard
        </sharp-action-view>

    </div>

@endsection