<sharp-nav-item :current="{{ json_encode($isCurrent) }}" link="{{ $item->url }}">
    <div class="row d-inline-flex no-gutters flex-nowrap w-100">
        <div class="col text-truncate">
            @if($item->icon)
                <sharp-item-visual :item="{{ json_encode($item) }}" icon-class="fa-fw"></sharp-item-visual>
            @endif
            {{ $item->label }}
        </div>
        @if($item->type === 'url')
        <div class="col-auto">
            <i class="fa fa-external-link fa-fw" style="opacity:.5; line-height:inherit"></i>
        </div>
        @endif
    </div>
</sharp-nav-item>