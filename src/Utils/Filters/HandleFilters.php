<?php

namespace Code16\Sharp\Utils\Filters;

use Code16\Sharp\EntityList\Filters\HiddenFilter;
use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Support\Collection;

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
}
