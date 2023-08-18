<?php

namespace Code16\Sharp\Utils;

use Illuminate\Support\Collection;

class StringUtil
{
    public function explodeSearchTerms(
        string $search,
        bool $isLike = true,
        bool $handleStar = true,
        string $noStarTermPrefix = '%',
        string $noStarTermSuffix = '%'
    ): Collection {
        return collect(explode(' ', $search))
            ->map(fn ($term) => trim($term))
            ->filter()
            ->when($isLike, fn ($terms) => $terms
                ->map(function ($term) use ($handleStar, $noStarTermPrefix, $noStarTermSuffix) {
                    return $handleStar && str_contains($term, '*')
                        ? str_replace('*', '%', $term)
                        : $noStarTermPrefix.$term.$noStarTermSuffix;
                })
            )
            ->values();
    }
}
