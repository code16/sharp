@extends("sharp::layout")

@section("content")

    <div id="sharp-app" v-cloak>
        @include("sharp::partials._menu", ["dashboard" => true])
        <sharp-action-view context="dashboard">
            <sharp-dashboard></sharp-dashboard>
        </sharp-action-view>
    </div>

@endsection