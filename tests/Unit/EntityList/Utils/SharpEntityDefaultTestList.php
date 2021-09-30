<?php

namespace Code16\Sharp\Tests\Unit\EntityList\Utils;

use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;

abstract class SharpEntityDefaultTestList extends SharpEntityList
{
    function buildListDataContainers(): void {}
    function buildListLayout(): void {}
    function buildListConfig(): void {}
    function getListData(): array|Arrayable { return []; }
}