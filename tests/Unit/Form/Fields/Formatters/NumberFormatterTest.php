<?php

use Code16\Sharp\Form\Fields\Formatters\NumberFormatter;
use Code16\Sharp\Form\Fields\SharpFormNumberField;

it('allows to format value to front', function () {
    $formatter = new NumberFormatter;
    $field = SharpFormNumberField::make('number');

    expect($formatter->toFront($field, 10))->toEqual(10)
        ->and($formatter->toFront($field, '10'))->toEqual(10)
        ->and($formatter->toFront($field, ''))->toEqual(0)
        ->and($formatter->toFront($field, 'abc'))->toEqual(0);
});

it('allows to format value from front', function () {
    $formatter = new NumberFormatter;
    $field = SharpFormNumberField::make('number');

    expect($formatter->fromFront($field, 'attr', 10))->toEqual(10)
        ->and($formatter->fromFront($field, 'attr', '10'))->toEqual(10)
        ->and($formatter->fromFront($field, 'attr', ''))->toEqual(0)
        ->and($formatter->fromFront($field, 'attr', 'abc'))->toEqual(0);
});
