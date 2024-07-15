<?php

use Code16\Sharp\Form\Fields\SharpFormTagsField;

it('sets only default values', function () {
    $options = [
        '1' => 'Elem 1',
        '2' => 'Elem 2',
    ];

    $formField = fakeTagsField($options);

    expect($formField->toArray())
        ->toEqual([
            'key' => 'field',
            'type' => 'tags',
            'options' => [
                ['id' => '1', 'label' => 'Elem 1'],
                ['id' => '2', 'label' => 'Elem 2'],
            ],
            'creatable' => false,
            'createText' => 'Create',
        ]);
});

it('allows to define creatable', function () {
    $formField = fakeTagsField()
        ->setCreatable(true);

    expect($formField->toArray())
        ->toHaveKey('creatable', true);
});

it('allows to define createText', function () {
    $formField = fakeTagsField()
        ->setCreateText('A');

    expect($formField->toArray())
        ->toHaveKey('createText', 'A');
});

it('allows to define maxTagsCount', function () {
    $formField = fakeTagsField()
        ->setMaxTagCount(2);

    expect($formField->toArray())
        ->toHaveKey('maxTagCount', 2);
});

function fakeTagsField(?array $options = null): SharpFormTagsField
{
    return SharpFormTagsField::make('field', $options ?: [
        '1' => 'Elem 1',
        '2' => 'Elem 2',
    ]);
}
