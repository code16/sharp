<?php

use Code16\Sharp\Config\SharpConfigBuilder;
use Code16\Sharp\Filters\SelectFilter;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\SinglePersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Tests\Unit\Utils\FakesBreadcrumb;

uses(FakesBreadcrumb::class);

it('allows to get form update state from request', function () {
    $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/1/s-form/child/2');

    expect(sharp()->context())
        ->isForm()->toBeTrue()
        ->isUpdate()->toBeTrue()
        ->isCreation()->toBeFalse()
        ->isShow()->toBeFalse()
        ->isEntityList()->toBeFalse();
});

it('allows to get form creation state from request', function () {
    sharp()->config()->declareEntity(PersonEntity::class);

    $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/1/s-form/person');

    expect(sharp()->context())
        ->isForm()->toBeTrue()
        ->isUpdate()->toBeFalse()
        ->isCreation()->toBeTrue()
        ->isShow()->toBeFalse()
        ->isEntityList()->toBeFalse();
});

it('allows to get show state from request', function () {
    $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/1');

    expect(sharp()->context())
        ->isForm()->toBeFalse()
        ->isUpdate()->toBeFalse()
        ->isCreation()->toBeFalse()
        ->isShow()->toBeTrue()
        ->isEntityList()->toBeFalse();
});

it('allows to get entity list state from request', function () {
    $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person');

    expect(sharp()->context())
        ->isForm()->toBeFalse()
        ->isUpdate()->toBeFalse()
        ->isCreation()->toBeFalse()
        ->isShow()->toBeFalse()
        ->isEntityList()->toBeTrue();
});

it('allows to get current breadcrumb item from request', function () {
    $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/1/s-form/child/2');

    expect(sharp()->context()->breadcrumb()->currentSegment())
        ->isForm()->toBeTrue()
        ->isSingleForm()->toBeFalse()
        ->entityKey()->toBe('child')
        ->instanceId()->toEqual(2);
});

it('allows to get previous show from request', function () {
    $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/42/s-form/child/2');

    expect(sharp()->context()->breadcrumb()->previousShowSegment())
        ->entityKey()->toBe('person')
        ->instanceId()->toEqual(42);
});

it('allows to get previous show of a given key from request', function () {
    $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/31/s-show/person/42/s-show/child/84/s-form/child/84');

    expect(sharp()->context()->breadcrumb())
        ->previousShowSegment()->entityKey()->toBe('child')
        ->previousShowSegment()->instanceId()->toEqual(84)
        ->previousShowSegment('person')->entityKey()->toBe('person')
        ->previousShowSegment('person')->instanceId()->toEqual(42);
});

it('allows to get previous show of a given entity class name from request', function () {
    app(SharpConfigBuilder::class)->declareEntity(PersonEntity::class);
    $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/31/s-show/person/42/s-show/child/84/s-form/child/84');

    expect(sharp()->context()->breadcrumb())
        ->previousShowSegment()->entityKey()->toBe('child')
        ->previousShowSegment()->instanceId()->toEqual(84)
        ->previousShowSegment(PersonEntity::class)->entityKey()->toBe('person')
        ->previousShowSegment(PersonEntity::class)->instanceId()->toEqual(42);
});

it('allows to check entity of a segment', function () {
    app(SharpConfigBuilder::class)->declareEntity(PersonEntity::class);
    app(SharpConfigBuilder::class)->declareEntity(SinglePersonEntity::class);
    $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/1/s-form/person/1');

    expect(sharp()->context()->breadcrumb()->currentSegment()->entityIs(PersonEntity::class))->toBeTrue();
    expect(sharp()->context()->breadcrumb()->currentSegment()->entityIs('person'))->toBeTrue();
    expect(sharp()->context()->breadcrumb()->currentSegment()->entityIs(SinglePersonEntity::class))->toBeFalse();
    expect(sharp()->context()->breadcrumb()->currentSegment()->entityIs('child'))->toBeFalse();
    expect(sharp()->context()->breadcrumb()->previousShowSegment()->entityIs(PersonEntity::class))->toBeTrue();
    expect(sharp()->context()->breadcrumb()->previousShowSegment()->entityIs(SinglePersonEntity::class))->toBeFalse();
    expect(sharp()->context()->breadcrumb()->previousShowSegment()->entityIs('child'))->toBeFalse();
});

it('allows to get previous url from request', function () {
    $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/42/s-form/child/2');

    expect(sharp()->context()->breadcrumb()->getPreviousSegmentUrl())
        ->toEqual(url('/sharp/root/s-list/person/s-show/person/42'));
});

it('allow to retrieve retained filters value in the context', function () {
    sharp()->config()->declareEntity(PersonEntity::class);
    login();

    fakeListFor('person', new class() extends PersonList
    {
        protected function getFilters(): ?array
        {
            return [
                new class() extends SelectFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('job')
                            ->configureRetainInSession();
                    }

                    public function values(): array
                    {
                        return [
                            'physicist' => 'Physicist',
                            'physician' => 'Physician',
                        ];
                    }
                },
            ];
        }
    });

    expect(sharp()->context()->retainedFilterValue('job'))->toBeNull();

    $this
        ->withoutExceptionHandling()
        ->post(
            route('code16.sharp.list.filters.store', [
                'entityKey' => 'person',
            ]), [
                'filterValues' => [
                    'job' => 'physicist',
                ],
            ]);

    expect(sharp()->context()->retainedFilterValue('job'))->toEqual('physicist');
});
