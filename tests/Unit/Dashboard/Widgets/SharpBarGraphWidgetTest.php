<?php

use Code16\Sharp\Dashboard\Widgets\SharpBarGraphWidget;

it('handles bar display', function () {
    $widget = SharpBarGraphWidget::make('name');

    expect($widget->toArray()['display'])->toEqual('bar');
});

it('handles default ratio', function () {
    $widget = SharpBarGraphWidget::make('name');

    expect($widget->toArray()['ratioX'])->toEqual(16)
        ->and($widget->toArray()['ratioY'])->toEqual(9);
});

it('allows to define a specific ratio', function () {
    $widget = SharpBarGraphWidget::make('name')
        ->setRatio('2:3');

    expect($widget->toArray()['ratioX'])->toEqual(2)
        ->and($widget->toArray()['ratioY'])->toEqual(3);
});

it('allows to define minimal attribute', function () {
    $widget = SharpBarGraphWidget::make('name')
        ->setMinimal();

    expect($widget->toArray()['minimal'])->toBeTrue();
});

it('allows to define showLegend attribute', function () {
    $widget = SharpBarGraphWidget::make('name')
        ->setShowLegend(false);

    expect($widget->toArray()['showLegend'])->toBeFalse();
});

it('allows to define height attribute', function () {
    $widget = SharpBarGraphWidget::make('name')
        ->setHeight(150);

    expect($widget->toArray()['height'])->toEqual(150);
});

it('allows to define displayHorizontalAxisAsTimeline attribute', function () {
    $widget = SharpBarGraphWidget::make('name')
        ->setDisplayHorizontalAxisAsTimeline();

    expect($widget->toArray()['dateLabels'])->toBeTrue();
});

it('allows to define horizontal option attribute', function () {
    $widget = SharpBarGraphWidget::make('name')
        ->setHorizontal();

    expect($widget->toArray()['options']['horizontal'])->toBeTrue();
});
