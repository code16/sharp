@if($item->type === 'separator')
    <x-sharp::menu-separator>
        {{ $item->label }}
    </x-sharp::menu-separator>
@else
    <sharp-nav-item :current="{{ json_encode($isCurrent) }}" link="{{ $item->url }}">
        <div class="row flex-nowrap gx-3">
            @if($item->icon)
                <div class="col-auto">
                    <sharp-item-visual :item="{{ json_encode($item) }}" icon-class="fa-fw"></sharp-item-visual>
                </div>
            @elseif($nested ?? false)
                <div class="col-auto">
                    <div class="fa-fw"></div>
                </div>
            @endif
            <div class="col">
                {{ $item->label }}
            </div>
            @if($item->type === 'url')
                <div class="col-auto me-n2">
                    <i class="fas fa-external-link-alt fa-fw fs-sm" style="opacity:.5; font-size: .75rem"></i>
                </div>
            @endif
        </div>
    </sharp-nav-item>
@endif
