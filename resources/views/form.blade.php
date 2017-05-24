@extends("sharp::layout")

@section("content")

    <div id="sharp-app">
        <sharp-form entityKey="{{ $entityKey }}" instanceId=" {{ $instanceId ?? "" }}"></sharp-form>
    </div>

@endsection