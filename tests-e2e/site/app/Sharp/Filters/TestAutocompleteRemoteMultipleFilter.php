<?php

namespace App\Sharp\Filters;

use Code16\Sharp\Filters\AutocompleteRemoteMultipleFilter;

class TestAutocompleteRemoteMultipleFilter extends AutocompleteRemoteMultipleFilter
{
    public function buildFilterConfig(): void
    {
        $this
            ->configureLabel('Autocomplete remote multiple')
            ->configureSearchMinChars(2);
    }

    public function values(string $query): array
    {
        return collect($this->options())
            ->filter(fn ($option) => str_contains($option, $query))
            ->all();
    }

    public function valueLabelsFor(array $ids): array
    {
        return collect($this->options())
            ->only($ids)
            ->all();
    }

    private function options(): array
    {
        return collect(range(1, 10))
            ->mapWithKeys(fn ($id) => [$id => "Option {$id}"])
            ->all();
    }
}
