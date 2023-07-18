
<div id="sharp-app" {{ $attributes }}>
    <x-sharp::menu />

    @if(trim($slot))
        {{ $slot }}
    @else
        <sharp-action-view>
            @if(config('sharp.search.enabled'))
                <template v-slot:search>
                    <sharp-global-search
                        placeholder="{{ config('sharp.search.placeholder') }}"
                    ></sharp-global-search>
                </template>
            @endif
            <template v-slot:user-dropdown>
                <x-sharp::user-dropdown />
            </template>
        </sharp-action-view>
    @endif
</div>
