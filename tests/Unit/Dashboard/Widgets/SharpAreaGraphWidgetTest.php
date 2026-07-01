<?php

use Code16\Sharp\Dashboard\Widgets\SharpAreaGraphWidget;

it('handles area display', function () {
    $widget = SharpAreaGraphWidget::make('name');

    expect($widget->toArray()['display'])->toEqual('area');
});

it('handles default ratio', function () {
    $widget = SharpAreaGraphWidget::make('name');

    expect($widget->toArray()['ratioX'])->toEqual(16)
        ->and($widget->toArray()['ratioY'])->toEqual(9);
});

it('allows to define a specific ratio', function () {
    $widget = SharpAreaGraphWidget::make('name')
        ->setRatio('4:3');

    expect($widget->toArray()['ratioX'])->toEqual(4)
        ->and($widget->toArray()['ratioY'])->toEqual(3);
});

it('allows to define minimal attribute', function () {
    $widget = SharpAreaGraphWidget::make('name')
        ->setMinimal();

    expect($widget->toArray()['minimal'])->toBeTrue();
});

it('allows to define showLegend attribute', function () {
    $widget = SharpAreaGraphWidget::make('name')
        ->setShowLegend(false);

    expect($widget->toArray()['showLegend'])->toBeFalse();
});

it('allows to define height attribute', function () {
    $widget = SharpAreaGraphWidget::make('name')
        ->setHeight(200);

    expect($widget->toArray()['height'])->toEqual(200);
});

it('handles default curvedLines as true', function () {
    $widget = SharpAreaGraphWidget::make('name');

    expect($widget->toArray()['curved'])->toBeTrue();
});

it('allows to disable curvedLines', function () {
    $widget = SharpAreaGraphWidget::make('name')
        ->setCurvedLines(false);

    expect($widget->toArray()['curved'])->toBeFalse();
});

it('handles default gradient as false', function () {
    $widget = SharpAreaGraphWidget::make('name');

    expect($widget->toArray()['gradient'])->toBeFalse();
});

it('allows to enable gradient', function () {
    $widget = SharpAreaGraphWidget::make('name')
        ->setShowGradient();

    expect($widget->toArray()['gradient'])->toBeTrue();
});

it('handles default opacity', function () {
    $widget = SharpAreaGraphWidget::make('name');

    expect($widget->toArray()['opacity'])->toEqual(.4);
});

it('allows to define opacity', function () {
    $widget = SharpAreaGraphWidget::make('name')
        ->setOpacity(.8);

    expect($widget->toArray()['opacity'])->toEqual(.8);
});

it('handles default stacked as false', function () {
    $widget = SharpAreaGraphWidget::make('name');

    expect($widget->toArray()['stacked'])->toBeFalse();
});

it('allows to enable stacked', function () {
    $widget = SharpAreaGraphWidget::make('name')
        ->setStacked();

    expect($widget->toArray()['stacked'])->toBeTrue();
});

it('handles default showStackTotal as false', function () {
    $widget = SharpAreaGraphWidget::make('name');

    expect($widget->toArray()['showStackTotal'])->toBeFalse();
});

it('allows to enable showStackTotal', function () {
    $widget = SharpAreaGraphWidget::make('name')
        ->setShowStackTotal();

    expect($widget->toArray()['showStackTotal'])->toBeTrue();
});

it('allows to define a custom stackTotalLabel', function () {
    $widget = SharpAreaGraphWidget::make('name')
        ->setShowStackTotal(true, 'Custom total');

    expect($widget->toArray()['stackTotalLabel'])->toEqual('Custom total');
});

it('allows to define displayHorizontalAxisAsTimeline attribute', function () {
    $widget = SharpAreaGraphWidget::make('name')
        ->setDisplayHorizontalAxisAsTimeline();

    expect($widget->toArray()['displayHorizontalAxisAsTimeline'])->toBeTrue();
});

it('allows to enable horizontal axis label sampling', function () {
    $widget = SharpAreaGraphWidget::make('name')
        ->setEnableHorizontalAxisLabelSampling();

    expect($widget->toArray()['enableHorizontalAxisLabelSampling'])->toBeTrue();
});
