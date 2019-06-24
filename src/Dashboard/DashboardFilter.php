<?php

namespace Code16\Sharp\Dashboard;

use Code16\Sharp\Utils\Filters\ListFilter;
use Code16\Sharp\Utils\Filters\ListRangeFilter;
use Code16\Sharp\Utils\Filters\ListMultipleFilter;
use Code16\Sharp\Utils\Filters\ListRequiredFilter;

interface DashboardFilter extends ListFilter
{
}

interface DashboardRangeFilter extends ListRangeFilter
{
}

interface DashboardMultipleFilter extends ListMultipleFilter
{
}

interface DashboardRequiredFilter extends ListRequiredFilter
{
}