<sharp-left-nav v-cloak
    current="{{ $sharpMenu->currentEntity }}"
    title="{{ $sharpMenu->name }}"
    :items="{{ json_encode($sharpMenu->menuItems) }}"
    :has-global-filters="{{ json_encode($hasGlobalFilters) }}"
>
    @if(file_exists(public_path('/sharp-assets/menu-icon.png')))
        <template slot="title">
            <img src="{{ asset('/sharp-assets/menu-icon.png') }}" alt="{{ $sharpMenu->name }}" width="150" class="w-auto h-auto" style="max-height: 50px;">
        </template>
    @endif
    <ul role="menubar" class="SharpLeftNav__list" aria-hidden="false">
        <sharp-nav-item disabled>
            <span title="{{ $sharpMenu->user }}">
                {{ $sharpMenu->user }}
            </span>
            <a href="{{ route('code16.sharp.logout') }}"> <sharp-item-visual :item="{ icon:'fas fa-sign-out-alt' }" icon-class="fa-fw"></sharp-item-visual></a>
        </sharp-nav-item>

        @foreach($sharpMenu->menuItems as $menuItem)
            @if($menuItem->type == 'category')
                <sharp-collapsible-item label="{{ $menuItem->label }}">
                    @foreach($menuItem->entities as $entity)
                        @include('sharp::partials._menu-item', [
                            'item' => $entity,
                            'isCurrent' => $sharpMenu->currentEntity == $entity->key
                        ])
                    @endforeach
                </sharp-collapsible-item>
            @else
                @include('sharp::partials._menu-item', [
                    'item' => $menuItem,
                    'isCurrent' => $sharpMenu->currentEntity == $menuItem->key
                ])
            @endif
        @endforeach
    </ul>
</sharp-left-nav>