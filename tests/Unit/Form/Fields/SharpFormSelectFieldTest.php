<?php

use Code16\Sharp\Form\Fields\SharpFormSelectField;

it('sets only default values', function () {
    $options = [
        '1' => 'Elem 1',
        '2' => 'Elem 2',
    ];

    $formField = fakeSelectField($options);

    expect($formField->toArray())
        ->toEqual([
            'key' => 'field', 'type' => 'select',
            'options' => [
                ['id' => '1', 'label' => 'Elem 1'],
                ['id' => '2', 'label' => 'Elem 2'],
            ],
            'multiple' => false, 'clearable' => false, 'inline' => false,
            'showSelectAll' => false, 'display' => 'list',
        ]);
});

it('we can define multiple', function () {
    $formField = fakeSelectField()
        ->setMultiple(true);

    expect($formField->toArray())
        ->toHaveKey('multiple', true);
});

it('we can define inline', function () {
    $formField = fakeSelectField()
        ->setInline();

    expect($formField->toArray())
        ->toHaveKey('inline', true);
});

it('we can define maxSelected', function () {
    $formField = fakeSelectField()
        ->setMaxSelected(12);

    expect($formField->toArray())
        ->toHaveKey('maxSelected', 12);
});

it('we can define clearable', function () {
    $formField = fakeSelectField()
        ->setClearable(true);

    expect($formField->toArray())
        ->toHaveKey('clearable', true);
});

it('we can define display as list', function () {
    $formField = fakeSelectField()
        ->setDisplayAsList();

    expect($formField->toArray())
        ->toHaveKey('display', 'list');
});

it('we can define display as dropdown', function () {
    $formField = fakeSelectField()
        ->setDisplayAsDropdown();

    expect($formField->toArray())
        ->toHaveKey('display', 'dropdown');
});

it('we can define showSelectAll', function () {
    $formField = fakeSelectField()
        ->allowSelectAll();

    expect($formField->toArray())
        ->toHaveKey('showSelectAll', true);
});

it('we can define options as a id label array', function () {
    $formField = fakeSelectField([
        ['id' => 1, 'label' => 'Elem 1'],
        ['id' => 2, 'label' => 'Elem 2'],
    ]);

    expect($formField->toArray())
        ->toHaveKey('options', [
            ['id' => 1, 'label' => 'Elem 1'],
            ['id' => 2, 'label' => 'Elem 2'],
        ]);
});

it('we can define options as a custom array', function () {
    $formField = fakeSelectField([
        ['key' => 'Elem-1'],
        ['key' => 'Elem-2'],
    ])->setIdAttribute('key');

    expect($formField->toArray())
        ->toHaveKey('options', [
            ['key' => 'Elem-1'],
            ['key' => 'Elem-2'],
        ]);
});

it('we can define localized options', function () {
    $formField = fakeSelectField([
        '1' => ['en' => 'Option one', 'fr' => 'Option un'],
        '2' => ['en' => 'Option two', 'fr' => 'Option deux'],
    ])->setLocalized();

    expect($formField->toArray())
        ->toHaveKey('options', [
            ['id' => '1', 'label' => ['en' => 'Option one', 'fr' => 'Option un']],
            ['id' => '2', 'label' => ['en' => 'Option two', 'fr' => 'Option deux']],
        ]);
});

it('we can define localized options with id label array', function () {
    $formField = fakeSelectField([
        ['id' => '1', 'label' => ['en' => 'Option one', 'fr' => 'Option un']],
        ['id' => '2', 'label' => ['en' => 'Option two', 'fr' => 'Option deux']],
    ])->setLocalized();

    expect($formField->toArray())
        ->toHaveKey('options', [
            ['id' => '1', 'label' => ['en' => 'Option one', 'fr' => 'Option un']],
            ['id' => '2', 'label' => ['en' => 'Option two', 'fr' => 'Option deux']],
        ]);
});

it('we can define linked options with dynamic attributes', function () {
    $formField = fakeSelectField([
        'A' => [
            'A1' => 'test A1',
            'A2' => 'test A2',
        ],
        'B' => [
            'B1' => 'test B1',
            'B2' => 'test B2',
        ],
    ])->setOptionsLinkedTo('master');

    expect($formField->toArray())
        ->toHaveKey('options', [
            'A' => [
                ['id' => 'A1', 'label' => 'test A1'],
                ['id' => 'A2', 'label' => 'test A2'],
            ],
            'B' => [
                ['id' => 'B1', 'label' => 'test B1'],
                ['id' => 'B2', 'label' => 'test B2'],
            ],
        ]);
});

it('we can define linked options with dynamic attributes and localization', function () {
    $formField = fakeSelectField([
        'A' => [
            'A1' => ['fr' => 'test A1 fr', 'en' => 'test A1 en'],
            'A2' => ['fr' => 'test A2 fr', 'en' => 'test A2 en'],
        ],
        'B' => [
            'B1' => ['fr' => 'test B1 fr', 'en' => 'test B1 en'],
            'B2' => ['fr' => 'test B2 fr', 'en' => 'test B2 en'],
        ],
    ])->setOptionsLinkedTo('master')->setLocalized();

    expect($formField->toArray())
        ->toHaveKey('options', [
            'A' => [
                ['id' => 'A1', 'label' => ['fr' => 'test A1 fr', 'en' => 'test A1 en']],
                ['id' => 'A2', 'label' => ['fr' => 'test A2 fr', 'en' => 'test A2 en']],
            ],
            'B' => [
                ['id' => 'B1', 'label' => ['fr' => 'test B1 fr', 'en' => 'test B1 en']],
                ['id' => 'B2', 'label' => ['fr' => 'test B2 fr', 'en' => 'test B2 en']],
            ],
        ]);
});

it('we can define linked options with dynamic attributes on multiple master fields', function () {
    $formField = fakeSelectField([
        'A' => [
            'A1' => [
                'A11' => 'test A11',
                'A12' => 'test A12',
            ],
            'A2' => [
                'A21' => 'test A21',
                'A22' => 'test A22',
            ],
        ],
        'B' => [
            'B1' => [
                'B11' => 'test B11',
                'B12' => 'test B12',
            ],
        ],
    ])->setOptionsLinkedTo('master', 'master2');

    expect($formField->toArray())
        ->toHaveKey('options', [
            'A' => [
                'A1' => [
                    ['id' => 'A11', 'label' => 'test A11'],
                    ['id' => 'A12', 'label' => 'test A12'],
                ],
                'A2' => [
                    ['id' => 'A21', 'label' => 'test A21'],
                    ['id' => 'A22', 'label' => 'test A22'],
                ],
            ],
            'B' => [
                'B1' => [
                    ['id' => 'B11', 'label' => 'test B11'],
                    ['id' => 'B12', 'label' => 'test B12'],
                ],
            ],
        ]);
});

function fakeSelectField(?array $options = null): SharpFormSelectField
{
    return SharpFormSelectField::make('field', $options ?: [
        '1' => 'Elem 1',
        '2' => 'Elem 2',
    ]);
}
