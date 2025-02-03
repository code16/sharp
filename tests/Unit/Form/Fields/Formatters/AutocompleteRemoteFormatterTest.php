<?php

use Code16\Sharp\Form\Fields\Formatters\AutocompleteRemoteFormatter;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteRemoteField;
use Illuminate\Support\Str;

it('allows to format remote value to front', function () {
    $value = [
        'id' => 1,
        'name' => 'Bob',
        'age' => 42,
    ];

    $toFront = (new AutocompleteRemoteFormatter())
        ->toFront(
            SharpFormAutocompleteRemoteField::make('text')
                ->setListItemTemplate('{{ $name }}, {{ $age }}')
                ->setResultItemTemplate('{{ $name }}, {{ $age }}'),
            $value,
        );

    expect($toFront)->toEqual([
        'id' => 1,
        'name' => 'Bob',
        'age' => 42,
        '_html' => 'Bob, 42',
        '_htmlResult' => 'Bob, 42',
    ]);
});

it('allows to format remote value from front', function () {
    // Front always send an object
    $value = [
        'id' => Str::random(),
        'label' => Str::random(),
    ];

    // Back always need an id
    expect(
        (new AutocompleteRemoteFormatter())->fromFront(
            SharpFormAutocompleteRemoteField::make('text'),
            'attribute',
            $value,
        )
    )->toBe($value['id']);
});
