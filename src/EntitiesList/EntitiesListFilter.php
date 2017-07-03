<?php

namespace Code16\Sharp\EntitiesList;

interface EntitiesListFilter
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