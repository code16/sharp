
<div id="app" {{ $attributes }}>
    <x-sharp::menu />

    @if(trim($slot))
        {{ $slot }}
    @else
        <sharp-action-view>
            @if(sharp()->config()->get('search.enabled'))
                <template v-slot:search>
                    <sharp-global-search
                        placeholder="{{ sharp()->config()->get('search.placeholder') }}"
                    ></sharp-global-search>
                </template>
            @endif
            <template v-slot:user-dropdown>
                <x-sharp::user-dropdown />
            </template>
        </sharp-action-view>
    @endif
</div>
