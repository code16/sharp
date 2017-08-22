@extends("sharp::layout")

@section("content")

    <div id="sharp-app" v-cloak>
        @include("sharp::partials._menu", ["dashboard" => true])

        @if($sharpMenu->dashboard)

            <sharp-action-view context="dashboard">
                <sharp-dashboard></sharp-dashboard>
            </sharp-action-view>

        @else

            <div class="SharpActionView">
                <div class="container">
                    <h1>
                        @lang("sharp::menu.no-dashboard-message")
                    </h1>
                </div>
            </div>

        @endif
    </div>

@endsection