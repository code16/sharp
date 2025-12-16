<?php

use Code16\Sharp\Filters\GlobalRequiredFilter;
use Code16\Sharp\Tests\Fixtures\Entities\DashboardEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Entities\SinglePersonEntity;

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);
    login();
});

it('redirects to route with default filterKey when missing', function () {
    sharp()->config()
        ->declareEntity(DashboardEntity::class)
        ->declareEntity(SinglePersonEntity::class);

    $this->get('/sharp/s-list/person')
        ->assertRedirect('/sharp/root/s-list/person');

    $this->get('/sharp/s-list/person/s-show/person/1')
        ->assertRedirect('/sharp/root/s-list/person/s-show/person/1');

    $this->get('/sharp/s-list/person/s-show/person/1/s-form/person/1')
        ->assertRedirect('/sharp/root/s-list/person/s-show/person/1/s-form/person/1');

    $this->get('/sharp/s-list/person/s-show/person/1/s-show/person/2/s-form/person/2')
        ->assertRedirect('/sharp/root/s-list/person/s-show/person/1/s-show/person/2/s-form/person/2');

    $this->get('/sharp/s-dashboard/dashboard')
        ->assertRedirect('/sharp/root/s-dashboard/dashboard');

    $this->get('/sharp/s-show/single-person')
        ->assertRedirect('/sharp/root/s-show/single-person');
});

it('redirects to route with correct filterKey when missing and global filters are defined', function () {
    fakeGlobalFilter();

    $this->get('/sharp/s-list/person')
        ->assertRedirect('/sharp/two/s-list/person');
});

it('sets the current filterKey according to the URL', function () {
    fakeGlobalFilter();

    $this->get('/sharp/one/s-list/person/s-show/person/1')
        ->assertOk();

    expect(sharp()->context()->globalFilterValue('test'))->toEqual('one');
});

it('redirects to the homepage if an invalid filterKey is set in the URL', function () {
    fakeGlobalFilter();

    $this->get('/sharp/five/s-list/person/s-show/person/1')
        ->assertRedirect(route('code16.sharp.home', ['filterKey' => 'two']));

    expect(sharp()->context()->globalFilterValue('test'))->toEqual('two');
});

it('redirects to route with correct filterKeys when missing and multiple global filters are defined', function () {
    fakeGlobalFilter('test1');
    fakeGlobalFilter('test2');

    $this->get('/sharp/s-list/person')
        ->assertRedirect('/sharp/two~two/s-list/person');
});

it('sets the current multiple filterKeys according to the URL', function () {
    fakeGlobalFilter('test1');
    fakeGlobalFilter('test2');

    $this->get('/sharp/one~two/s-list/person/s-show/person/1')
        ->assertOk();

    expect(sharp()->context()->globalFilterValue('test1'))->toEqual('one');
    expect(sharp()->context()->globalFilterValue('test2'))->toEqual('two');
});

function fakeGlobalFilter(string $key = 'test'): void
{
    sharp()->config()->addGlobalFilter(
        new class($key) extends GlobalRequiredFilter
        {
            public function __construct(private string $key) {}

            public function buildFilterConfig(): void
            {
                $this->configureKey($this->key);
            }

            public function values(): array
            {
                return [
                    'one' => 'Company One',
                    'two' => 'Company Two',
                    'three' => 'Company Three',
                ];
            }

            public function defaultValue(): mixed
            {
                return 'two';
            }
        }
    );
}
