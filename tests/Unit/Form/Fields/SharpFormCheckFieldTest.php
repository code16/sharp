<?php

use Code16\Sharp\Form\Fields\SharpFormCheckField;

it('sets default values are set', function () {
    $formField = SharpFormCheckField::make('check', 'text');

    expect($formField->toArray())
        ->toEqual([
            'key' => 'check', 'type' => 'check',
            'text' => 'text',
        ]);
});

it('allows to define text', function () {
    $formField = SharpFormCheckField::make('check', 'text')
        ->setText('text 2');

    expect($formField->toArray())
        ->toHaveKey('text', 'text 2');
});
