<?php

namespace Code16\Sharp\Filters\Concerns;

use Code16\Sharp\Filters\FilterContainer\FilterContainer;

trait HasFilters
{
    /**
     * @internal
     */
    protected ?FilterContainer $filterContainer = null;

    /**
     * @internal
     */
    final public function filterContainer(): FilterContainer
    {
        return $this->filterContainer ??= new FilterContainer($this->getFilters());
    }

    final public function hideFilter(string $filterFullClassNameOrKey): void
    {
        $this->filterContainer()->excludeFilter($filterFullClassNameOrKey);
    }
}
