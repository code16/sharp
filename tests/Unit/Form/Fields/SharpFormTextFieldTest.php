<?php

use Code16\Sharp\Form\Fields\SharpFormTextField;

it('allows to define inputType', function () {
    expect(SharpFormTextField::make('name')->toArray())->toMatchArray([
        'type' => 'text',
        'inputType' => 'text',
    ]);
    expect(SharpFormTextField::make('name')->setInputTypeText()->toArray())->toMatchArray([
        'type' => 'text',
        'inputType' => 'text',
    ]);
    expect(SharpFormTextField::make('name')->setInputTypePassword()->toArray())->toMatchArray([
        'type' => 'text',
        'inputType' => 'password',
    ]);
    expect(SharpFormTextField::make('name')->setInputTypeEmail()->toArray())->toMatchArray([
        'type' => 'text',
        'inputType' => 'email',
    ]);
    expect(SharpFormTextField::make('name')->setInputTypeTel()->toArray())->toMatchArray([
        'type' => 'text',
        'inputType' => 'tel',
    ]);
    expect(SharpFormTextField::make('name')->setInputTypeUrl()->toArray())->toMatchArray([
        'type' => 'text',
        'inputType' => 'url',
    ]);
});

it('allows to define placeholder', function () {
    $formField = SharpFormTextField::make('name')
        ->setPlaceholder('my placeholder');

    expect($formField->toArray())
        ->toHaveKey('placeholder', 'my placeholder');
});

it('allows to define maxLength', function () {
    $formField = SharpFormTextField::make('text')
        ->setMaxLength(10);

    expect($formField->toArray())
        ->toHaveKey('maxLength', 10);
});
