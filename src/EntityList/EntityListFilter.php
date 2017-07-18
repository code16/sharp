<?php

namespace Code16\Sharp\EntityList;

interface EntityListFilter
{
    /**
     * @return array
     */
    public function values();
}

interface EntityListMultipleFilter extends EntityListFilter
{
}

interface EntityListRequiredFilter extends EntityListFilter
{
    /**
     * @return string|int
     */
    public function defaultValue();
}