<?php

use Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;

it('sets only default values', function () {
    $formField = SharpFormTextareaField::make('text');

    expect($formField->toArray())
        ->toEqual([
            'key' => 'text',
            'type' => 'textarea',
        ]);
});

it('allows to define row count', function () {
    $formField = SharpFormTextareaField::make('text')
        ->setRowCount(5);

    expect($formField->toArray())
        ->toHaveKey('rows', 5);
});

it('allows tot define an invalid row count', function () {
    $this->expectException(SharpFormFieldValidationException::class);

    SharpFormTextareaField::make('text')
        ->setRowCount(0)
        ->toArray();
});

it('allows to define maxLength', function () {
    $formField = SharpFormTextareaField::make('text')
        ->setMaxLength(10);

    expect($formField->toArray())
        ->toHaveKey('maxLength', 10);
});
