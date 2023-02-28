@extends('sharp::layout')


@section('head')
    @inertiaHead
@endsection

@section("content")
    <div id="menu">
        <x-sharp::menu />
    </div>
    @inertia
@endsection
