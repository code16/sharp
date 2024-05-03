<?php

use Code16\Sharp\Show\Fields\SharpShowEntityListField;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;

beforeEach(function() {
    sharpConfig()->addEntity('entityKey', PersonEntity::class);
});

it('allows to define a EEL field', function () {
    $field = SharpShowEntityListField::make('entityListField', 'entityKey');

    expect($field->toArray())->toEqual([
        'key' => 'entityListField',
        'type' => 'entityList',
        'entityListKey' => 'entityKey',
        'showEntityState' => true,
        'showCreateButton' => true,
        'showReorderButton' => true,
        'showSearchField' => true,
        'emptyVisible' => false,
        'showCount' => false,
        'hiddenCommands' => ['entity' => [], 'instance' => []],
        'endpointUrl' => route('code16.sharp.api.list', ['entityKey']),
    ]);
});

it('allows to define EEL field with default key', function () {
    sharpConfig()->addEntity('instances', PersonEntity::class);
    $field = SharpShowEntityListField::make('instances');

    expect($field->toArray())->toEqual([
        'key' => 'instances',
        'type' => 'entityList',
        'entityListKey' => 'instances',
        'showEntityState' => true,
        'showCreateButton' => true,
        'showReorderButton' => true,
        'showSearchField' => true,
        'emptyVisible' => false,
        'showCount' => false,
        'hiddenCommands' => ['entity' => [], 'instance' => []],
        'endpointUrl' => route('code16.sharp.api.list', ['instances']),
    ]);
});

it('handles hideFilterWithValue', function () {
    $field = SharpShowEntityListField::make('entityListField', 'entityKey')
        ->hideFilterWithValue('f1', 'value1');

    expect($field->toArray()['hiddenFilters'])->toEqual([
        'f1' => 'value1',
    ]);
});

it('handles hideFilterWithValue with a callable', function () {
    $field = SharpShowEntityListField::make('entityListField', 'entityKey')
        ->hideFilterWithValue('f1', fn () => 'computed');

    expect($field->toArray()['hiddenFilters'])->toEqual([
        'f1' => 'computed',
    ]);
});

it('handles showEntityState', function () {
    $field = SharpShowEntityListField::make('entityListField', 'entityKey')
        ->showEntityState(false);

    expect($field->toArray()['showEntityState'])->toBeFalse();
});

it('handles showReorderButton', function () {
    $field = SharpShowEntityListField::make('entityListField', 'entityKey')
        ->showReorderButton(false);

    expect($field->toArray()['showReorderButton'])->toBeFalse();
});

it('handles showCreateButton', function () {
    $field = SharpShowEntityListField::make('entityListField', 'entityKey')
        ->showCreateButton(false);

    expect($field->toArray()['showCreateButton'])->toBeFalse();
});

it('handles showSearchField', function () {
    $field = SharpShowEntityListField::make('entityListField', 'entityKey')
        ->showSearchField(false);

    expect($field->toArray()['showSearchField'])->toBeFalse();
});

it('handles showCount', function () {
    $field = SharpShowEntityListField::make('entityListField', 'entityKey')
        ->showCount();

    expect($field->toArray()['showCount'])->toBeTrue();
});

it('handles hideEntityCommands', function () {
    $field = SharpShowEntityListField::make('entityListField', 'entityKey')
        ->hideEntityCommand(['c1', 'c2']);

    expect($field->toArray()['hiddenCommands']['entity'])->toEqual(
        ['c1', 'c2'],
    );

    $field->hideEntityCommand('c3');

    expect($field->toArray()['hiddenCommands']['entity'])->toEqual(
        ['c1', 'c2', 'c3']
    );
});

it('handles hideInstanceCommands', function () {
    $field = SharpShowEntityListField::make('entityListField', 'entityKey')
        ->hideInstanceCommand(['c1', 'c2']);

    expect($field->toArray()['hiddenCommands']['instance'])->toEqual(
        ['c1', 'c2'],
    );

    $field->hideInstanceCommand('c3');

    expect($field->toArray()['hiddenCommands']['instance'])->toEqual(
        ['c1', 'c2', 'c3'],
    );
});
