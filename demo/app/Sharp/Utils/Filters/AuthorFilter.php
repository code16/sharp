<?php

namespace App\Sharp\Utils\Filters;

use App\Models\User;
use Code16\Sharp\Filters\AutocompleteRemoteFilter;

class AuthorFilter extends AutocompleteRemoteFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Author');
    }

    public function values(string $query): array
    {
        return User::whereHas('posts')
            ->orderBy('name')
            ->when($query, function ($queryBuilder) use ($query) {
                $queryBuilder->where('name', 'like', "%$query%");
            })
            ->get()
            ->pluck('name', 'id')
            ->map(fn ($name, $id) => auth()->id() === $id ? "$name (me)" : $name)
            ->toArray();
    }

    public function valuesFor(array $ids): array
    {
        return User::whereIn('id', $ids)->get()->pluck('name', 'id')->toArray();
    }
}
