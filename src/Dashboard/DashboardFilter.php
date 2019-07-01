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

interface DashboardSelectFilter extends SelectFilter
{
}

/**
 * @deprecated use DashboardSelectMultipleFilter instead
 */
interface DashboardMultipleFilter extends SelectMultipleFilter
{
}

interface DashboardSelectMultipleFilter extends SelectMultipleFilter
{
}

/**
 * @deprecated use DashboardSelectRequiredFilter instead
 */
interface DashboardRequiredFilter extends SelectRequiredFilter
{
}

interface DashboardSelectRequiredFilter extends SelectRequiredFilter
{
}

interface DashboardDateRangeFilter extends DateRangeFilter
{
}

interface DashboardDateRangeRequiredFilter extends DateRangeRequiredFilter
{
}