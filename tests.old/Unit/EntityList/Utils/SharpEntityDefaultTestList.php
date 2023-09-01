<?php

namespace Code16\Sharp\Tests\Unit\EntityList\Utils;

use Code16\Sharp\EntityList\Fields\EntityListFieldsContainer;
use Code16\Sharp\EntityList\Fields\EntityListFieldsLayout;
use Code16\Sharp\EntityList\SharpEntityList;
use Illuminate\Contracts\Support\Arrayable;

abstract class SharpEntityDefaultTestList extends SharpEntityList
{
    public function buildListFields(EntityListFieldsContainer $fieldsContainer): void
    {
    }

    public function buildListLayout(EntityListFieldsLayout $fieldsLayout): void
    {
    }

    public function buildListConfig(): void
    {
    }

    public function getListData(): array|Arrayable
    {
        return [];
    }
}
