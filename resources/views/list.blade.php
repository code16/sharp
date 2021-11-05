@extends("sharp::layout")

<x-sharp::preload
    :href="route('code16.sharp.api.list', currentSharpRequest()->entityKey())"
/>

@section("content")

    <x-sharp::page class="entity-list">
        <sharp-action-view></sharp-action-view>
    </x-sharp::page>

@endsection
