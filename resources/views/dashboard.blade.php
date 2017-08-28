@extends("sharp::layout")

@section("content")

    <div id="sharp-app" class="Sharp__dashboard-page" v-cloak>
        @include("sharp::partials._menu", ["dashboard" => true])

        @if($sharpMenu->dashboard)

            <sharp-action-view context="dashboard">
                <sharp-dashboard></sharp-dashboard>
            </sharp-action-view>

        @else

            <div class="SharpActionView Sharp__empty-view">
                <div class="container h-100 d-flex justify-content-center align-items-center">
                    <h1>
                        @lang("sharp::menu.no-dashboard-message")
                    </h1>
                </div>
            </div>

        @endif
    </div>

@endsection