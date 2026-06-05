<?php

use Code16\Sharp\Dashboard\Widgets\SharpFigureWidget;

it('handles figure type', function () {
    $widget = SharpFigureWidget::make('name');

    expect($widget->toArray()['type'])->toEqual('figure');
});

it('returns key in array', function () {
    $widget = SharpFigureWidget::make('name');

    expect($widget->toArray()['key'])->toEqual('name');
});
