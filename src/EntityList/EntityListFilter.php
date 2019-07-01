<?php

namespace Code16\Sharp\EntityList;

use Code16\Sharp\Utils\Filters\SelectFilter;
use Code16\Sharp\Utils\Filters\SelectRequiredFilter;
use Code16\Sharp\Utils\Filters\SelectMultipleFilter;
use Code16\Sharp\Utils\Filters\DateRangeFilter;
use Code16\Sharp\Utils\Filters\DateRangeRequiredFilter;

/**
 * @deprecated use EntityListSelectFilter instead
 */
interface EntityListFilter extends SelectFilter
{
}

interface EntityListSelectFilter extends SelectFilter
{
}

/**
 * @deprecated use EntityListSelectRequiredFilter instead
 */
interface EntityListRequiredFilter extends SelectRequiredFilter
{
}

interface EntityListSelectRequiredFilter extends SelectRequiredFilter
{
}

/**
 * @deprecated use EntityListSelectMultipleFilter instead
 */
interface EntityListMultipleFilter extends SelectMultipleFilter
{
}

interface EntityListSelectMultipleFilter extends SelectMultipleFilter
{
}

interface EntityListDateRangeFilter extends DateRangeFilter
{
}

interface EntityListDateRangeRequiredFilter extends DateRangeRequiredFilter
{
}