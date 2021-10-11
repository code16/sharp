<?php

namespace Code16\Sharp\Tests\Unit\EntityList\Utils;

use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;

abstract class SharpEntityDefaultTestList extends SharpEntityList
{
    function buildListFields(EntityListFieldsContainer $fieldsContainer): void {}
    function buildListLayout(EntityListFieldsLayout $fieldsLayout): void {}
    function buildListConfig(): void {}
    function getListData(): array|Arrayable { return []; }
}