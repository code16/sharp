<?php

use Code16\Sharp\Utils\Entities\SharpEntity;

it('allows to set and get a config value', function () {
    sharp()->config()->setName('Test project')
        ->setCustomUrlSegment('test-sharp');

    expect(sharp()->config())->get('name')->toBe('Test project')
        ->get('custom_url_segment')->toBe('test-sharp');
});

it('allows to declare an entity without entity key', function () {
    class WithoutEntityKeyEntity extends SharpEntity {}
    sharp()->config()->declareEntity(WithoutEntityKeyEntity::class);

    expect(sharp()->config()->get('entities'))->toHaveKey('without-entity-key');
});

it('allows to declare an entity with an entity key', function () {
    class WithEntityKeyEntity extends SharpEntity
    {
        public static string $entityKey = 'my-entity';
    }

    sharp()->config()->declareEntity(WithEntityKeyEntity::class);

    expect(sharp()->config()->get('entities'))->toHaveKey('my-entity');
});
