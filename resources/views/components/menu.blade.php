@php
/**
 * @var \Code16\Sharp\View\Components\Menu $self
 */
@endphp

<sharp-left-nav
    class="SharpLeftNav"
    current="{{ $currentEntity }}"
    title="{{ $title }}"
    :items="{{ json_encode($items) }}"
    :has-global-filters="{{ json_encode($hasGlobalFilters) }}"
>
    <template v-slot:title>
        @if($icon = config('sharp.theme.logo_urls.menu'))
            <img src="{{ url($icon) }}" alt="{{ $title }}" width="150" class="w-auto h-auto" style="max-height: 50px;">
        @elseif(file_exists(public_path($icon = 'sharp-assets/menu-icon.png')))
            <img src="{{ asset($icon) }}?{{ filemtime(public_path($icon)) }}" alt="{{ $title }}" width="150" class="w-auto h-auto" style="max-height: 50px;">
        @endif
    </template>
    <ul role="menubar" class="SharpLeftNav__list" aria-hidden="false" v-cloak>
        <sharp-nav-item disabled>
            <div class="row align-items-center flex-nowrap gx-2">
                <div class="col" style="min-width: 0">
                    <div class="text-truncate" title="{{ $username }}">
                        {{ $username }}
                    </div>
                </div>
                <div class="col-auto">
                    <sharp-dropdown class="d-block my-n2 me-n1" :text="true" small right>
                        <template v-slot:text>
                            <i class="fas fa-user me-1" style="font-size: 1em"></i>
                        </template>
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
        </sharp-nav-item>

        @foreach($items as $menuItem)
            @if($menuItem->type === 'category')
                <sharp-collapsible-item
                    label="{{ $menuItem->label }}"
                    @if(collect($menuItem->entities)->some(fn ($entity) => $entity->key === $currentEntity))
                        opened
                    @endif
                >
                    @foreach($menuItem->entities as $entity)
                        <x-sharp::menu.menu-item
                            :item="$entity"
                            :is-current="$currentEntity == $entity->key"
                            nested
                        />
                    @endforeach
                </sharp-collapsible-item>
            @else
                <x-sharp::menu.menu-item
                    :item="$menuItem"
                    :is-current="$currentEntity == $menuItem->key"
                />
            @endif
        @endforeach
    </ul>
</sharp-left-nav>
