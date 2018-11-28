<sharp-left-nav v-cloak
    current="{{ $sharpMenu->currentEntity }}"
    :items="{{ json_encode($sharpMenu->menuItems) }}"
>
    <div class="SharpLeftNav__title-container">
        <h2 class="SharpLeftNav__title">{{ $sharpMenu->name }}</h2>
    </div>
    <div class="SharpLeftNav__shadow"></div>

    <ul role="menubar" class="SharpLeftNav__list" aria-hidden="false" v-cloak>
        <sharp-nav-item disabled>
            <span title="{{ $sharpMenu->user }}">
                {{ $sharpMenu->user }}
            </span>
            <a href="{{ route('code16.sharp.logout') }}"> <sharp-item-visual :item="{ icon:'fa-sign-out' }" icon-class="fa-fw"></sharp-item-visual></a>
        </sharp-nav-item>

        {{--@if($sharpMenu->dashboard)--}}
            {{--<sharp-nav-item :current="{{ json_encode($dashboard ?? false) }}" link="{{ route('code16.sharp.dashboard') }}">--}}
                {{--<span>--}}
                    {{--<sharp-item-visual :item="{ icon:'fa-dashboard' }" icon-class="fa-fw"></sharp-item-visual>--}}
                    {{--@lang('sharp::menu.dashboard')--}}
                {{--</span>--}}
            {{--</sharp-nav-item>--}}
        {{--@endif--}}

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