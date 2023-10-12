
<div id="app" {{ $attributes }}>
    <x-sharp::menu />

    @if(trim($slot))
        {{ $slot }}
    @else
        <sharp-action-view>
            @if(value(config('sharp.search.enabled', false)))
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
