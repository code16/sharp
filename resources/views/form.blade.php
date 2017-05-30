@extends("sharp::layout")

@section("content")

    <div id="sharp-app">
        <sharp-action-view main-button-text="Valider">
            <sharp-form entity-key="{{ $entityKey }}" instance-id="{{ $instanceId ?? "" }}"></sharp-form>
        </sharp-action-view>
    </div>

@endsection