<?php

use Code16\Sharp\Filters\AutocompleteRemoteFilter;
use Code16\Sharp\Filters\AutocompleteRemoteMultipleFilter;
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
            'filterHandlerKey' => 'test',
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
            'filterHandlerKey' => 'test',
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

it('uses the autocomplete endpoint for a multiple remote filter', function () {
    fakeListFor('person', new class() extends PersonList
    {
        public function getFilters(): ?array
        {
            return [
                new class() extends AutocompleteRemoteMultipleFilter
                {
                    public function buildFilterConfig(): void
                    {
                        $this->configureKey('products');
                    }

                    public function values(string $query): array
                    {
                        expect($query)->toBe('product');

                        return [
                            1 => 'Product A',
                            2 => 'Product B',
                        ];
                    }

                    public function valueLabelsFor(array $ids): array
                    {
                        return [];
                    }
                },
            ];
        }
    });

    $this
        ->postJson(route('code16.sharp.api.filters.autocomplete.index', [
            'entityKey' => 'person',
            'filterHandlerKey' => 'products',
        ]), [
            'query' => 'product',
        ])
        ->assertOk()
        ->assertJson([
            'data' => [
                ['id' => 1, 'label' => 'Product A'],
                ['id' => 2, 'label' => 'Product B'],
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
            'filterHandlerKey' => 'test',
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
