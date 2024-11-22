<?php

namespace Code16\Sharp\Utils\Filters;

trait HandleFilters
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

    /**
     * @internal
     */
    final public function hideFilter(string $filterFullClassNameOrKey): void
    {
        $this->filterContainer()->excludeFilter($filterFullClassNameOrKey);
    }
}
