<?php

use Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteRemoteField;

it('sets default values for remote autocomplete', function () {
    $autocompleteField = SharpFormAutocompleteRemoteField::make('field')
        ->setRemoteEndpoint('/endpoint');

    expect($autocompleteField->toArray())
        ->toEqual([
            'key' => 'field',
            'type' => 'autocomplete',
            'mode' => 'remote',
            'remoteMethod' => 'GET',
            'remoteEndpoint' => '/endpoint',
            'itemIdAttribute' => 'id',
            'searchMinChars' => 1,
            'remoteSearchAttribute' => 'query',
            'dataWrapper' => '',
            'debounceDelay' => 300,
        ]);
});

it('allows to define remote endpoint attributes', function () {
    $autocompleteField = SharpFormAutocompleteRemoteField::make('field')
        ->setRemoteMethodPOST()
        ->setRemoteEndpoint('/another/endpoint')
        ->setRemoteSearchAttribute('attribute');

    expect($autocompleteField->toArray())
        ->toHaveKey('remoteMethod', 'POST')
        ->toHaveKey('remoteEndpoint', '/another/endpoint')
        ->toHaveKey('remoteSearchAttribute', 'attribute');
});

it('allows to define searchMinChars', function () {
    $autocompleteField = SharpFormAutocompleteRemoteField::make('field')
        ->setRemoteEndpoint('/endpoint')
        ->setSearchMinChars(3);

    expect($autocompleteField->toArray())
        ->toHaveKey('searchMinChars', 3);
});

it('allows to define debounceDelay', function () {
    $autocompleteField = SharpFormAutocompleteRemoteField::make('field')
        ->setRemoteEndpoint('/endpoint')
        ->setDebounceDelayInMilliseconds(500);

    expect($autocompleteField->toArray())
        ->toHaveKey('debounceDelay', 500);
});

it('allows to define setDataWrapper', function () {
    $autocompleteField = SharpFormAutocompleteRemoteField::make('field')
        ->setRemoteEndpoint('/endpoint')
        ->setDataWrapper('test');

    expect($autocompleteField->toArray())
        ->toHaveKey('dataWrapper', 'test');
});

it('disallows to define a remote autocomplete without remoteEndpoint', function () {
    $this->expectException(SharpFormFieldValidationException::class);

    SharpFormAutocompleteRemoteField::make('field')
        ->toArray();
});

it('allows to define linked remote endpoint with dynamic attributes', function () {
    $formField = SharpFormAutocompleteRemoteField::make('field')
        ->setDynamicRemoteEndpoint('autocomplete/{{master}}/endpoint');

    expect($formField->toArray())
        ->toHaveKey('remoteEndpoint', 'autocomplete/{{master}}/endpoint')
        ->toHaveKey('dynamicAttributes', [
            [
                'name' => 'remoteEndpoint',
                'type' => 'template',
                'default' => null,
            ],
        ]);
});

it('allows to define linked remote endpoint with default value with dynamic attributes', function () {
    $master = Str::random(4);

    $formField = SharpFormAutocompleteRemoteField::make('field')
        ->setDynamicRemoteEndpoint(
            'autocomplete/{{master}}/endpoint',
            ['master' => $master],
        );

    expect($formField->toArray())
        ->toHaveKey('remoteEndpoint', 'autocomplete/{{master}}/endpoint')
        ->toHaveKey('dynamicAttributes', [
            [
                'name' => 'remoteEndpoint',
                'type' => 'template',
                'default' => "autocomplete/$master/endpoint",
            ],
        ]);
});

it('allows to define linked remote endpoint with multiple default value with dynamic attributes', function () {
    $master = Str::random(4);
    $secondary = Str::random(4);

    $formField = SharpFormAutocompleteRemoteField::make('field')
        ->setDynamicRemoteEndpoint(
            'autocomplete/{{master}}/{{secondary}}/endpoint',
            [
                'master' => $master,
                'secondary' => $secondary,
            ],
        );

    expect($formField->toArray())
        ->toHaveKey('remoteEndpoint', 'autocomplete/{{master}}/{{secondary}}/endpoint')
        ->toHaveKey('dynamicAttributes', [
            [
                'name' => 'remoteEndpoint',
                'type' => 'template',
                'default' => "autocomplete/$master/$secondary/endpoint",
            ],
        ]);
});