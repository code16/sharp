<?php

use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Utils\Filters\GlobalRequiredFilter;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    login();

    config()->set('sharp.global_filters', fn () => [
        new class extends GlobalRequiredFilter
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
        },
    ]);
});

it('allows to user to update a global filter', function () {
    $this->withoutExceptionHandling();

    $this
        ->post(route('code16.sharp.filters.update', 'test'), ['value' => 1])
        ->assertRedirect(route('code16.sharp.home'));

    $this->assertEquals(1, currentSharpRequest()->globalFilterFor('test'));
});

it('sets to global filter to the default value if missing', function () {
    $this
        ->post(route('code16.sharp.filters.update', 'test'))
        ->assertRedirect(route('code16.sharp.home'));

    $this->assertEquals(2, currentSharpRequest()->globalFilterFor('test'));
});

it('does not allow to set a global filter to an unexpected value', function () {
    $this
        ->post(route('code16.sharp.filters.update', 'test'), ['value' => 4])
        ->assertRedirect(route('code16.sharp.home'));

    $this->assertEquals(2, currentSharpRequest()->globalFilterFor('test'));
});

it('the current value of the global filter is sent with every inertia request', function () {
    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );

    $this
        ->get('/sharp/s-list/person')
        ->assertInertia(fn (Assert $page) => $page
            ->has('globalFilters.filters._root.0', fn (Assert $filter) => $filter
                ->where('key', 'test')
                ->where('required', true)
                ->where('default', 2)
                ->etc()
            )
        );

    $this
        ->post(route('code16.sharp.filters.update', 'test'), ['value' => 3]);

    $this
        ->get('/sharp/s-list/person')
        ->assertInertia(fn (Assert $page) => $page
            ->has('globalFilters.filters._root.0', fn (Assert $filter) => $filter
                ->where('key', 'test')
                ->where('default', 3)
                ->etc()
            )
        );
});
