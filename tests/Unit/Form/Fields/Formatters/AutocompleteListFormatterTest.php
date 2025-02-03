<?php

use Code16\Sharp\Form\Fields\Formatters\AutocompleteListFormatter;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteListField;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteRemoteField;

it('allows to format value to front', function () {
    $formatter = new AutocompleteListFormatter();
    $field = SharpFormAutocompleteListField::make('list')
        ->setItemField(SharpFormAutocompleteRemoteField::make('item')
            ->setRemoteEndpoint('/endpoint'),
        );

    expect(
        $formatter->toFront(
            $field,
            [
                ['id' => 1, 'item' => 'A'],
                ['id' => 2, 'item' => 'B'],
            ]
        )
    )->toBe([
        ['id' => 1, 'item' => ['id' => 1, 'item' => 'A', '_html' => 1]],
        ['id' => 2, 'item' => ['id' => 2, 'item' => 'B', '_html' => 2]],
    ]);
});

it('allows to format value from front', function () {
    $formatter = new AutocompleteListFormatter();
    $field = SharpFormAutocompleteListField::make('list')
        ->setItemField(SharpFormAutocompleteRemoteField::make('item')
            ->setRemoteEndpoint('/endpoint'),
        );

    expect(
        $formatter->fromFront(
            $field,
            'attribute', [
                ['id' => 1, 'item' => 'A'],
                ['id' => 2, 'item' => 'B'],
            ]
        )
    )->toBe([
        ['id' => 'A'],
        ['id' => 'B'],
    ]);
});
