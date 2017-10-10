@extends("sharp::layout")

@section("content")

    <div id="sharp-app">
        @include("sharp::partials._menu")
        <sharp-action-view context="list">
            <sharp-entity-list entity-key="{{ $entityKey }}"></sharp-entity-list>
        </sharp-action-view>
    </div>

@endsection