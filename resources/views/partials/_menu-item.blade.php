<sharp-nav-item :current="{{ json_encode($isCurrent) }}" link="{{ $item->url }}">
    <span>
        @if($item->icon)
            <sharp-item-visual :item="{{ json_encode($item) }}" icon-class="fa-fw"></sharp-item-visual>
        @endif
        {{ $item->label }}
        @if($item->type === 'url')
            <i class="fa fa-external-link fa-fw" style="float:right; opacity:.5; line-height:inherit"></i>
        @endif
    </span>
</sharp-nav-item>