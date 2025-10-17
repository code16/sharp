<?php

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Show\Fields\SharpShowDashboardField;
use Code16\Sharp\Tests\Fixtures\Entities\DashboardEntity;

beforeEach(function () {
    sharp()->config()->declareEntity(DashboardEntity::class);
});

it('allows to define a SharpShowDashboardField', function () {
    $field = SharpShowDashboardField::make('dashboardField', DashboardEntity::$entityKey);

    expect($field->toArray())
        ->key->toBe('dashboardField')
        ->type->toBe('dashboard')
        ->dashboardKey->toBe(DashboardEntity::$entityKey)
        ->hiddenCommands->toEqual([])
        ->endpointUrl->toStartWith(route('code16.sharp.api.dashboard', [DashboardEntity::$entityKey]))
        ->authorizations->toEqual(['view' => true]);
});

it('allows to define a SharpShowDashboardField with default key', function () {
    $field = SharpShowDashboardField::make(DashboardEntity::$entityKey);

    expect($field->toArray())
        ->key->toBe(DashboardEntity::$entityKey)
        ->type->toBe('dashboard')
        ->dashboardKey->toBe(DashboardEntity::$entityKey)
        ->endpointUrl->toStartWith(route('code16.sharp.api.dashboard', [DashboardEntity::$entityKey]));
});

it('allows to define a SharpShowDashboardField with the entity className', function () {
    $field = SharpShowDashboardField::make(DashboardEntity::class);

    expect($field->toArray())
        ->key->toBe(DashboardEntity::class)
        ->type->toBe('dashboard')
        ->dashboardKey->toBe(DashboardEntity::$entityKey)
        ->endpointUrl->toStartWith(route('code16.sharp.api.dashboard', [DashboardEntity::$entityKey]));
});

it('handles hideFilterWithValue', function () {
    $field = SharpShowDashboardField::make(DashboardEntity::$entityKey)
        ->hideFilterWithValue('f1', 'value1');

    expect($field->toArray()['hiddenFilters'])->toEqual([
        'f1' => 'value1',
    ]);
});

it('handles hideFilterWithValue with a callable', function () {
    $field = SharpShowDashboardField::make(DashboardEntity::$entityKey)
        ->hideFilterWithValue('f1', fn () => 'computed');

    expect($field->toArray()['hiddenFilters'])->toEqual([
        'f1' => 'computed',
    ]);
});

it('handles hideDashboardCommand', function () {
    $field = SharpShowDashboardField::make(DashboardEntity::$entityKey)
        ->hideDashboardCommand(['c1', 'c2']);

    expect($field->toArray()['hiddenCommands'])->toEqual(
        ['c1', 'c2'],
    );

    $field->hideDashboardCommand('c3');

    expect($field->toArray()['hiddenCommands'])->toEqual(
        ['c1', 'c2', 'c3']
    );
});

it('handles authorizations', function () {
    $field = SharpShowDashboardField::make(DashboardEntity::$entityKey);

    app()->bind(SharpAuthorizationManager::class, fn () => new class() extends SharpAuthorizationManager
    {
        public function __construct() {}

        public function isAllowed(string $ability, string $entityKey, ?string $instanceId = null): bool
        {
            if ($ability == 'entity' && $entityKey == DashboardEntity::$entityKey) {
                return false;
            }

            return true;
        }
    });

    expect($field->toArray()['authorizations']['view'])->toBeFalse();
});
