<?php

use Code16\Sharp\Filters\GlobalRequiredFilter;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    login();

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
});

it('allows to user to update a global filter', function () {
    $this->withoutExceptionHandling();

    $this
        ->followingRedirects()
        ->post(route('code16.sharp.filters.update', ['test']), ['filterValues' => ['test' => '1']])
        ->assertOk();

    $this->assertEquals('1', sharp()->context()->globalFilterValue('test'));
});

it('sets to global filter to the default value if missing', function () {
    $this
        ->followingRedirects()
        ->post(route('code16.sharp.filters.update', ['test']), ['filterValues' => []])
        ->assertOk();

    $this->assertEquals(2, sharp()->context()->globalFilterValue('test'));
});

it('does not allow to set a global filter to an unexpected value', function () {
    $this
        ->followingRedirects()
        ->post(route('code16.sharp.filters.update', ['test']), ['filterValues' => ['test' => 4]])
        ->assertOk();

    $this->assertEquals(2, sharp()->context()->globalFilterValue('test'));
});

it('sends the current value of the global filter with every inertia request', function () {
    sharp()->config()->declareEntity(PersonEntity::class);

    $this
        ->followingRedirects()
        ->get('/sharp/s-list/person')
        ->assertInertia(fn (Assert $page) => $page
            ->has('globalFilters.config.filters._root.0', fn (Assert $filter) => $filter
                ->where('key', 'test')
                ->where('required', true)
                ->etc()
            )
            ->where('globalFilters.filterValues.current.test', 2)
            ->where('globalFilters.filterValues.default.test', 2)
        );

    $this
        ->followingRedirects()
        ->get('/sharp/3/s-list/person')
        ->assertInertia(fn (Assert $page) => $page
            ->has('globalFilters.config.filters._root.0', fn (Assert $filter) => $filter
                ->where('key', 'test')
                ->etc()
            )
            ->where('globalFilters.filterValues.current.test', 3)
            ->where('globalFilters.filterValues.default.test', 2)
        );
});
