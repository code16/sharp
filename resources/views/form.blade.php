@extends("sharp::layout")

@section("content")

    <x-sharp::page class="form">
        <sharp-action-view context="form">
            <router-view></router-view>
        </sharp-action-view>
    </x-sharp::page>

@endsection
