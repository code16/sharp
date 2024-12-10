<?php

use Code16\Sharp\Form\Fields\Formatters\TextareaFormatter;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Illuminate\Support\Str;

it('allows to format value to front', function () {
    $value = Str::random();

    expect((new TextareaFormatter())->toFront(SharpFormTextareaField::make('text'), $value))
        ->toEqual($value);
});

it('allows to format value from front', function () {
    $value = Str::random();

    expect((new TextareaFormatter())->fromFront(SharpFormTextareaField::make('text'), 'attr', $value))
        ->toEqual($value);
});
