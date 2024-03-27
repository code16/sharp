<?php

use Code16\Sharp\Exceptions\Form\SharpFormFieldValidationException;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteField;

it('sets default values', function () {
    $localValues = [
        1 => 'bob',
    ];

    $defaultFormField = buildDefaultLocalAutocomplete($localValues);

    expect($defaultFormField->toArray())
        ->toEqual([
            'key' => 'field', 'type' => 'autocomplete',
            'mode' => 'local', 'searchKeys' => ['value'],
            'remoteMethod' => 'GET', 'itemIdAttribute' => 'id',
            'listItemTemplate' => 'LIT-content',
            'resultItemTemplate' => 'RIT-content',
            'searchMinChars' => 1, 'localValues' => [
                ['id' => 1, 'label' => 'bob'],
            ],
            'remoteSearchAttribute' => 'query',
            'dataWrapper' => '',
            'debounceDelay' => 300,
        ]);
});

it('allows to define remote attributes', function () {
    $formField = SharpFormAutocompleteField::make('field', 'remote')
        ->setListItemTemplatePath('LIT.vue')
        ->setResultItemTemplatePath('RIT.vue')
        ->setRemoteMethodPOST()
        ->setRemoteEndpoint('endpoint')
        ->setRemoteSearchAttribute('attribute');

    expect($formField->toArray())
        ->toHaveKey('remoteMethod', 'POST')
        ->toHaveKey('remoteEndpoint', 'endpoint')
        ->toHaveKey('remoteSearchAttribute', 'attribute');
});

it('allows to define localValues as a id label array', function () {
    $formField = buildDefaultLocalAutocomplete([
        ['id' => 1, 'label' => 'Elem 1'],
        ['id' => 2, 'label' => 'Elem 2'],
    ]);

    expect($formField->toArray()['localValues'])
        ->toEqual([
            ['id' => 1, 'label' => 'Elem 1'],
            ['id' => 2, 'label' => 'Elem 2'],
        ]);
});

it('allows to define_localValues_as_an_object_array', function () {
    $formField = buildDefaultLocalAutocomplete([
        (object) ['id' => 1, 'label' => 'Elem 1'],
        (object) ['id' => 2, 'label' => 'Elem 2'],
    ]);

    expect($formField->toArray()['localValues'])
        ->toEqual([
            (object) ['id' => 1, 'label' => 'Elem 1'],
            (object) ['id' => 2, 'label' => 'Elem 2'],
        ]);
});

it('allows to define searchMinChars', function () {
    $formField = buildDefaultLocalAutocomplete()
        ->setSearchMinChars(3);

    expect($formField->toArray())
        ->toHaveKey('searchMinChars', 3);
});

it('allows to define debounceDelay', function () {
    $formField = buildDefaultLocalAutocomplete()
        ->setDebounceDelayInMilliseconds(500);

    expect($formField->toArray())
        ->toHaveKey('debounceDelay', 500);
});

it('allows to define setDataWrapper', function () {
    $formField = buildDefaultLocalAutocomplete()
        ->setDataWrapper('test');

    expect($formField->toArray())
        ->toHaveKey('dataWrapper', 'test');
});

it('allows to define inline templates', function () {
    $formField = buildDefaultLocalAutocomplete()
        ->setListItemInlineTemplate('<strong>LIT</strong>')
        ->setResultItemInlineTemplate('<strong>RIT</strong>');

    expect($formField->toArray())
        ->toHaveKey('listItemTemplate', '<strong>LIT</strong>')
        ->toHaveKey('resultItemTemplate', '<strong>RIT</strong>');
});

it('allows to define templateData', function () {
    $formField = buildDefaultLocalAutocomplete()
        ->setAdditionalTemplateData([
            'lang' => ['fr', 'de'],
        ]);

    expect($formField->toArray())
        ->toHaveKey('templateData', [
            'lang' => ['fr', 'de'],
        ]);
});

it('disallows to define a remote autocomplete without remoteEndpoint', function () {
    $this->expectException(SharpFormFieldValidationException::class);

    SharpFormAutocompleteField::make('field', 'remote')
        ->setListItemTemplatePath('LIT.vue')
        ->setResultItemTemplatePath('RIT.vue')
        ->toArray();
});

it('allows to define linked localValues with dynamic attributes', function () {
    $formField = buildDefaultLocalAutocomplete([
        'A' => [
            'A1' => 'test A1',
            'A2' => 'test A2',
        ],
        'B' => [
            'B1' => 'test B1',
            'B2' => 'test B2',
        ],
    ])->setLocalValuesLinkedTo('master');

    expect($formField->toArray()['localValues'])
        ->toEqual([
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

it('allows to define linked localValues with dynamic attributes and localization', function () {
    $formField = buildDefaultLocalAutocomplete([
        'A' => [
            'A1' => ['fr' => 'test A1 fr', 'en' => 'test A1 en'],
            'A2' => ['fr' => 'test A2 fr', 'en' => 'test A2 en'],
        ],
        'B' => [
            'B1' => ['fr' => 'test B1 fr', 'en' => 'test B1 en'],
            'B2' => ['fr' => 'test B2 fr', 'en' => 'test B2 en'],
        ],
    ])->setLocalValuesLinkedTo('master')->setLocalized();

    expect($formField->toArray()['localValues'])
        ->toEqual([
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

it('allows to define linked localValues with dynamic attributes on multiple master fields', function () {
    $formField = buildDefaultLocalAutocomplete([
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
    ])->setLocalValuesLinkedTo('master', 'master2');

    expect($formField->toArray()['localValues'])
        ->toEqual([
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

it('allows to define linked remote endpoint with dynamic attributes', function () {
    $formField = buildDefaultDynamicRemoteAutocomplete(
        'autocomplete/{{master}}/endpoint',
    );

    expect($formField->toArray())
        ->toHaveKey('remoteEndpoint', 'autocomplete/{{master}}/endpoint')
        ->toHaveKey('dynamicAttributes', [
            [
                'name' => 'remoteEndpoint',
                'type' => 'template',
            ],
        ]);
});

it('allows to define linked remote endpoint with default value with dynamic attributes', function () {
    $master = Str::random(4);

    $formField = buildDefaultDynamicRemoteAutocomplete(
        'autocomplete/{{master}}/endpoint', [
            'master' => $master,
        ],
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

    $formField = buildDefaultDynamicRemoteAutocomplete(
        'autocomplete/{{master}}/{{secondary}}/endpoint', [
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

function buildDefaultLocalAutocomplete(?array $localValues = null): SharpFormAutocompleteField
{
    createFakeAutocompleteFieldTemplates();

    return SharpFormAutocompleteField::make('field', 'local')
        ->setListItemTemplatePath('LIT.vue')
        ->setResultItemTemplatePath('RIT.vue')
        ->setLocalValues($localValues ?: [
            1 => 'bob',
        ]);
}

function buildDefaultDynamicRemoteAutocomplete(string $remoteEndpoint, array $defaultValues = []): SharpFormAutocompleteField
{
    createFakeAutocompleteFieldTemplates();

    return SharpFormAutocompleteField::make('field', 'remote')
        ->setListItemTemplatePath('LIT.vue')
        ->setResultItemTemplatePath('RIT.vue')
        ->setDynamicRemoteEndpoint($remoteEndpoint, $defaultValues);
}

function createFakeAutocompleteFieldTemplates()
{
    @unlink(resource_path('views/LIT.vue'));
    @unlink(resource_path('views/RIT.vue'));
    file_put_contents(resource_path('views/LIT.vue'), 'LIT-content');
    file_put_contents(resource_path('views/RIT.vue'), 'RIT-content');
}
