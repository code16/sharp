<sharp-left-nav v-cloak current="{{ $entityKey ?? ($dashboard ? 'dashboard' : '') }}" :categories="{{ json_encode($sharpMenu->categories) }}">
    <h2 class="SharpLeftNav__title">{{ $sharpMenu->name }}</h2>

    <ul role="menubar" class="SharpLeftNav__list" aria-hidden="false" v-cloak>
        <sharp-nav-item disabled>
            <span>
                <i class="fa fa-user"></i>
                {{ $sharpMenu->user }}
            </span>
            <a href="{{ route('code16.sharp.logout') }}"><i class="fa fa-sign-out"></i></a>
        </sharp-nav-item>

        @if($sharpMenu->dashboard)
            <sharp-nav-item :current="{{ json_encode($dashboard ?? false) }}" link="{{ route('code16.sharp.dashboard') }}">
                <span>
                    <i class="fa fa-dashboard"></i>
                    @lang('sharp::menu.dashboard')
                </span>
            </sharp-nav-item>
        @endif

        @foreach($sharpMenu->categories as $category)
            <sharp-collapsible-item label="{{ $category->label }}">

                @foreach($category->entities as $entity)
                    <sharp-nav-item :current="{{ json_encode(($entityKey??false)==$entity->key) }}"
                                    link="{{ route('code16.sharp.list', $entity->key) }}">
                        <span>
                            @if($entity->icon)
                                <i class="fa {{ $entity->icon }}"></i>
                            @endif
                            {{ $entity->label }}
                        </span>
                    </sharp-nav-item>
                @endforeach

            </sharp-collapsible-item>
        @endforeach
    </ul>
</sharp-left-nav>
