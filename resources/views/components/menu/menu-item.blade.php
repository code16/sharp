@props([
    'item',
    'currentEntityKey',
])

@php
    /**
     * @var \Code16\Sharp\Utils\Menu\SharpMenuItem $item
     */
@endphp

@if($item->isSeparator())
    <div {{ $attributes->class(['SharpLeftNav__separator', 'SharpLeftNav__separator--labelled' => $item->getLabel()]) }}>
        @if($item->getLabel())
            <span>
                {{ $item->getLabel() }}
            </span>
        @endif
    </div>
@else
    <sharp-nav-item
            href="{{ $item->getUrl() }}"
            @if($item->isExternalLink())
                target="_blank"
            @endif
            @if($item->isEntity() && $item->getEntityKey() === $currentEntityKey)
                current
            @endif
    >
        <div class="row flex-nowrap gx-3">
            @if($item->getIcon())
                <div class="col-auto">
                    <sharp-item-visual :item="{{ json_encode(['icon' => $item->getIcon()]) }}" icon-class="fa-fw"></sharp-item-visual>
                </div>
            @elseif($nested ?? false)
                <div class="col-auto">
                    <div class="fa-fw"></div>
                </div>
            @endif
            <div class="col">
                {{ $item->getLabel() }}
            </div>
            @if($item->isExternalLink())
                <div class="col-auto me-n2">
                    <i class="fas fa-external-link-alt fa-fw fs-sm" style="opacity:.5; font-size: .75rem"></i>
                </div>
            @endif
        </div>
    </sharp-nav-item>
@endif
