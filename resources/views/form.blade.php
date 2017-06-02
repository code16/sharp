@extends("sharp::layout")

@section("content")

    <div id="sharp-app">
        <sharp-action-view>
            <sharp-form entity-key="{{ $entityKey }}" instance-id="{{ $instanceId ?? "" }}"></sharp-form>
        </sharp-action-view>
    </div>

@endsection