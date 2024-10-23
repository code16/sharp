<?php

use Code16\Sharp\Dashboard\Widgets\SharpPanelWidget;
use Code16\Sharp\Utils\Links\LinkToEntityList;

it('handles template', function () {
    $widget = SharpPanelWidget::make('name')
        ->setInlineTemplate('<b>test</b>');

    expect($widget->toArray()['template'])->toEqual('<b>test</b>');
});

it('handles SharpLinkTo link', function () {
    $widget = SharpPanelWidget::make('name')
        ->setInlineTemplate('<b>test</b>')
        ->setLink(LinkToEntityList::make('entity'));

    expect($widget->toArray()['link'])->toEqual(url('sharp/s-list/entity'));
});
