@extends("sharp::layout")

@section("content")

    <div id="sharp-app">
        @include("sharp::partials._menu")
        <sharp-action-view context="list">
            <sharp-entities-list entity-key="{{ $entityKey }}"></sharp-entities-list>
        </sharp-action-view>
    </div>

@endsection