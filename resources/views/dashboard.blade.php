@extends("sharp::layout")

@section("content")

    <div id="sharp-app" class="Sharp__dashboard-page">
        @include("sharp::partials._menu")

        <sharp-action-view context="dashboard" v-cloak>
            <sharp-dashboard dashboard-key="{{ $dashboardKey }}"></sharp-dashboard>
        </sharp-action-view>

    </div>

@endsection