@extends("sharp::layout")


<x-sharp::preload
    :href="route('code16.sharp.api.show.show', [
        currentSharpRequest()->entityKey(),
        currentSharpRequest()->instanceId()
    ])"
/>

@section("content")
    <x-sharp::page class="show">
        <sharp-action-view></sharp-action-view>
    </x-sharp::page>
@endsection
