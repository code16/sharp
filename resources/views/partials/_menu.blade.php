<nav role="navigation" aria-label="Menu Sharp" class="SharpLeftNav">
    <h2 class="SharpLeftNav__title">{{ $sharpMenu->name }}</h2>

    <ul role="menubar" class="SharpLeftNav__list" aria-hidden="false">
        <li role="menuitem" tabindex="0" class="SharpLeftNav__item">
            <span class="SharpLeftNav__item-link">
                <span>
                    <i class="fa fa-user"></i>
                {{ $sharpMenu->user }}
                </span>
                <a href="{{ route('code16.sharp.logout') }}" style="float:left"><i class="fa fa-sign-out"></i></a>
            </span>
        </li>

        @if($sharpMenu->dashboard)
        <li class="SharpLeftNav__item">
            <a class="SharpLeftNav__item-link" href="{{ route('code16.sharp.dashboard') }}" class="{{ ($dashboard ?? false) ? 'current' : ''}}">
                <span>
                    <i class="fa fa-dashboard"></i>

                    Dashboard {{-- <-- TODO i18 this --}}
                </span>
            </a>
        </li>
        @endif

        @foreach($sharpMenu->categories as $category)
        <sharp-collapsible-item label="{{ $category->label }}">
            @foreach($category->entities as $entity)
            <li class="SharpLeftNav__item" role="menuitem" tabindex="-1">
                <a class="SharpLeftNav__item-link" href="{{ route('code16.sharp.list', $entity->key) }}" class="{{ ($entityKey??false)==$entity->key ? 'current' : ''}}">
                        <span>
                            @if($entity->icon)
                            <i class="fa {{ $entity->icon }}"></i>
                            @endif
                            {{ $entity->label }}
                        </span>
                </a>
            </li>
            @endforeach
        </sharp-collapsible-item>
        @endforeach
    </ul>
</nav>
