@extends("sharp::layout")

@section("content")

    <div id="sharp-app">
        <sharp-action-view context="form">
            <sharp-form entity-key="{{ $entityKey }}" instance-id="{{ $instanceId ?? "" }}"></sharp-form>
        </sharp-action-view>
    </div>

@endsection