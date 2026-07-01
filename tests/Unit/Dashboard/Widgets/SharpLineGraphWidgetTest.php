<?php

use Code16\Sharp\Dashboard\Widgets\SharpLineGraphWidget;

it('handles line display', function () {
    $widget = SharpLineGraphWidget::make('name');

    expect($widget->toArray()['display'])->toEqual('line');
});

it('handles default ratio', function () {
    $widget = SharpLineGraphWidget::make('name');

    expect($widget->toArray()['ratioX'])->toEqual(16)
        ->and($widget->toArray()['ratioY'])->toEqual(9);
});

it('allows to define a specific ratio', function () {
    $widget = SharpLineGraphWidget::make('name')
        ->setRatio('4:3');

    expect($widget->toArray()['ratioX'])->toEqual(4)
        ->and($widget->toArray()['ratioY'])->toEqual(3);
});

it('allows to define minimal attribute', function () {
    $widget = SharpLineGraphWidget::make('name')
        ->setMinimal();

    expect($widget->toArray()['minimal'])->toBeTrue();
});

it('allows to define showLegend attribute', function () {
    $widget = SharpLineGraphWidget::make('name')
        ->setShowLegend(false);

    expect($widget->toArray()['showLegend'])->toBeFalse();
});

it('allows to define height attribute', function () {
    $widget = SharpLineGraphWidget::make('name')
        ->setHeight(200);

    expect($widget->toArray()['height'])->toEqual(200);
});

it('handles default curvedLines as true', function () {
    $widget = SharpLineGraphWidget::make('name');

    expect($widget->toArray()['curved'])->toBeTrue();
});

it('allows to disable curvedLines', function () {
    $widget = SharpLineGraphWidget::make('name')
        ->setCurvedLines(false);

    expect($widget->toArray()['curved'])->toBeFalse();
});

it('handles default showDots as false', function () {
    $widget = SharpLineGraphWidget::make('name');

    expect($widget->toArray()['showDots'])->toBeFalse();
});

it('allows to enable showDots', function () {
    $widget = SharpLineGraphWidget::make('name')
        ->setShowDots();

    expect($widget->toArray()['showDots'])->toBeTrue();
});

it('allows to define displayHorizontalAxisAsTimeline attribute', function () {
    $widget = SharpLineGraphWidget::make('name')
        ->setDisplayHorizontalAxisAsTimeline();

    expect($widget->toArray()['displayHorizontalAxisAsTimeline'])->toBeTrue();
});

it('allows to enable horizontal axis label sampling', function () {
    $widget = SharpLineGraphWidget::make('name')
        ->setEnableHorizontalAxisLabelSampling();

    expect($widget->toArray()['enableHorizontalAxisLabelSampling'])->toBeTrue();
});
