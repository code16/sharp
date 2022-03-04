<?php

namespace Code16\Sharp\EntityList\Commands;

interface ReorderHandler
{
    public function reorder(array $ids): void;
}
