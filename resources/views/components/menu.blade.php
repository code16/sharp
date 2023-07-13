@php
/**
 * @var \Code16\Sharp\View\Components\Menu $self
 * @var \Code16\Sharp\Utils\Menu\SharpMenuItem[] $items
 * @var \Code16\Sharp\Utils\Menu\SharpMenuItemLink $currentEntityItem
 */
@endphp

<sharp-left-nav
    class="SharpLeftNav"
    @if($currentEntityItem)
        :current-entity="{{ json_encode([
            'label' => $currentEntityItem->getLabel(),
            'icon' => $currentEntityItem->getIcon(),
        ]) }}"
    @endif
    title="{{ $title }}"
>
    <template v-slot:title>
        @if($icon = config('sharp.theme.logo_urls.menu'))
            <img src="{{ url($icon) }}" alt="{{ $title }}" width="150" class="w-auto h-auto" style="max-height: 50px;">
        @elseif(file_exists(public_path($icon = 'sharp-assets/menu-icon.png')))
            <img src="{{ asset($icon) }}?{{ filemtime(public_path($icon)) }}" alt="{{ $title }}" width="150" class="w-auto h-auto" style="max-height: 50px;">
        @endif
    </template>
    <ul role="menubar" class="SharpLeftNav__list" aria-hidden="false" v-cloak>
        @if($hasGlobalFilters)
            <sharp-nav-item
                class="SharpLeftNav__item--unstyled position-static"
                link-class="position-static py-0"
                disabled
            >
                <div class="ms-n2 me-n1">
                    <sharp-global-filters
                        class="d-block"
                        style="min-height: 2rem"
                    />
                </div>
            </sharp-nav-item>
        @endif

        @foreach($self->getItems() as $item)
            @if($item->isSection())
                <x-sharp::menu.menu-section
                    :item="$item"
                    :current-entity-key="$currentEntityKey"
                />
            @else
                <x-sharp::menu.menu-item
                    :item="$item"
                    :current-entity-key="$currentEntityKey"
                />
            @endif
        @endforeach
    </ul>
</sharp-left-nav>
