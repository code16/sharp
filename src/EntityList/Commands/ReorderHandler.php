<?php

namespace Code16\Sharp\EntityList\Commands;

interface ReorderHandler
{
    function reorder(array $ids);
}