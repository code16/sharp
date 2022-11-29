@php
/**
 * @var \Code16\Sharp\View\Components\Menu\MenuSection $self
 */
@endphp

<sharp-nav-section
    label="{{ $item->getLabel() }}"
    :collapsible="{{ json_encode($item->isCollapsible()) }}"
    @if($self->hasCurrentItem())
        opened
    @endif
>
    @foreach($self->getItems() as $childItem)
        <x-sharp::menu.menu-item
            :item="$childItem"
            :current-entity-key="$currentEntityKey"
            nested
        />
    @endforeach
</sharp-nav-section>
