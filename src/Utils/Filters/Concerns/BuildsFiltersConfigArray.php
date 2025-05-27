<?php

namespace Code16\Sharp\Utils\Filters\Concerns;

use Code16\Sharp\EntityList\Filters\HiddenFilter;
use Code16\Sharp\Utils\Filters\Filter;
use Illuminate\Support\Collection;

trait BuildsFiltersConfigArray
{
    public function getFiltersConfigArray(): ?array
    {
        return $this->getFilterHandlers()
            ->map(function (Collection $filterHandlers) {
                return $filterHandlers
                    ->filter(fn (Filter $handler) => ! $this->isHiddenFilter($handler))
                    ->map(fn (Filter $handler) => $handler->toArray())
                    ->values();
            })
            ->filter(fn (Collection $filters) => count($filters) > 0)
            ->toArray()
            ?: null;
    }

    protected function isHiddenFilter(Filter $handler): bool
    {
        return $handler instanceof HiddenFilter;
    }
}
