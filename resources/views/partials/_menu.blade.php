<sharp-left-nav v-cloak current="{{ $sharpMenu->currentEntity ?: ($dashboard ? 'dashboard' : '') }}" :categories="{{ json_encode($sharpMenu->menuItems) }}">
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

        @if($sharpMenu->dashboard)
            <sharp-nav-item :current="{{ json_encode($dashboard ?? false) }}" link="{{ route('code16.sharp.dashboard') }}">
                <span>
                    <sharp-item-visual :item="{ icon:'fa-dashboard' }" icon-class="fa-fw"></sharp-item-visual>
                    @lang('sharp::menu.dashboard')
                </span>
            </sharp-nav-item>
        @endif

        @foreach($sharpMenu->menuItems as $menuItem)
            @if($menuItem->type == 'category')
                <sharp-collapsible-item label="{{ $menuItem->label }}">

                    @foreach($menuItem->entities as $entity)
                        <sharp-nav-item :current="{{ json_encode($sharpMenu->currentEntity==$entity->key) }}"
                                        link="{{ route('code16.sharp.list', $entity->key) }}">
                        <span>
                            @if($entity->icon)
                                <sharp-item-visual :item="{{ json_encode($entity) }}" icon-class="fa-fw"></sharp-item-visual>
                            @endif
                            {{ $entity->label }}
                        </span>
                        </sharp-nav-item>
                    @endforeach

                </sharp-collapsible-item>
            @else
                <sharp-nav-item :current="{{ json_encode($sharpMenu->currentEntity==$menuItem->key) }}"
                                link="{{ route('code16.sharp.list', $menuItem->key) }}">
                        <span>
                            @if($menuItem->icon)
                                <sharp-item-visual :item="{{ json_encode($menuItem) }}" icon-class="fa-fw"></sharp-item-visual>
                            @endif
                            {{ $menuItem->label }}
                        </span>
                </sharp-nav-item>
            @endif
        @endforeach
    </ul>
</sharp-left-nav>
<script>
    import SharpNavItem from "../../assets/js/components/menu/NavItem";
    export default {
        components: {SharpNavItem}
    }
</script>
<script>
    import SharpItemVisual from "../../assets/js/components/ui/ItemVisual";
    export default {
        components: {SharpItemVisual}
    }
</script>