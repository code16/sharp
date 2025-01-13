<?php

use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Entities\SharpEntityResolver;

it('returns an entity declared in configuration', function () {
    sharp()->config()->addEntity('person', PersonEntity::class);

    expect(app(SharpEntityManager::class)->entityFor('person'))
        ->toBeInstanceOf(PersonEntity::class);
});

it('throws an exception on unknown entity', function () {
    app(SharpEntityManager::class)->entityFor('person');
})->throws(SharpInvalidEntityKeyException::class);

it('resolves entity via a custom EntityResolver', function () {
    sharp()->config()->declareEntityResolver(
        new class() implements SharpEntityResolver
        {
            public function entityClassName(string $entityKey): ?string
            {
                return $entityKey == 'person' ? PersonEntity::class : null;
            }
        }
    );

    expect(app(SharpEntityManager::class)->entityFor('person'))
        ->toBeInstanceOf(PersonEntity::class);
});
