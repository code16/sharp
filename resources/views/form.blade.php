@extends("sharp::layout")

@section("content")

    <div id="sharp-app">
        <sharp-form entity-key="{{ $entityKey }}" instance-id="{{ $instanceId ?? "" }}"></sharp-form>
    </div>

@endsection