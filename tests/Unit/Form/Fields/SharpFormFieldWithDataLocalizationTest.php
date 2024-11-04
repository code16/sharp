<?php

use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Fields\SharpFormTextField;

it('allows to define the localized attribute for text field', function () {
    $formField = SharpFormTextField::make('name')
        ->setLocalized(false);

    expect($formField->toArray())->not->toHaveKey('localized');

    $formField->setLocalized();

    expect($formField->toArray())->toHaveKey('localized', true);
});

it('allows to define the localized attribute for textarea field', function () {
    $formField = SharpFormTextareaField::make('name')
        ->setLocalized();

    expect($formField->toArray())->toHaveKey('localized', true);
});

it('allows to define the localized attribute for editor field', function () {
    $formField = SharpFormEditorField::make('name')
        ->setLocalized();

    expect($formField->toArray())->toHaveKey('localized', true);
});
