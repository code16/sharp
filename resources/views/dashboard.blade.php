@extends("sharp::layout")

@section("content")

{{--    <h1>Test incroyable</h1>--}}
{{--    <h2>Test incroyable</h2>--}}
{{--    <h3>Test incroyable</h3>--}}
{{--    <h4>Test incroyable</h4>--}}
{{--    <h5>Test incroyable</h5>--}}
{{--    <h6>Test incroyable</h6>--}}

    <x-sharp::page class="dashboard">
        <sharp-action-view context="dashboard" v-cloak>
            <router-view></router-view>
        </sharp-action-view>
    </x-sharp::page>

@endsection
