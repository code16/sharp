<?php

use Code16\Sharp\Form\Fields\SharpFormTextField;

it('allows to define inputType', function () {
    $defaultFormField = SharpFormTextField::make('name');

    $textFormField = SharpFormTextField::make('name')
        ->setInputTypeText();

    $passwordFormField = SharpFormTextField::make('name')
        ->setInputTypePassword();

    expect($defaultFormField->toArray())
        ->toHaveKey('key', 'name')
        ->toHaveKey('type', 'text')
        ->toHaveKey('inputType', 'text')
        ->and($textFormField->toArray())
        ->toHaveKey('key', 'name')
        ->toHaveKey('type', 'text')
        ->toHaveKey('inputType', 'text')
        ->and($passwordFormField->toArray())
        ->toHaveKey('key', 'name')
        ->toHaveKey('type', 'text')
        ->toHaveKey('inputType', 'password');
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
