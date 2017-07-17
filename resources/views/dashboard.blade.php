@extends("sharp::layout")

@section("content")

    @include("sharp::partials._menu", ["dashboard" => true])

    <div id="sharp-app">
        Dashboard
    </div>

@endsection