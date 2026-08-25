<?php

use Code16\Sharp\Filters\AutocompleteRemoteMultipleFilter;

function makeAutocompleteRemoteMultipleFilter(array &$calls): AutocompleteRemoteMultipleFilter
{
    return new class($calls) extends AutocompleteRemoteMultipleFilter
    {
        public function __construct(private array &$calls) {}

        public function buildFilterConfig(): void
        {
            $this->configureKey('products')
                ->configureLabel('Products')
                ->configureSearchMinChars(2)
                ->configureDebounceDelay(500);
        }

        public function values(string $query): array
        {
            return [];
        }

        public function valueLabelsFor(array $ids): array
        {
            $this->calls[] = $ids;

            return collect($ids)
                ->reverse()
                ->map(fn ($id) => [
                    'id' => $id,
                    'label' => "Product {$id}",
                ])
                ->values()
                ->all();
        }
    };
}

it('serializes a remote multiple autocomplete filter without loading its values', function () {
    $calls = [];
    $filter = makeAutocompleteRemoteMultipleFilter($calls);
    $filter->buildFilterConfig();

    expect($filter->toArray())->toEqual([
        'key' => 'products',
        'label' => 'Products',
        'type' => 'autocompleteRemote',
        'master' => false,
        'required' => false,
        'multiple' => true,
        'debounceDelay' => 500,
        'searchMinChars' => 2,
    ])
        ->and($calls)->toBeEmpty();
});

it('restores all selected labels in one batch and keeps selection order', function () {
    $calls = [];
    $filter = makeAutocompleteRemoteMultipleFilter($calls);

    expect($filter->fromQueryParam('2,1,2'))->toEqual([
        ['id' => '2', 'label' => 'Product 2'],
        ['id' => '1', 'label' => 'Product 1'],
    ])
        ->and($calls)->toEqual([[
            '2',
            '1',
        ]]);
});

it('caches restored labels and only batches unresolved IDs', function () {
    $calls = [];
    $filter = makeAutocompleteRemoteMultipleFilter($calls);

    $filter->fromQueryParam('1,2');

    expect($filter->fromQueryParam('2,3'))->toEqual([
        ['id' => '2', 'label' => 'Product 2'],
        ['id' => '3', 'label' => 'Product 3'],
    ])
        ->and($calls)->toEqual([
            ['1', '2'],
            ['3'],
        ]);
});

it('serializes selected options like other multiple filters and exposes raw IDs', function () {
    $calls = [];
    $filter = makeAutocompleteRemoteMultipleFilter($calls);
    $value = [
        ['id' => 2, 'label' => 'Product 2'],
        ['id' => '1', 'label' => 'Product 1'],
        ['id' => 2, 'label' => 'Product 2'],
    ];

    expect($filter->toQueryParam($value))->toBe('2,1')
        ->and($filter->formatRawValue($value))->toBe([2, '1'])
        ->and($filter->fromQueryParam(null))->toBe([])
        ->and($filter->toQueryParam([]))->toBeNull()
        ->and($filter->defaultValue())->toBe([]);
});
