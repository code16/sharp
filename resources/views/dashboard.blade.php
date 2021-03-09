@extends("sharp::layout")

@section("content")

    <x-sharp::page class="dashboard">
        <sharp-action-view context="dashboard" v-cloak>
            <router-view></router-view>
        </sharp-action-view>
    </x-sharp::page>

@endsection
