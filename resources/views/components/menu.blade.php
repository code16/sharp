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
        <sharp-nav-item
            class="SharpLeftNav__item--unstyled position-static"
            link-class="position-static py-0"
            disabled
        >
            <div class="row align-items-center flex-nowrap gx-2">
                <div class="col" style="min-width: 0">
                    <div class="text-truncate" title="{{ $username }}">
                        {{ $username }}
                    </div>
                </div>
                <div class="col-auto">
                    <sharp-dropdown class="d-block me-n1" :text="true" small right>
                        <template v-slot:text>
                            <i class="fas fa-user me-1" style="font-size: 1em"></i>
                        </template>
                        @if($userMenu = $self->getUserMenu())
                            @foreach($userMenu->getItems() as $item)
                                <li>
                                    <x-sharp::menu.dropdown-item
                                        :item="$item"
                                    />
                                </li>
                            @endforeach
                            @if(count($userMenu->getItems()))
                                <li><hr class="dropdown-divider"></li>
                            @endif
                        @endif
                        <li>
                            <form action="{{ route('code16.sharp.logout') }}" method="post">
                                @csrf
                                <button class="dropdown-item" type="submit">
                                    <span class="row align-items-center flex-nowrap gx-2">
                                        <span class="col-auto">
                                            <i class="fas fa-fw fa-sign-out-alt" style="font-size: 1em"></i>
                                        </span>
                                        <span class="col">
                                            {{ __('sharp::menu.logout_label') }}
                                        </span>
                                    </span>
                                </button>
                            </form>
                        </li>
                    </sharp-dropdown>
                </div>
            </div>
            @if($hasGlobalFilters)
                <div class="mt-2 pt-1 ms-n2 me-n1">
                    <sharp-global-filters
                        class="d-block"
                        style="min-height: 2rem"
                    />
                </div>
            @endif
        </sharp-nav-item>

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
