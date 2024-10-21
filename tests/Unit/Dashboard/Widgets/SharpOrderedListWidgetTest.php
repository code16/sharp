<?php

use Code16\Sharp\Dashboard\Widgets\SharpOrderedListWidget;

it('allows to define html attribute', function () {
    $widget = SharpOrderedListWidget::make('name')
        ->setHtml();

    expect($widget->toArray()['html'])->toBeTrue();
});
