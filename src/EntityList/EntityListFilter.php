<?php

namespace Code16\Sharp\EntityList;

interface EntityListFilter
{
    /**
     * @return bool
     */
    public function multiple();

    /**
     * @return array
     */
    public function values();
}