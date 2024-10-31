<?php

use Code16\Sharp\Form\Fields\SharpFormAutocompleteLocalField;

it('sets default values for local autocomplete', function () {
    $autocompleteField = SharpFormAutocompleteLocalField::make('field')
        ->setLocalValues([1 => 'bob']);

    expect($autocompleteField->toArray())
        ->toEqual([
            'key' => 'field',
            'type' => 'autocomplete',
            'mode' => 'local',
            'searchKeys' => ['value'],
            'itemIdAttribute' => 'id',
            'localValues' => [
                [
                    'id' => 1,
                    'label' => 'bob',
                    '_html' => 'bob',
                ],
            ],
        ]);
});

it('allows to define inline templates', function () {
    $autocompleteField = SharpFormAutocompleteLocalField::make('field')
        ->setListItemTemplate('list: {{ $label }}')
        ->setResultItemTemplate('result: {{ $label }}')
        ->setLocalValues([1 => 'bob']);

    expect($autocompleteField->toArray()['localValues'])
        ->toEqual([
            [
                'id' => 1,
                'label' => 'bob',
                '_html' => 'list: bob',
                '_htmlResult' => 'result: bob',
            ],
        ]);
});

it('allows to rely on blade file for templates', function () {
    @unlink(resource_path('views/LIT.blade.php'));
    @unlink(resource_path('views/RIT.blade.php'));
    file_put_contents(resource_path('views/LIT.blade.php'), 'List: {{ $label }}');
    file_put_contents(resource_path('views/RIT.blade.php'), 'Result: {{ $label }}');

    $autocompleteField = SharpFormAutocompleteLocalField::make('field')
        ->setListItemTemplate(view('LIT'))
        ->setResultItemTemplate(view('RIT'))
        ->setLocalValues([1 => 'bob']);

    expect($autocompleteField->toArray()['localValues'])
        ->toEqual([
            [
                'id' => 1,
                'label' => 'bob',
                '_html' => 'List: bob',
                '_htmlResult' => 'Result: bob',
            ],
        ]);
});

it('allows to define localValues as a [id, label] array', function () {
    $autocompleteField = SharpFormAutocompleteLocalField::make('field')
        ->setLocalValues([
            ['id' => 1, 'label' => 'bob'],
            ['id' => 2, 'label' => 'mary'],
        ]);

    expect($autocompleteField->toArray()['localValues'])
        ->toEqual([
            ['id' => 1, 'label' => 'bob', '_html' => 'bob'],
            ['id' => 2, 'label' => 'mary', '_html' => 'mary'],
        ]);
});

it('allows to define localValues as attributes array', function () {
    $autocompleteField = SharpFormAutocompleteLocalField::make('field')
        ->setListItemTemplate('Choose {{ $name }}, he is {{ $age }}')
        ->setResultItemTemplate('{{ $name }} was chosen')
        ->setLocalValues([['id' => 1, 'name' => 'bob', 'age' => 42]]);

    expect($autocompleteField->toArray()['localValues'])
        ->toEqual([
            [
                'id' => 1,
                'name' => 'bob',
                'age' => 42,
                '_html' => 'Choose bob, he is 42',
                '_htmlResult' => 'bob was chosen',
            ],
        ]);
});

it('allows to define linked localValues with dynamic attributes', function () {
    $autocompleteField = SharpFormAutocompleteLocalField::make('field')
        ->setLocalValues([
            'A' => [
                'A1' => 'test A1',
                'A2' => 'test A2',
            ],
            'B' => [
                'B1' => 'test B1',
                'B2' => 'test B2',
            ],
        ])
        ->setLocalValuesLinkedTo('master');

    expect($autocompleteField->toArray()['localValues'])
        ->toEqual([
            'A' => [
                ['id' => 'A1', 'label' => 'test A1', '_html' => 'test A1'],
                ['id' => 'A2', 'label' => 'test A2', '_html' => 'test A2'],
            ],
            'B' => [
                ['id' => 'B1', 'label' => 'test B1', '_html' => 'test B1'],
                ['id' => 'B2', 'label' => 'test B2', '_html' => 'test B2'],
            ],
        ]);
});

it('allows to define linked localValues with dynamic attributes and localization', function () {
    $autocompleteField = SharpFormAutocompleteLocalField::make('field')
        ->setLocalValues([
            'A' => [
                'A1' => ['fr' => 'test A1 fr', 'en' => 'test A1 en'],
                'A2' => ['fr' => 'test A2 fr', 'en' => 'test A2 en'],
            ],
            'B' => [
                'B1' => ['fr' => 'test B1 fr', 'en' => 'test B1 en'],
                'B2' => ['fr' => 'test B2 fr', 'en' => 'test B2 en'],
            ],
        ])
        ->setLocalValuesLinkedTo('master')
        ->setLocalized();

    expect($autocompleteField->toArray()['localValues'])
        ->toEqual([
            'A' => [
                [
                    'id' => 'A1',
                    'label' => ['fr' => 'test A1 fr', 'en' => 'test A1 en'],
                    '_html' => ['fr' => 'test A1 fr', 'en' => 'test A1 en']
                ],
                [
                    'id' => 'A2',
                    'label' => ['fr' => 'test A2 fr', 'en' => 'test A2 en'],
                    '_html' => ['fr' => 'test A2 fr', 'en' => 'test A2 en']
                ],
            ],
            'B' => [
                [
                    'id' => 'B1',
                    'label' => ['fr' => 'test B1 fr', 'en' => 'test B1 en'],
                    '_html' => ['fr' => 'test B1 fr', 'en' => 'test B1 en']
                ],
                [
                    'id' => 'B2',
                    'label' => ['fr' => 'test B2 fr', 'en' => 'test B2 en'],
                    '_html' => ['fr' => 'test B2 fr', 'en' => 'test B2 en']
                ],
            ],
        ]);
});

it('allows to define linked localValues with dynamic attributes on multiple master fields', function () {
    $autocompleteField = SharpFormAutocompleteLocalField::make('field')
        ->setLocalValues([
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
        ])
        ->setLocalValuesLinkedTo('master', 'master2');

    expect($autocompleteField->toArray()['localValues'])
        ->toEqual([
            'A' => [
                'A1' => [
                    [
                        'id' => 'A11',
                        'label' => 'test A11',
                        '_html' => 'test A11',
                    ],
                    [
                        'id' => 'A12',
                        'label' => 'test A12',
                        '_html' => 'test A12',
                    ],
                ],
                'A2' => [
                    [
                        'id' => 'A21',
                        'label' => 'test A21',
                        '_html' => 'test A21',
                    ],
                    [
                        'id' => 'A22',
                        'label' => 'test A22',
                        '_html' => 'test A22',
                    ],
                ],
            ],
            'B' => [
                'B1' => [
                    [
                        'id' => 'B11',
                        'label' => 'test B11',
                        '_html' => 'test B11',
                    ],
                    [
                        'id' => 'B12',
                        'label' => 'test B12',
                        '_html' => 'test B12',
                    ],
                ],
            ],
        ]);
});