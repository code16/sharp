<?php

namespace Code16\Sharp\Dashboard;

use Code16\Sharp\Utils\Filters\SelectFilter;
use Code16\Sharp\Utils\Filters\SelectMultipleFilter;
use Code16\Sharp\Utils\Filters\SelectRequiredFilter;
use Code16\Sharp\Utils\Filters\DateRangeFilter;
use Code16\Sharp\Utils\Filters\DateRangeRequiredFilter;

/**
 * @deprecated use DashboardSelectFilter instead
 */
interface DashboardFilter extends SelectFilter
{
}

