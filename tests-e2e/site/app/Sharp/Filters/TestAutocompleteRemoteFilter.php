<?php

namespace App\Sharp\Filters;

use Code16\Sharp\Filters\AutocompleteRemoteFilter;

class TestAutocompleteRemoteFilter extends AutocompleteRemoteFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Autocomplete remote');
    }

    public function values(string $query): array
    {
        return collect([
            1 => 'Option 1',
            2 => 'Option 2',
            3 => 'Option 3',
            4 => 'Option 4',
            5 => 'Option 5',
            6 => 'Option 6',
            7 => 'Option 7',
            8 => 'Option 8',
            9 => 'Option 9',
            10 => 'Option 10',
        ])
            ->filter(fn ($option) => str_contains($option, $query))
            ->all();
    }

    public function valueLabelFor(string $id): ?string
    {
        return "Option {$id}";
    }
}
