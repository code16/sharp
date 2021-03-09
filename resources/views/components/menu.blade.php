@php
    /** @var \Code16\Sharp\View\Components\Menu $component */
@endphp

<sharp-left-nav v-cloak
    current="{{ $component->currentEntity }}"
    title="{{ $component->title }}"
    :items="{{ json_encode($component->items) }}"
    :has-global-filters="{{ json_encode($hasGlobalFilters) }}"
>
    @if(file_exists(public_path($icon = '/sharp-assets/menu-icon.png')))
        <template slot="title">
            <img src="{{ asset($icon) }}?{{ filemtime(public_path($icon)) }}" alt="{{ $component->title }}" width="150" class="w-auto h-auto" style="max-height: 50px;">
        </template>
    @endif
    <ul role="menubar" class="SharpLeftNav__list" aria-hidden="false">
        <sharp-nav-item disabled>
            <span title="{{ $component->username }}">
                {{ $component->username }}
            </span>
            <a href="{{ route('code16.sharp.logout') }}"> <sharp-item-visual :item="{ icon:'fas fa-sign-out-alt' }" icon-class="fa-fw"></sharp-item-visual></a>
        </sharp-nav-item>

        @foreach($component->items as $menuItem)
            @if($menuItem->type == 'category')
                <sharp-collapsible-item label="{{ $menuItem->label }}">
                    @foreach($menuItem->entities as $entity)
                        <x-sharp::menu-item
                            :item="$entity"
                            :is-current="$currentEntity == $entity->key"
                        />
                    @endforeach
                </sharp-collapsible-item>
            @else
                <x-sharp::menu-item
                    :item="$menuItem"
                    :is-current="$currentEntity == $menuItem->key"
                />
            @endif
        @endforeach
    </ul>
</sharp-left-nav>
