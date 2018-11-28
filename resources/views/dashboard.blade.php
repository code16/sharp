@extends("sharp::layout")

@section("content")

    <div id="sharp-app" class="Sharp__dashboard-page" v-cloak>
        @include("sharp::partials._menu")

        <sharp-action-view context="dashboard">
            <sharp-dashboard></sharp-dashboard>
        </sharp-action-view>

    </div>

@endsection