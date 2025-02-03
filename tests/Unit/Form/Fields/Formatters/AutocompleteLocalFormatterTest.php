<?php

use Code16\Sharp\Form\Fields\Formatters\AutocompleteLocalFormatter;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteLocalField;
use Illuminate\Support\Str;

it('allows to format local value to front', function () {
    $value = Str::random();

    // Front always need an object
    $toFront = (new AutocompleteLocalFormatter())
        ->toFront(SharpFormAutocompleteLocalField::make('text'), $value);

    expect($toFront)->toBe(['id' => $value]);

    $toFront = (new AutocompleteLocalFormatter())
        ->toFront(SharpFormAutocompleteLocalField::make('text')->setItemIdAttribute('num'), $value);

    expect($toFront)->toBe(['num' => $value]);

    $toFront = (new AutocompleteLocalFormatter())
        ->toFront(SharpFormAutocompleteLocalField::make('text'), ['id' => $value]);

    expect($toFront)->toBe(['id' => $value]);

    $toFront = (new AutocompleteLocalFormatter())
        ->toFront(SharpFormAutocompleteLocalField::make('text'), (object) ['id' => $value]);

    expect($toFront)->toBe(['id' => $value]);

    $toFront = (new AutocompleteLocalFormatter())->toFront(
        SharpFormAutocompleteLocalField::make('text'),
        new class($value)
        {
            public function __construct(private $value) {}

            public function toArray()
            {
                return ['id' => $this->value];
            }
        },
    );

    expect($toFront)->toEqual(['id' => $value]);
});

it('allows to format null value to front', function () {
    expect(
        (new AutocompleteLocalFormatter())->toFront(
            SharpFormAutocompleteLocalField::make('text'),
            null,
        )
    )->toBeNull();
});

it('allows to format null value from front', function () {
    expect(
        (new AutocompleteLocalFormatter())->fromFront(
            SharpFormAutocompleteLocalField::make('text'),
            'attribute',
            null,
        )
    )->toBeNull();
});

it('allows to format local value from front', function () {
    // Front always send an object
    $value = [
        'id' => Str::random(),
        'label' => Str::random(),
    ];

    // Back always need an id
    expect(
        (new AutocompleteLocalFormatter())->fromFront(
            SharpFormAutocompleteLocalField::make('text'),
            'attribute',
            $value,
        )
    )->toBe($value['id']);
});
