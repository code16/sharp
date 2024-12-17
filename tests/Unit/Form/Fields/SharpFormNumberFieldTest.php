<?php

use Code16\Sharp\Form\Fields\SharpFormNumberField;

it('sets only default values', function () {
    $formField = SharpFormNumberField::make('text');

    expect($formField->toArray())
        ->toEqual([
            'key' => 'text', 'type' => 'number',
            'step' => 1, 'showControls' => false,
        ]);
});

it('allows to define min and max', function () {
    $formField = SharpFormNumberField::make('text')
        ->setMin(5)
        ->setMax(15);

    expect($formField->toArray())
        ->toHaveKey('min', 5)
        ->toHaveKey('max', 15);
});

it('allows to define a step', function () {
    $formField = SharpFormNumberField::make('text')
        ->setStep(5);

    expect($formField->toArray())
        ->toHaveKey('step', 5);
});

it('allows to define showControls', function () {
    $formField = SharpFormNumberField::make('text')
        ->setShowControls();

    expect($formField->toArray())
        ->toHaveKey('showControls', true);
});
