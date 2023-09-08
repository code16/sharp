<?php

use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormSelectField;
use Code16\Sharp\Form\Fields\SharpFormTagsField;
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

it('allows to define the localized attribute for select field', function () {
    $formField = SharpFormSelectField::make('name', ['1' => 'one'])
        ->setLocalized();

    expect($formField->toArray())->toHaveKey('localized', true);
});

it('allows to define the localized attribute for autocomplete field', function () {
    $formField = SharpFormAutocompleteField::make('name', 'local')
        ->setLocalValues(['1' => 'one'])
        ->setResultItemInlineTemplate('{{id}}')
        ->setListItemInlineTemplate('{{id}}')
        ->setLocalized();

    expect($formField->toArray())->toHaveKey('localized', true);
});

it('allows to define the localized attribute for tags field', function () {
    $formField = SharpFormTagsField::make('name', ['1' => 'one'])
        ->setLocalized();

    expect($formField->toArray())->toHaveKey('localized', true);
});
