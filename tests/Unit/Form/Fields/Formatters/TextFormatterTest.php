<?php

use Code16\Sharp\Form\Fields\Formatters\TextFormatter;
use Code16\Sharp\Form\Fields\SharpFormTextField;

it('allows to format value to front', function () {
    $value = Str::random();

    expect((new TextFormatter)->toFront(SharpFormTextField::make('text'), $value))
        ->toEqual($value);
});

it('allows to format value from front', function () {
    $value = Str::random();

    expect((new TextFormatter)->fromFront(SharpFormTextField::make('text'), 'attr', $value))
        ->toEqual($value);
});
