<?php

use Code16\Sharp\Filters\AutocompleteRemoteFilter;
use Code16\Sharp\Tests\Fixtures\Entities\DashboardEntity;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\Sharp\PersonList;
use Code16\Sharp\Tests\Fixtures\Sharp\TestDashboard;

beforeEach(function () {
    sharp()->config()->declareEntity(PersonEntity::class);
    sharp()->config()->declareEntity(DashboardEntity::class);
    login();
});

it('allows to call an autocomplete remote filter endpoint for entity list', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function getFilters(): ?array
        {
            return [
                new class() extends AutocompleteRemoteFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('test')
                            ->configureLabel('Test filter')
                            ->configureSearchMinChars(2)
                            ->configureDebounceDelay(500);
                    }

                    public function values(string $query): array
                    {
                        expect($query)->toBe('my search');

                        return [
                            ['id' => 1, 'label' => 'Item A'],
                            ['id' => 2, 'label' => 'Item B'],
                        ];
                    }

                    public function valueLabelFor(string $id): string
                    {
                        return "Item $id";
                    }
                },
            ];
        }
    });

    $this
        ->postJson(route('code16.sharp.api.filters.autocomplete.index', [
            'entityKey' => 'person',
            'filterKey' => 'test',
        ]), [
            'query' => 'my search',
        ])
        ->assertOk()
        ->assertJson([
            'data' => [
                ['id' => 1, 'label' => 'Item A'],
                ['id' => 2, 'label' => 'Item B'],
            ],
        ]);
});

it('allows to call an autocomplete remote filter endpoint with empty query', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function getFilters(): ?array
        {
            return [
                new class() extends AutocompleteRemoteFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('test')
                            ->configureLabel('Test filter')
                            ->configureSearchMinChars(0);
                    }

                    public function values(string $query): array
                    {
                        expect($query)->toBe('');

                        return [
                            ['id' => 1, 'label' => 'Item A'],
                            ['id' => 2, 'label' => 'Item B'],
                        ];
                    }

                    public function valueLabelFor(string $id): string
                    {
                        return "Item $id";
                    }
                },
            ];
        }
    });

    $this
        ->postJson(route('code16.sharp.api.filters.autocomplete.index', [
            'entityKey' => 'person',
            'filterKey' => 'test',
        ]), [
            'query' => '',
        ])
        ->assertOk()
        ->assertJson([
            'data' => [
                ['id' => 1, 'label' => 'Item A'],
                ['id' => 2, 'label' => 'Item B'],
            ],
        ]);
});

it('allows to call an autocomplete remote filter endpoint for dashboard', function () {
    fakeShowFor('dashboard', new class() extends TestDashboard
    {
        public function getFilters(): ?array
        {
            return [
                new class() extends AutocompleteRemoteFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('test')
                            ->configureLabel('Test filter')
                            ->configureSearchMinChars(2)
                            ->configureDebounceDelay(500);
                    }

                    public function values(string $query): array
                    {
                        expect($query)->toBe('my search');

                        return [
                            ['id' => 1, 'label' => 'Item A'],
                            ['id' => 2, 'label' => 'Item B'],
                        ];
                    }

                    public function valueLabelFor(string $id): string
                    {
                        return "Item $id";
                    }
                },
            ];
        }
    });

    $this
        ->postJson(route('code16.sharp.api.filters.autocomplete.index', [
            'entityKey' => 'dashboard',
            'filterKey' => 'test',
        ]), [
            'query' => 'my search',
        ])
        ->assertOk()
        ->assertJson([
            'data' => [
                ['id' => 1, 'label' => 'Item A'],
                ['id' => 2, 'label' => 'Item B'],
            ],
        ]);
});
