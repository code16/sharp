<?php

namespace Code16\Sharp\Tests\Unit\EntityList\Utils;

use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;

abstract class SharpEntityDefaultTestList extends SharpEntityList
{
    public function buildListDataContainers(): void
    {
    }

    public function buildListLayout(): void
    {
    }

    public function buildListConfig(): void
    {
    }

    public function getListData(EntityListQueryParams $params)
    {
        return [];
    }
}
