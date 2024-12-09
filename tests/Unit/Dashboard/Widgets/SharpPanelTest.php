<?php

use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;
use Code16\Sharp\Utils\Links\LinkToEntityList;

it('handles SharpLinkTo link', function () {
    $widget = SharpPanelWidget::make('name')
        ->setTemplate('<b>test</b>')
        ->setLink(LinkToEntityList::make('entity'));

    expect($widget->toArray()['link'])->toEqual(url('sharp/s-list/entity'));
});
