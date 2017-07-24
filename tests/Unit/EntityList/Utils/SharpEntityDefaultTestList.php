<?php

namespace Code16\Sharp\Tests\Unit\EntityList\Utils;

use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;

abstract class SharpEntityDefaultTestList extends SharpEntityList
{
    function buildListDataContainers() {}
    function buildListLayout() {}
    function buildListConfig() {}
    function getListData(EntityListQueryParams $params) { return []; }
}