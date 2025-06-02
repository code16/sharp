<?php

namespace App\Sharp\Utils\Filters;

use App\Models\User;
use Code16\Sharp\Filters\SelectFilter;

class AuthorFilter extends SelectFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Author');
    }

    public function values(): array
    {
        return User::whereHas('posts')
            ->orderBy('name')
            ->get()
            ->pluck('name', 'id')
            ->map(fn ($name, $id) => auth()->id() === $id ? "$name (me)" : $name)
            ->toArray();
    }
}
