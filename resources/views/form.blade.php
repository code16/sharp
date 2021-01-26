@extends("sharp::layout")

@section("content")

    <div id="sharp-app" class="form">
        @include("sharp::partials._menu")
        <sharp-action-view context="form">
            <router-view>
                <template v-slot:field-text>
                    <x-sharp-field type="text" />
                </template>
            </router-view>
        </sharp-action-view>
    </div>

@endsection
