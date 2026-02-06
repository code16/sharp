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

it('allows to discover entities automatically', function () {
    sharp()->config()->discoverEntities([
        __DIR__.'/../../Fixtures/Entities',
    ]);

    expect(sharp()->config()->get('entities'))
        ->toHaveKey('dashboard')
        ->toHaveKey('person')
        ->toHaveKey('single-person');
});

it('allows to configure breadcrumb options', function () {
    expect(sharp()->config()->get('breadcrumb.labels.lazy_loading'))->toBeFalse()
        ->and(sharp()->config()->get('breadcrumb.labels.cache'))->toBeTrue()
        ->and(sharp()->config()->get('breadcrumb.labels.cache_duration'))->toBe(30);

    sharp()->config()->enableBreadcrumbLabelsLazyLoading();
    sharp()->config()->configureBreadcrumbLabelsCache(false);

    expect(sharp()->config()->get('breadcrumb.labels.lazy_loading'))->toBeTrue()
        ->and(sharp()->config()->get('breadcrumb.labels.cache'))->toBeFalse();

    sharp()->config()->configureBreadcrumbLabelsCache(duration: 40);

    expect(sharp()->config()->get('breadcrumb.labels.cache_duration'))->toBe(40);
});
