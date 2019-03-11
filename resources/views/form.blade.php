@extends("sharp::layout")

@section("content")

    <div id="sharp-app" class="form">
        @include("sharp::partials._menu")
        <sharp-action-view context="form">
            <sharp-form entity-key="{{ $entityKey }}"
                        instance-id="{{ $instanceId ?? '' }}">
            </sharp-form>
        </sharp-action-view>
    </div>

@endsection