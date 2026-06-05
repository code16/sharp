<?php

use Code16\Sharp\Dashboard\Widgets\SharpGraphWidgetDataSet;

it('formats numeric values as floats', function () {
    $dataSet = SharpGraphWidgetDataSet::make(['a' => 1, 'b' => 2, 'c' => 3]);

    expect($dataSet->toArray()['data'])->toEqual([1.0, 2.0, 3.0]);
});

it('formats string numeric values as floats', function () {
    $dataSet = SharpGraphWidgetDataSet::make(['a' => '1.5', 'b' => '2.7']);

    expect($dataSet->toArray()['data'])->toEqual([1.5, 2.7]);
});

it('returns array keys as labels by default', function () {
    $dataSet = SharpGraphWidgetDataSet::make(['foo' => 10, 'bar' => 20]);

    expect($dataSet->toArray()['labels'])->toEqual(['foo', 'bar']);
});

it('accepts a collection', function () {
    $dataSet = SharpGraphWidgetDataSet::make(collect(['a' => 1, 'b' => 2]));

    expect($dataSet->toArray()['data'])->toEqual([1.0, 2.0])
        ->and($dataSet->toArray()['labels'])->toEqual(['a', 'b']);
});

it('sets a label', function () {
    $dataSet = SharpGraphWidgetDataSet::make(['a' => 1])
        ->setLabel('My Dataset');

    expect($dataSet->toArray()['label'])->toEqual('My Dataset');
});

it('does not include label key when not set', function () {
    $dataSet = SharpGraphWidgetDataSet::make(['a' => 1]);

    expect($dataSet->toArray())->not->toHaveKey('label');
});

it('sets a color', function () {
    $dataSet = SharpGraphWidgetDataSet::make(['a' => 1])
        ->setColor('#ff0000');

    expect($dataSet->toArray()['color'])->toEqual('#ff0000');
});

it('does not include color key when not set', function () {
    $dataSet = SharpGraphWidgetDataSet::make(['a' => 1]);

    expect($dataSet->toArray())->not->toHaveKey('color');
});

it('formats date labels as UTC atom strings regardless of app timezone', function (string $timezone) {
    config(['app.timezone' => $timezone]);

    $dataSet = SharpGraphWidgetDataSet::make(['2024-01-15' => 10])->withDateLabels();

    expect($dataSet->toArray()['labels'][0])->toEqual('2024-01-15T00:00:00+00:00');
})->with([
    'UTC',
    'America/New_York', // UTC-5
    'Asia/Tokyo',       // UTC+9
]);

it('returns plain keys when withDateLabels is not called', function () {
    $dataSet = SharpGraphWidgetDataSet::make([
        '2024-01-15' => 10,
        '2024-06-01' => 20,
    ]);

    expect($dataSet->toArray()['labels'])->toEqual(['2024-01-15', '2024-06-01']);
});
