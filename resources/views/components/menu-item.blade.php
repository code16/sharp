<sharp-nav-item :current="{{ json_encode($isCurrent) }}" link="{{ $item->url }}">
    <div class="row flex-nowrap w-100 gx-2">
        @if($item->icon)
            <div class="col-auto">
                <sharp-item-visual :item="{{ json_encode($item) }}" icon-class="fa-fw"></sharp-item-visual>
            </div>
        @endif
        <div class="col">
            {{ $item->label }}
        </div>
        @if($item->type === 'url')
        <div class="col-auto">
            <i class="fas fa-external-link-alt fa-fw" style="opacity:.5; line-height:inherit"></i>
        </div>
        @endif
    </div>
</sharp-nav-item>
