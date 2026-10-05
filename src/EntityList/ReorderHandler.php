<?php

namespace Code16\Sharp\EntityList;

interface ReorderHandler
{
    public function reorder(array $ids): void;
}
