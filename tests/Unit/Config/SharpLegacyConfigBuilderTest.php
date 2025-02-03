<?php

use Code16\Sharp\Config\SharpConfigBuilder;
use Code16\Sharp\Config\SharpLegacyConfigBuilder;

beforeEach(function () {
    // Force legacy config handling
    app()->bind(SharpConfigBuilder::class, SharpLegacyConfigBuilder::class);
});

it('allows to set and get a config value in legacy form', function () {
    config()->set('sharp', [
        'name' => 'Test project',
        'custom_url_segment' => 'test-sharp',
    ]);

    expect(sharp()->config()->get('name'))->toBe('Test project')
        ->and(sharp()->config()->get('custom_url_segment'))->toBe('test-sharp');
});

it('allows to set and get an entity resolver in legacy form', function () {
    config()->set('sharp', [
        'entities' => 'SomeEntityResolverClass',
    ]);

    expect(sharp()->config()->get('entity_resolver'))->toBe('SomeEntityResolverClass');
});
