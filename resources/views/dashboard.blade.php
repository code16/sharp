@extends("sharp::layout")

@section("content")

    <div id="sharp-app" class="dashboard">
        @include("sharp::partials._menu")

        <sharp-action-view context="dashboard" v-cloak>
            <router-view></router-view>
        </sharp-action-view>
    </div>

@endsection