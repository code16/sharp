
<div id="sharp-app" {{ $attributes }}>
    <x-sharp::menu />

    @if(trim($slot))
        {{ $slot }}
    @else
        <sharp-action-view>
            <template v-slot:user-dropdown>
                <x-sharp::user-dropdown />
            </template>
        </sharp-action-view>
    @endif
</div>
