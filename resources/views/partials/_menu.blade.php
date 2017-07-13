
<h2>{{ $sharpMenu->name }}</h2>

<ul>
    <li>
        <i class="fa fa-user"></i>
        {{ $sharpMenu->user }}
        <a href="#"><i class="fa fa-sign-out"></i></a>
    </li>

    <li>
        <i class="fa fa-dashboard"></i>
        Dashboard {{-- <--i18 this--}}
    </li>

    @foreach($sharpMenu->categories as $category)
        <li>
            {{ $category->label }}

            <ul>
                @foreach($category->entities as $entity)
                    <li>
                        <a href="{{ route("code16.sharp.list", $entity->key) }}">
                            @if($entity->icon)
                                <i class="fa {{ $entity->icon }}"></i>
                            @endif
                            {{ $entity->label }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </li>

    @endforeach
</ul>