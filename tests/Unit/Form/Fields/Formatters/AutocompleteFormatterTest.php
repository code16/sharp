<?php

use Code16\Sharp\Form\Fields\Formatters\AutocompleteFormatter;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;
use Illuminate\Support\Str;

it('allows to format local value to front', function () {
    $value = Str::random();

    // Front always need an object
    $toFront = (new AutocompleteFormatter)
        ->toFront(SharpFormAutocompleteField::make('text', 'local'), $value);

    expect($toFront)->toBe(['id' => $value]);

    $toFront = (new AutocompleteFormatter)
        ->toFront(SharpFormAutocompleteField::make('text', 'local')->setItemIdAttribute('num'), $value);

    expect($toFront)->toBe(['num' => $value]);

    $toFront = (new AutocompleteFormatter)
        ->toFront(SharpFormAutocompleteField::make('text', 'local'), ['id' => $value]);

    expect($toFront)->toBe(['id' => $value]);

    $toFront = (new AutocompleteFormatter)
        ->toFront(SharpFormAutocompleteField::make('text', 'local'), (object) ['id' => $value]);

    expect($toFront)->toBe(['id' => $value]);

    $toFront = (new AutocompleteFormatter)->toFront(
        SharpFormAutocompleteField::make('text', 'local'),
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

it('allows to format remote value to front', function () {
    $value = [
        'id' => Str::random(),
        'label' => Str::random(),
    ];

    $toFront = (new AutocompleteFormatter)->toFront(
        SharpFormAutocompleteField::make('text', 'remote'),
        $value,
    );

    expect($toFront)->toEqual($value);
});

it('allows to format null value to front', function () {
    expect(
        (new AutocompleteFormatter)->toFront(
            SharpFormAutocompleteField::make('text', 'local'),
            null,
        )
    )->toBeNull();
});

it('allows to format null value from front', function () {
    expect(
        (new AutocompleteFormatter)->fromFront(
            SharpFormAutocompleteField::make('text', 'local'),
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
        (new AutocompleteFormatter)->fromFront(
            SharpFormAutocompleteField::make('text', 'local'),
            'attribute',
            $value,
        )
    )->toBe($value['id']);
});