<sharp-left-nav v-cloak
    current="{{ $sharpMenu->currentEntity }}"
    title="{{ $sharpMenu->name }}"
    :items="{{ json_encode($sharpMenu->menuItems) }}"
>
    <ul role="menubar" class="SharpLeftNav__list" aria-hidden="false" v-cloak>
        <sharp-nav-item disabled>
            <span title="{{ $sharpMenu->user }}">
                {{ $sharpMenu->user }}
            </span>
            <a href="{{ route('code16.sharp.logout') }}"> <sharp-item-visual :item="{ icon:'fa-sign-out' }" icon-class="fa-fw"></sharp-item-visual></a>
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