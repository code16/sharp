<?php

namespace Code16\Sharp\Tests\Unit\EntityList\Utils;

use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;

abstract class SharpEntityDefaultTestList extends SharpEntityList
{
    function buildListDataContainers(): void {}
    function buildListLayout(): void {}
    function buildListConfig(): void {}
    function getListData(EntityListQueryParams $params) { return []; }
}