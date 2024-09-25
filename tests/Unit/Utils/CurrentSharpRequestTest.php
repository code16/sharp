<?php

use Code16\Sharp\Tests\Unit\Utils\FakesCurrentSharpRequest;
use Code16\Sharp\Utils\Entities\SharpEntity;

uses(FakesCurrentSharpRequest::class);

it('allows to get form update state from request', function () {
    $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-show/person/1/s-form/child/2');

    expect(currentSharpRequest()->isForm())->toBeTrue()
        ->and(currentSharpRequest()->isUpdate())->toBeTrue()
        ->and(currentSharpRequest()->isCreation())->toBeFalse()
        ->and(currentSharpRequest()->isShow())->toBeFalse()
        ->and(currentSharpRequest()->isEntityList())->toBeFalse();
});

it('allows to get form creation state from request', function () {
    // We have to define "child" as a non-single form
    app()->bind('child_entity', fn () => new class extends SharpEntity {});
    sharp()->config()->addEntity('child', 'child_entity');

    $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-show/person/1/s-form/child');

    expect(currentSharpRequest()->isForm())->toBeTrue()
        ->and(currentSharpRequest()->isUpdate())->toBeFalse()
        ->and(currentSharpRequest()->isCreation())->toBeTrue()
        ->and(currentSharpRequest()->isShow())->toBeFalse()
        ->and(currentSharpRequest()->isEntityList())->toBeFalse();
});

it('allows to get show state from request', function () {
    $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-show/person/1');

    expect(currentSharpRequest()->isForm())->toBeFalse()
        ->and(currentSharpRequest()->isUpdate())->toBeFalse()
        ->and(currentSharpRequest()->isCreation())->toBeFalse()
        ->and(currentSharpRequest()->isShow())->toBeTrue()
        ->and(currentSharpRequest()->isEntityList())->toBeFalse();
});

it('allows to get entity list state from request', function () {
    $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person');

    expect(currentSharpRequest()->isForm())->toBeFalse()
        ->and(currentSharpRequest()->isUpdate())->toBeFalse()
        ->and(currentSharpRequest()->isCreation())->toBeFalse()
        ->and(currentSharpRequest()->isShow())->toBeFalse()
        ->and(currentSharpRequest()->isEntityList())->toBeTrue();
});

it('allows to get current breadcrumb item from request', function () {
    $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-show/person/1/s-form/child/2');

    expect(currentSharpRequest()->getCurrentBreadcrumbItem()->isForm())->toBeTrue()
        ->and(currentSharpRequest()->getCurrentBreadcrumbItem()->isSingleForm())->toBeFalse()
        ->and(currentSharpRequest()->getCurrentBreadcrumbItem()->entityKey())->toBe('child')
        ->and(currentSharpRequest()->getCurrentBreadcrumbItem()->instanceId())->toEqual(2);
});

it('allows to get previous show from request', function () {
    $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-show/person/42/s-form/child/2');

    expect(currentSharpRequest()->getPreviousShowFromBreadcrumbItems()->entityKey())->toBe('person')
        ->and(currentSharpRequest()->getPreviousShowFromBreadcrumbItems()->instanceId())->toEqual(42);
});

it('allows to get previous show of a given key from request', function () {
    $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-show/person/31/s-show/person/42/s-show/child/84/s-form/child/84');

    expect(currentSharpRequest()->getPreviousShowFromBreadcrumbItems()->entityKey())->toBe('child')
        ->and(currentSharpRequest()->getPreviousShowFromBreadcrumbItems()->instanceId())->toEqual(84)
        ->and(currentSharpRequest()->getPreviousShowFromBreadcrumbItems('person')->entityKey())->toBe('person')
        ->and(currentSharpRequest()->getPreviousShowFromBreadcrumbItems('person')->instanceId())->toEqual(42);
});

it('allows to get previous url from request', function () {
    $this->fakeCurrentSharpRequestWithUrl('/sharp/s-list/person/s-show/person/42/s-form/child/2');

    expect(currentSharpRequest()->getUrlOfPreviousBreadcrumbItem())
        ->toEqual(url('/sharp/s-list/person/s-show/person/42'));
});
