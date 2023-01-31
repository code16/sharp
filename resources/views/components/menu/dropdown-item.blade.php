@props([
    'item',
])

@php
/**
 * @var \Code16\Sharp\Utils\Menu\SharpMenuItem $item
 */
@endphp

@if(!$item->isSeparator())
    <a class="dropdown-item text-wrap"
        href="{{ $item->getUrl() }}"
        @if($item->isExternalLink())
            target="_blank"
        @endif
        style="width: max-content; max-width: 13rem; min-width: 100%"
    >
        <span class="row flex-nowrap gx-2">
            @if($item->getIcon())
                <div class="col-auto">
                    <i class="fa fa-fw {{ $item->getIcon() }}" style="font-size: 1em"></i>
                </div>
            @else
                <div class="col-auto">
                    <div class="fa-fw"></div>
                </div>
            @endif
            <span class="col" style="min-width: 0">
                <span style="overflow-wrap: break-word">
                    {{ $item->getLabel() }}
                </span>
            </span>
            @if($item->isExternalLink())
                <span class="col-auto me-n2">
                    <i class="fas fa-external-link-alt fa-fw fs-sm" style="opacity:.5; font-size: .625rem; vertical-align: .125em"></i>
                </span>
            @endif
        </span>
    </a>
@endif
