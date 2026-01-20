<?php

use Code16\Sharp\Exceptions\SharpInvalidGlobalFilterKeyException;
use Code16\Sharp\Filters\GlobalRequiredFilter;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    login();
    sharp()->config()->declareEntity(PersonEntity::class);
});

it('does not sends filters of none configured', function () {
    $this
        ->followingRedirects()
        ->get('/sharp/s-list/person')
        ->assertInertia(fn (Assert $page) => $page
            ->where('globalFilters', null)
        );
});

it('does not sends filters of there have no values', function () {
    sharp()->config()->addGlobalFilter(
        new class() extends GlobalRequiredFilter
        {
            public function buildFilterConfig(): void
            {
                $this->configureKey('test-no-values');
            }

            public function values(): array
            {
                return [];
            }

            public function defaultValue(): mixed
            {
                return null;
            }
        }
    );

    $this
        ->followingRedirects()
        ->get('/sharp/s-list/person')
        ->assertInertia(fn (Assert $page) => $page
            ->where('globalFilters', null)
        );

    sharp()->config()->addGlobalFilter(
        new class() extends GlobalRequiredFilter
        {
            public function buildFilterConfig(): void
            {
                $this->configureKey('test');
            }

            public function values(): array
            {
                return [
                    1 => 'One',
                    2 => 'Two',
                    3 => 'Three',
                ];
            }

            public function defaultValue(): mixed
            {
                return 2;
            }
        }
    );

    $this
        ->followingRedirects()
        ->get('/sharp/3/s-list/person')
        ->assertInertia(fn (Assert $page) => $page
            ->has('globalFilters.config.filters._root', 1)
            ->has('globalFilters.config.filters._root.0', fn (Assert $filter) => $filter
                ->where('key', 'test')
                ->etc()
            )
        );
});

it('globalFilterValue() throws if global filter is not declared', function () {
    $this
        ->followingRedirects()
        ->get('/sharp/s-list/person');

    expect(fn () => sharp()->context()->globalFilterValue('test'))->toThrow(SharpInvalidGlobalFilterKeyException::class);
});

it('globalFilterValue() returns null if global filter is not authorized', function () {
    sharp()->config()->addGlobalFilter(
        new class() extends GlobalRequiredFilter
        {
            public function buildFilterConfig(): void
            {
                $this->configureKey('test');
            }

            public function values(): array
            {
                return [
                    1 => 'One',
                    2 => 'Two',
                    3 => 'Three',
                ];
            }

            public function defaultValue(): mixed
            {
                return 2;
            }

            public function authorize(): bool
            {
                return false;
            }
        }
    );

    $this->get(route('code16.sharp.list', ['globalFilter' => 'root', 'entityKey' => 'person']));

    expect(sharp()->context()->globalFilterValue('test'))->toBeNull();
});

it('globalFilterValue() returns null if global filter has no values', function () {
    sharp()->config()->addGlobalFilter(
        new class() extends GlobalRequiredFilter
        {
            public function buildFilterConfig(): void
            {
                $this->configureKey('test');
            }

            public function values(): array
            {
                return [];
            }

            public function defaultValue(): mixed
            {
                return 2;
            }
        }
    );

    $this->get(route('code16.sharp.list', ['globalFilter' => 'root', 'entityKey' => 'person']));

    expect(sharp()->context()->globalFilterValue('test'))->toBeNull();
});
