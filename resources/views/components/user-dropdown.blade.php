
<sharp-dropdown class="d-block me-n2" toggle-class="border-0" variant="light" outline small right>
    <template v-slot:text>
        <div class="d-inline-block me-1">
            <div class="row align-items-center flex-nowrap gx-2">
                <div class="col-auto">
                    <i class="fas fa-user me-1" style="font-size: 1em"></i>
                </div>
                <div class="col" style="min-width: 0">
                    @php($username = sharp_user()->{config('sharp.auth.display_attribute', 'name')})
                    <div class="text-truncate fs-7 fw-normal" style="text-transform: none; letter-spacing: 0; font-family: var(--bs-body-font-family)" title="{{ $username }}">
                        {{ $username }}
                    </div>
                </div>
            </div>
        </div>
    </template>
    @if($userMenu = app(\Code16\Sharp\Utils\Menu\SharpMenuManager::class)->userMenu())
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
