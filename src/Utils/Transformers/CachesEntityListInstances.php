<?php

namespace Code16\Sharp\Utils\Transformers;

interface CachesEntityListInstances
{
    /**
     * @param  array<int, mixed>  $instances
     */
    public function cacheEntityListInstances(array $instances): void;
}
