<?php

namespace App\Sharp\Utils\Filters;

use App\Models\Post;
use Code16\Sharp\Filters\AutocompleteRemoteMultipleFilter;

class PostFilter extends AutocompleteRemoteMultipleFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Posts');
    }

    public function values(string $query): array
    {
        return Post::query()
            ->when($query, function ($builder) use ($query) {
                $builder->where(function ($builder) use ($query) {
                    $builder->where('title->en', 'like', "%$query%")
                        ->orWhere('title->fr', 'like', "%$query%");
                });
            })
            ->orderBy('title->en')
            ->limit(20)
            ->get()
            ->mapWithKeys(fn (Post $post) => [$post->id => $post->getTranslation('title', 'en')])
            ->all();
    }

    public function valueLabelsFor(array $ids): array
    {
        return Post::query()
            ->whereKey($ids)
            ->get()
            ->mapWithKeys(fn (Post $post) => [$post->id => $post->getTranslation('title', 'en')])
            ->all();
    }
}
